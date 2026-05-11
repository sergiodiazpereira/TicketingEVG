<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Gestión de la conexión a la base de datos MySQLi.
 */

class Conexion {
    private static $conexion = null;

    /**
     * Establece y retorna la conexión única a la base de datos.
     * @return mysqli Objeto de conexión.
     */
    public static function conectar() {
        if (self::$conexion === null) {
            // Carga de configuración desde el archivo .env
            $ruta_env = __DIR__ . '/../.env';
            if (!file_exists($ruta_env)) {
                die("Error: No se encuentra el archivo .env en el backend.");
            }
            
            $config = parse_ini_file($ruta_env);
            
            $servidor = 'localhost';
            $usuario  = $config['USER_DB'];
            $clave    = $config['CLAVE'];
            $base_datos = $config['BBDD_DEMO']; // Se utiliza la base de datos de demostración

            self::$conexion = new mysqli($servidor, $usuario, $clave, $base_datos);

            if (self::$conexion->connect_error) {
                die("Error de conexión MySQLi: " . self::$conexion->connect_error);
            }

            self::$conexion->set_charset("utf8mb4");
        }
        return self::$conexion;
    }
}
?>
