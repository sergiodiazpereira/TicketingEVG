<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Sergio Díaz Pereira
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para las peticiones de dashboard admin.
 */

require_once __DIR__ . '/../Models/M_Dashboard.php';

class C_Dashboard {
    private $modelo;

    public function __construct() {
        $this->modelo = new M_Dashboard();
    }

    /**
     * Obtiene los datos estadísticos para el dashboard.
     * @return array
     */
    public function obtenerDatos() {
        return $this->modelo->obtenerDatos();
    }
}
?>