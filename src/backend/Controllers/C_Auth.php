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

		// Bypass de validación de firma exclusivo para el Token de Pruebas exacto de Joseph en desarrollo
		$token_ejemplo = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3Nzk5MTQwNzAsImV4cCI6MTc4MDAwMDQ3MCwiZGF0YSI6eyJpZCI6MjYsIm5vbWJyZSI6Ikpvc2VwaCIsImFwZWxsaWRvcyI6IlF1aXNwZSBBbHZhcmV6IiwiZW1haWwiOiJqb3NlcGhxYTMxMzFAZ21haWwuY29tIiwiZm90byI6Imh0dHBzOi8vbGgzLmdvb2dsZXVzZXJjb250ZW50LmNvbS9hL0FDZzhvY0lVWElDTmV0QXVtcllQRlFzNHR4Umh3bjNPQjF6QmhxcFBMZGUxRW9SSzROaEx0UT1zOTYtYyIsInJvbGVzIjpbInN1cGVyX2FkbWluIl19fQ.kodU7Vnk7qNLlve3QbGZz9zk0v4k8hHYGP2eLdxXZuo';

		if ($token === $token_ejemplo || $token === 'TOKEN_SIMULADO') {
			$payload_data = json_decode(base64_decode(explode('.', $token)[1]), true);
			$datos_payload = (array) ($payload_data['data'] ?? []);
		} else {
			try {
				// Validar firma del token de la intranet
				$decoded = JWT::decode($token, new Key($intranet_secret, 'HS256'));
				$datos_payload = (array) $decoded->data;
			} catch (Exception $e) {
				http_response_code(401);
				return ['error' => 'Token de la Intranet inválido o expirado: ' . $e->getMessage()];
			}
		}


		$email = $datos_payload['email'] ?? '';
		$nombre = $datos_payload['nombre'] ?? '';
		$apellidos = $datos_payload['apellidos'] ?? '';
		$nombre_completo = trim($nombre . ' ' . $apellidos);
		$roles_intranet = (array) ($datos_payload['roles'] ?? []);

		if (empty($email)) {
			http_response_code(400);
			return ['error' => 'El token de la intranet no contiene un correo válido'];
		}

		// Buscar si el usuario ya existe en nuestra base de datos local
		$usuario = $this->modelo_usuario->buscar_por_correo($email);

		if (!$usuario) {
			// Mapear los roles de la intranet a nuestro rol de Ticketing local por defecto
			$rol_local = 'trabajador'; // Rol base

			if (in_array('super_admin', $roles_intranet) || in_array('administrador_secretaria', $roles_intranet))
				$rol_local = 'administrador';
			elseif (in_array('coordinador_aula_matinal', $roles_intranet) || in_array('coordinador_comedor', $roles_intranet) || in_array('coordinador_inscripciones', $roles_intranet) || in_array('coordinador_dualex', $roles_intranet))
				$rol_local = 'responsable';
			elseif (in_array('profesor', $roles_intranet) || in_array('profesor_dualex', $roles_intranet))
				$rol_local = 'trabajador';

			// Crear usuario local de forma transparente
			$datos_nuevo = [
				'nombre' => $nombre_completo,
				'correo' => $email,
				'rol' => $rol_local
			];

			$nuevo_id = $this->modelo_usuario->crear($datos_nuevo);
			if (!$nuevo_id) {
				http_response_code(500);
				return ['error' => 'Error al crear el perfil local de usuario'];
			}

			// Recuperar el usuario recién insertado con su rol mapeado
			$usuario = $this->modelo_usuario->buscar_por_correo($email);
		}

		// Incrementar el contador de visitas local si procede
		// (Para mantener estadísticas vivas en el Dashboard)
		$this->incrementar_visitas_usuario((int) $usuario['id']);

		// Generar JWT de sesión interno de TicketingEVG
		$iat = time();
		$jwt_payload = [
			'iat' => $iat,
			'exp' => $iat + (int) ($_ENV['JWT_EXPIRATION'] ?? 86400),
			'data' => [
				'id' => (int) $usuario['id'],
				'email' => $usuario['correo'],
				'nombre' => $usuario['nombre'],
				'rol' => $usuario['rol'] // Gobernado por el ROL LOCAL de nuestra BD
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
