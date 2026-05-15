<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para las peticiones de Categorías.
 */

require_once __DIR__ . '/../Models/M_Categoria.php';

class C_Categoria {
    private $modelo;

    public function __construct() {
        $this->modelo = new M_Categoria();
    }

    public function listar() {
        return $this->modelo->listar();
    }
}
?>
