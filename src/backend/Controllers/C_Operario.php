<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Manuel Vega Purificación
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador (C) para gestionar operarios y sus tickets.
 */

require_once __DIR__ . '/../Models/M_Operario.php';

class C_Operario {
    /** @var M_Operario Instancia del modelo */
    private $modelo;

    public function __construct() {
        $this->modelo = new M_Operario();
    }

    /**
     * Obtiene la lista de todos los operarios.
     */
    public function listar_operarios() {
        return $this->modelo->listar_operarios();
    }

    /**
     * Obtiene un operario específico.
     * @param int $id_operario ID del operario.
     */
    public function obtener_operario($id_operario) {
        return $this->modelo->obtener_operario($id_operario);
    }

    /**
     * Obtiene los tickets de un operario con filtros.
     * @param int $id_operario ID del operario.
     * @param string|null $estado Filtro opcional.
     * @param int $limite Número máximo.
     * @param int $offset Punto de inicio.
     */
    public function obtener_tickets_operario($id_operario, $estado = null, $limite = 999, $offset = 0) {
        return $this->modelo->obtener_tickets_operario($id_operario, $estado, $limite, $offset);
    }

    /**
     * Obtiene detalles de un ticket.
     * @param string $id_ticket ID del ticket.
     */
    public function obtener_ticket_detalle($id_ticket) {
        return $this->modelo->obtener_ticket_detalle($id_ticket);
    }

    /**
     * Actualiza el estado de un ticket.
     * @param string $id_ticket ID del ticket.
     * @param string $nuevo_estado Nuevo estado.
     * @param int $id_operario ID del operario.
     */
    public function actualizar_estado_ticket($id_ticket, $nuevo_estado, $id_operario) {
        if ($this->modelo->actualizar_estado_ticket($id_ticket, $nuevo_estado, $id_operario)) {
            return ["status" => "success", "message" => "Estado actualizado correctamente"];
        }
        return ["status" => "error", "message" => "Error al actualizar el estado o sin permisos"];
    }

    /**
     * Obtiene estadísticas de un operario.
     * @param int $id_operario ID del operario.
     */
    public function obtener_estadisticas($id_operario) {
        return $this->modelo->obtener_estadisticas($id_operario);
    }

    /**
     * Obtiene tickets disponibles sin asignar.
     * @param int $limite Número máximo.
     */
    public function obtener_tickets_disponibles($limite = 10) {
        return $this->modelo->obtener_tickets_disponibles($limite);
    }

    /**
     * Asigna un ticket a un operario.
     * @param string $id_ticket ID del ticket.
     * @param int $id_operario ID del operario.
     */
    public function asignar_ticket($id_ticket, $id_operario) {
        if ($this->modelo->asignar_ticket($id_ticket, $id_operario)) {
            return ["status" => "success", "message" => "Ticket asignado correctamente"];
        }
        return ["status" => "error", "message" => "Error al asignar el ticket"];
    }

    /**
     * Busca tickets.
     * @param int $id_operario ID del operario.
     * @param string $termino Término de búsqueda.
     */
    public function buscar_tickets($id_operario, $termino) {
        return $this->modelo->buscar_tickets($id_operario, $termino);
    }
}
?>
