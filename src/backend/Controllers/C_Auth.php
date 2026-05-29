<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para autenticación SSO heredada de la Intranet.
 */

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../Models/M_Usuario.php';

class C_Auth {
	private $modelo_usuario;

	public function __construct() {
		$this->modelo_usuario = new M_Usuario();
	}

	/**
	 * Endpoint /api/auth/sso
	 * Recibe el token JWT de la Intranet, valida la firma, registra/sincroniza al usuario
	 * localmente y emite el JWT interno de la aplicación.
	 * 
	 * @param string $token Token provisto por la Intranet.
	 * @return array
	 */
	public function sso($token = null) {
		if (!$token) {
			http_response_code(400);
			return ['error' => 'Token de la Intranet requerido'];
		}

		$intranet_secret = $_ENV['INTRANET_JWT_SECRET'] ?? 'super_secret_key_12345678901234567890_para_pruebas';

		try {
			// Tolerancia de 60 s para desfases de reloj entre el servidor de la Intranet y este servidor
			JWT::$leeway = 60;

			// Validar firma del token de la intranet
			$decoded = JWT::decode($token, new Key($intranet_secret, 'HS256'));
			$datos_payload = (array) $decoded->data;
		} catch (Exception $e) {
			$log_msg = date('Y-m-d H:i:s') . " - Error de validación JWT: " . $e->getMessage() . "\n";
			$log_msg .= "Hora actual del servidor (time()): " . time() . "\n";

			if (substr_count($token, '.') === 2) {
				$payload_base64 = explode('.', $token)[1];
				// Añadir padding de base64 si es necesario
				$payload_json = base64_decode(str_pad(strtr($payload_base64, '-_', '+/'), strlen($payload_base64) % 4 === 0 ? strlen($payload_base64) : strlen($payload_base64) + (4 - strlen($payload_base64) % 4), '='));
				$payload_arr = json_decode($payload_json, true);
				$log_msg .= "Payload: " . $payload_json . "\n";
				if (isset($payload_arr['iat']))
					$log_msg .= "  iat (emitido): " . $payload_arr['iat'] . " => " . date('Y-m-d H:i:s', $payload_arr['iat']) . "\n";
				if (isset($payload_arr['exp']))
					$log_msg .= "  exp (expira):  " . $payload_arr['exp'] . " => " . date('Y-m-d H:i:s', $payload_arr['exp']) . "\n";
			}
			file_put_contents(__DIR__ . '/../error_sso.log', $log_msg . "--------------------------\n", FILE_APPEND);
			
			http_response_code(401);
			return ['error' => 'Token de la Intranet inválido o expirado: ' . $e->getMessage()];
		}

		$id = (int) ($datos_payload['id'] ?? 0);
		$email = $datos_payload['email'] ?? $datos_payload['correo'] ?? '';
		$nombre = $datos_payload['nombre'] ?? '';
		$apellidos = $datos_payload['apellidos'] ?? '';
		$nombre_completo = trim($nombre . ' ' . $apellidos);
		$roles_intranet = (array) ($datos_payload['roles'] ?? []);

		if (!$id) {
			http_response_code(400);
			return ['error' => 'El token de la intranet no contiene un ID de usuario válido'];
		}

		if (empty($email)) {
			http_response_code(400);
			file_put_contents(__DIR__ . '/../error_sso.log', date('Y-m-d H:i:s') . " - Error: Falta el email en el payload.\n", FILE_APPEND);
			return ['error' => 'El token de la intranet no contiene un correo válido'];
		}

		// Buscar si el usuario ya existe en nuestra base de datos local por su ID
		try {
			$usuario = $this->modelo_usuario->buscar_por_id($id);
		} catch (Exception $e) {
			file_put_contents(__DIR__ . '/../error_sso.log', date('Y-m-d H:i:s') . " - BD Error buscar_por_id: " . $e->getMessage() . "\n", FILE_APPEND);
			http_response_code(500);
			return ['error' => 'Error de base de datos'];
		}

		if (!$usuario) {
			// Mapear los roles de la intranet a nuestro rol de Ticketing local por defecto
			$rol_local = 'profesor'; // Rol base de solicitante por defecto (id_rol = NULL)

			if (in_array('super_admin', $roles_intranet) || in_array('administrador_secretaria', $roles_intranet))
				$rol_local = 'administrador';
			elseif (in_array('coordinador_aula_matinal', $roles_intranet) || in_array('coordinador_comedor', $roles_intranet) || in_array('coordinador_inscripciones', $roles_intranet) || in_array('coordinador_dualex', $roles_intranet))
				$rol_local = 'responsable';

			// Crear usuario local de forma transparente con el ID heredado
			$datos_nuevo = [
				'id' => $id,
				'rol' => $rol_local
			];

			$nuevo_id = $this->modelo_usuario->crear($datos_nuevo);
			if (!$nuevo_id) {
				http_response_code(500);
				return ['error' => 'Error al crear el perfil local de usuario'];
			}

			// Recuperar el usuario recién insertado
			$usuario = $this->modelo_usuario->buscar_por_id($id);
		}

		// Incrementar el contador de visitas local si procede
		$this->incrementar_visitas_usuario((int) $usuario['id']);

		// Generar JWT de sesión interno de TicketingEVG
		$iat = time();
		$jwt_payload = [
			'iat' => $iat,
			'exp' => $iat + (int) ($_ENV['JWT_EXPIRATION'] ?? 86400),
			'data' => [
				'id' => (int) $usuario['id'],
				'email' => $email, // Se toma dinámicamente del token de la intranet
				'nombre' => $nombre_completo, // Se toma dinámicamente del token de la intranet
				'rol' => $usuario['rol'] ?? 'profesor' // Si id_rol es NULL localmente, se asigna 'profesor'
			]
		];

		$jwt_interno = JWT::encode($jwt_payload, $_ENV['JWT_SECRET'] ?? 'default_secret', 'HS256');

		return [
			'status' => 'success',
			'token' => $jwt_interno,
			'usuario' => $jwt_payload['data']
		];
	}

	/**
	 * Incrementa de forma transparente el contador de visitas de un usuario local.
	 * 
	 * @param int $id_usuario
	 */
	private function incrementar_visitas_usuario($id_usuario) {
		$db = Conexion::conectar();
		$stmt = $db->prepare("UPDATE Usuario SET visitas_totales = visitas_totales + 1 WHERE id = ?");
		$stmt->bind_param("i", $id_usuario);
		$stmt->execute();
	}
}
?>
