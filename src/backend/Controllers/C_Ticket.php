<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador (C) para gestionar las peticiones API de Tickets.
 */

require_once __DIR__ . '/../Models/M_Ticket.php';

class C_Ticket {
    /** @var M_Ticket Instancia del modelo */
    private $modelo;

    public function __construct() {
        $this->modelo = new M_Ticket();
    }

    /**
     * Maneja la petición de listado de tickets.
     * @param int|null $id_usuario Filtro opcional por creador.
     */
    public function listar($id_usuario = null) {
        if ($id_usuario) {
            return $this->modelo->listar_por_usuario($id_usuario);
        }
        return $this->modelo->listar();
    }

    /**
     * Maneja la creación de un nuevo ticket a partir de datos JSON.
     * @param array $json_data Datos decodificados del cuerpo de la petición.
     */
    public function guardar($json_data) {
        $id_usuario_creador = $json_data['id_usuario_creador'] ?? $json_data['id_Usuario_Creador'] ?? null;
        if (!isset($json_data['titulo']) || !$id_usuario_creador) {
            return ["status" => "error", "message" => "Faltan campos obligatorios"];
        }

        $id = $this->modelo->crear($json_data);
        if ($id) {
            return ["status" => "success", "id" => $id, "message" => "Ticket creado correctamente"];
        }
        return ["status" => "error", "message" => "Error interno al insertar en la base de datos"];
    }

    /**
     * Comprueba si el usuario en sesión tiene permisos sobre un ticket.
     * @param array $ticket Datos actuales del ticket.
     * @param bool $es_edicion_o_cancelacion Indica si la acción es editar o cancelar.
     * @return bool|string Devuelve true si tiene permiso, o un mensaje de error si no.
     */
    private function verificar_permisos_solicitante($ticket, $es_edicion_o_cancelacion = false) {
        $usuario = $GLOBALS['usuario_sesion'] ?? null;
        if (!$usuario) return "No autenticado";

        $rol = $usuario['rol'] ?? 'profesor';

        // Técnicos tienen poder absoluto
        if ($rol !== 'profesor') return true;

        // Reglas para el profesor (solicitante)
        if ((int)$ticket['id_usuario_creador'] !== (int)$usuario['id']) {
            return "No tienes permiso para modificar un ticket que no es tuyo";
        }

        if ($es_edicion_o_cancelacion) {
            $estado = $ticket['estado'];
            if ($estado === 'proceso' || $estado === 'resuelto' || $estado === 'no aplica') {
                return "No puedes modificar ni cancelar un ticket que está en " . $estado;
            }
        }

        return true;
    }

    /**
     * Actualiza los datos generales de un ticket.
     * @param string $id Identificador del ticket.
     * @param array $json_data Nuevos datos.
     */
    public function actualizar($id, $json_data) {
        $ticket_actual = $this->modelo->buscar_por_id($id);
        if (!$ticket_actual) return ["status" => "error", "message" => "Ticket no encontrado"];

        $permiso = $this->verificar_permisos_solicitante($ticket_actual, true);
        if ($permiso !== true) return ["status" => "error", "message" => $permiso];

        if ($this->modelo->actualizar($id, $json_data)) {
            return ["status" => "success", "message" => "Ticket actualizado correctamente"];
        }
        return ["status" => "error", "message" => "Error al actualizar el ticket"];
    }

    /**
     * Gestiona el cambio de estado de un ticket.
     * @param string $id Identificador del ticket.
     * @param string $estado Nuevo estado.
     */
    public function cambiar_estado($id, $estado) {
        $ticket_actual = $this->modelo->buscar_por_id($id);
        if (!$ticket_actual) return ["status" => "error", "message" => "Ticket no encontrado"];

        $usuario = $GLOBALS['usuario_sesion'] ?? null;
        $rol = $usuario['rol'] ?? 'profesor';

        if ($rol === 'profesor') {
            if ($estado === 'resuelto') {
                return ["status" => "error", "message" => "No tienes permisos para marcar un ticket como resuelto"];
            }
            if ($estado === 'no aplica') {
                $permiso = $this->verificar_permisos_solicitante($ticket_actual, true);
                if ($permiso !== true) return ["status" => "error", "message" => $permiso];
            } elseif ($estado !== $ticket_actual['estado']) {
                // El profesor no puede cambiar el estado a asignado o pendiente directamente
                return ["status" => "error", "message" => "No tienes permisos para cambiar a este estado"];
            }
        }

        if ($this->modelo->actualizar_estado($id, $estado)) {
            return ["status" => "success", "message" => "Estado actualizado"];
        }
        return ["status" => "error", "message" => "No se pudo actualizar el estado"];
    }

    /**
     * Gestiona la eliminación de un ticket.
     * @param string $id Identificador del ticket.
     */
    public function borrar($id) {
        $usuario = $GLOBALS['usuario_sesion'] ?? null;
        $rol = $usuario['rol'] ?? 'profesor';
        
        // Solo un técnico o administrador podría borrar físicamente un ticket (o ni eso, pero lo bloqueamos para profes)
        if ($rol === 'profesor') {
            return ["status" => "error", "message" => "No tienes permisos para eliminar tickets físicamente. Usa cancelar en su lugar."];
        }

        if ($this->modelo->eliminar($id)) {
            return ["status" => "success", "message" => "Ticket eliminado"];
        }
        return ["status" => "error", "message" => "No se pudo eliminar el ticket"];
    }
}
?>
