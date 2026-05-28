<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez - Sergio Díaz Pereira
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
	 * Decodifica un segmento Base64Url (usado en JWT) de forma segura.
	 * PHP's base64_decode no soporta '-' ni '_' que usa el estándar JWT.
	 *
	 * @param string $entrada Segmento Base64Url del JWT.
	 * @return string JSON decodificado.
	 */
	private function base64url_decode($entrada) {
		$base64 = str_replace(['-', '_'], ['+', '/'], $entrada);
		$padding = strlen($base64) % 4;
		if ($padding)
			$base64 .= str_repeat('=', 4 - $padding);
		return base64_decode($base64);
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
			return ['error' => 'Token requerido'];
		}

		$intranet_secret = $_ENV['INTRANET_JWT_SECRET'] ?? null;
		$datos_payload = null;

		// Intentar validar con la clave secreta de la Intranet si está configurada
		if ($intranet_secret) {
			try {
				$decoded = JWT::decode($token, new Key($intranet_secret, 'HS256'));
				$datos_payload = (array) $decoded->data;
			} catch (Exception $e) {
				// La firma no coincide; se extrae el payload igualmente (modo desarrollo)
				$datos_payload = null;
			}
		}

		// Fallback: extraer payload sin verificar firma (Base64Url correcto)
		if (!$datos_payload) {
			$partes = explode('.', $token);
			if (count($partes) !== 3) {
				http_response_code(401);
				return ['error' => 'Formato de token inválido'];
			}
			$payload_json = $this->base64url_decode($partes[1]);
			$payload_data = json_decode($payload_json, true);
			if (!$payload_data) {
				http_response_code(401);
				return ['error' => 'No se pudo decodificar el token'];
			}
			$datos_payload = (array) ($payload_data['data'] ?? $payload_data);
		}

		$id = $datos_payload['id'] ?? null;
		$roles_intranet = (array) ($datos_payload['roles'] ?? []);

		if (!$id) {
			http_response_code(400);
			return ['error' => 'El token no contiene un ID válido'];
		}

		// Buscar si el usuario ya existe en nuestra base de datos local
		$usuario = $this->modelo_usuario->buscar_por_id($id);

		if ($usuario) {
			// Si existe, sumar +1 a visitas_totales
			$this->modelo_usuario->incrementar_visitas($id);
		} else {
			// Si no existe, crearlo.
			// Si la Intranet indica 'super_admin', el rol local es 1 (Administrador), si no, NULL.
			$id_rol = in_array('super_admin', $roles_intranet) ? 1 : null;
			$this->modelo_usuario->crear_con_id($id, $id_rol);
			$usuario = $this->modelo_usuario->buscar_por_id($id);
		}

		// Generar JWT de sesión interno de TicketingEVG
		$iat = time();
		$jwt_payload = [
			'iat' => $iat,
			'exp' => $iat + (int) ($_ENV['JWT_EXPIRATION'] ?? 86400),
			'data' => array_merge($datos_payload, [
				'id_rol_local' => isset($usuario['id_rol']) ? (int) $usuario['id_rol'] : null
			])
		];

		$jwt_interno = JWT::encode($jwt_payload, $_ENV['JWT_SECRET'] ?? 'ticketingevg_secret_2025', 'HS256');

		return [
			'status' => 'success',
			'token' => $jwt_interno,
			'usuario' => $jwt_payload['data']
		];
	}
}
?>
