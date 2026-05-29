<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Gestión de la conexión a la base de datos externa de la Intranet (Aitor).
 */

class ConexionIntranet {
    private static $conexion = null;

    /**
     * Establece y retorna la conexión única a la base de datos de la Intranet.
     * @return mysqli Objeto de conexión.
     */
    public static function conectar() {
        if (self::$conexion === null) {
            $config = [];
            $ruta_env = __DIR__ . '/../.env';
            if (file_exists($ruta_env)) {
                $lineas = file($ruta_env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lineas as $linea) {
                    $linea = trim($linea);
                    if (empty($linea) || $linea[0] === '#' || $linea[0] === ';') continue;
                    if (strpos($linea, '=') !== false) {
                        list($key, $val) = explode('=', $linea, 2);
                        $config[trim($key)] = trim($val, " \t\n\r\0\x0B\"");
                    }
                }
            }
            
            // Las credenciales de la BBDD de Aitor se recogerán de estas variables
            $servidor = $config['INTRANET_DB_HOST'] ?? '127.0.0.1';
            $usuario  = $config['INTRANET_DB_USER'] ?? 'root';
            $clave    = $config['INTRANET_DB_PASS'] ?? '';
            $base_datos = $config['INTRANET_DB_NAME'] ?? 'intranet';
            $puerto = $config['INTRANET_DB_PORT'] ?? 3306;
            
            self::$conexion = @new mysqli($servidor, $usuario, $clave, $base_datos, $puerto);

            if (self::$conexion->connect_error) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Fallo de conexión a BBDD Intranet: ' . self::$conexion->connect_error]);
                exit;
            }

            self::$conexion->set_charset("utf8mb4");
        }
        return self::$conexion;
    }
}
?>
