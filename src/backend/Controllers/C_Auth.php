<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para autenticación con Google.
 */

use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

require_once __DIR__ . '/../Models/M_Usuario.php';

class C_Auth {
    private $modeloUsuario;

    public function __construct() {
        $this->modeloUsuario = new M_Usuario();
    }

    /**
     * Endpoint /api/auth/google
     * @param string $token
     * @return array
     */
    public function google($token = null) {
        if (!$token) {
            http_response_code(400);
            return ['error' => 'Token requerido'];
        }

        $payload = $this->verificarIdTokenGoogle($token, $_ENV['GOOGLE_CLIENT_ID'] ?? '');
        
        if (!$payload) {
            http_response_code(401);
            return ['error' => 'Token de Google inválido o no configurado'];
        }

        $email = $payload['email'];
        $nombre = $payload['name'] ?? 'Usuario Google';

        // Lógica de base de datos
        $usuario = $this->modeloUsuario->buscar_por_correo($email);
        
        if (!$usuario) {
            // Crear usuario nuevo con rol 'trabajador' por defecto
            $datos_nuevo = [
                'nombre' => $nombre,
                'correo' => $email,
                'rol' => 'trabajador'
            ];
            $nuevo_id = $this->modeloUsuario->crear($datos_nuevo);
            if (!$nuevo_id) {
                http_response_code(500);
                return ['error' => 'Error al crear el usuario en la base de datos'];
            }
            $usuario = $this->modeloUsuario->buscar_por_correo($email);
        }

        $iat = time();
        $jwtPayload = [
            'iat' => $iat,
            'exp' => $iat + (int)($_ENV['JWT_EXPIRATION'] ?? 86400),
            'data' => [
                'id' => $usuario['id'],
                'email' => $usuario['correo'],
                'nombre' => $usuario['nombre'],
                'rol' => $usuario['rol']
            ]
        ];

        $jwtInterno = JWT::encode($jwtPayload, $_ENV['JWT_SECRET'] ?? 'default_secret', 'HS256');

        return [
            'status' => 'success', 
            'token' => $jwtInterno,
            'usuario' => $jwtPayload['data']
        ];
    }

    private function verificarIdTokenGoogle(string $idToken, string $clientId): ?array {
        // En entorno local o sin credenciales, si se pasa un token de prueba (ej: uno falso),
        // Google rechazará. Intentaremos validar con las claves de Google.
        if (empty($clientId) || $clientId === 'TU_GOOGLE_CLIENT_ID') {
            // Simular login exitoso para facilitar pruebas locales si aún no hay Client ID
            // ¡ELIMINAR ESTO EN PRODUCCIÓN O CUANDO HAYA CREDENCIALES REALES!
            if ($idToken === 'TOKEN_SIMULADO') {
                return ['email' => 'admin@evg.es', 'name' => 'Admin Simulado'];
            }
            return null;
        }

        $jwksUrl = 'https://www.googleapis.com/oauth2/v3/certs';
        
        // Evitar problemas de SSL locales en curl
        $ch = curl_init($jwksUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $keysJson = curl_exec($ch);
        curl_close($ch);
        
        if (!$keysJson) return null;
        
        $keySet = JWK::parseKeySet(json_decode($keysJson, true));
        
        try {
            $decoded = (array) JWT::decode($idToken, $keySet);
            $validIssuers = ['https://accounts.google.com', 'accounts.google.com'];
            if (!in_array($decoded['iss'], $validIssuers)) return null;
            if ($decoded['aud'] !== $clientId) return null;
            if ($decoded['exp'] < time()) return null;
            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
}
?>
