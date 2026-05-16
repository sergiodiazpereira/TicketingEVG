<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para las peticiones de Usuarios.
 */

require_once __DIR__ . '/../Models/M_Usuario.php';

class C_Usuario {
    private $modelo;

    public function __construct() {
        $this->modelo = new M_Usuario();
    }

    /**
     * Obtiene la lista de operarios del sistema.
     * @return array
     */
    public function listar_operarios() {
        return $this->modelo->listar_operarios();
    }

    /**
     * Obtiene estadísticas globales de los usuarios.
     * @return array
     */
    public function get_estadisticas() {
        return $this->modelo->get_estadisticas();
    }
}
?>
