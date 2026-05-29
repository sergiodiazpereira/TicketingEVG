<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Modelo para consultar datos directamente desde la base de datos de la Intranet.
 */

require_once __DIR__ . '/../config/ConexionIntranet.php';

class M_Intranet {
    private $db;

    public function __construct() {
        $this->db = ConexionIntranet::conectar();
    }

    /**
     * Obtiene todo el personal registrado en la Intranet.
     * Asume que la tabla se llama 'personal' en la BBDD de Aitor.
     * @return array
     */
    public function listar_personal() {
        try {
            $sql = "SELECT id, nombre, apellidos, email FROM personal WHERE tipo_personal = 'servicio'";
            $resultado = $this->db->query($sql);
            if (!$resultado) {
                return [];
            }
            $usuarios_api = [];
            while ($row = $resultado->fetch_assoc()) {
                $usuarios_api[] = [
                    'id'     => (int) $row['id'],
                    'nombre' => trim(($row['nombre'] ?? '') . ' ' . ($row['apellidos'] ?? '')),
                    'correo' => $row['email'] ?? ''
                ];
            }
            return $usuarios_api;
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
