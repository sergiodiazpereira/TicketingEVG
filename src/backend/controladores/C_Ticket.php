<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador (C) para gestionar las peticiones API de Tickets.
 */

require_once __DIR__ . '/../modelos/M_Ticket.php';

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
        if (!isset($json_data['titulo']) || !isset($json_data['id_Usuario_Creador'])) {
            return ["status" => "error", "message" => "Faltan campos obligatorios"];
        }

        $id = $this->modelo->crear($json_data);
        if ($id) {
            return ["status" => "success", "id" => $id, "message" => "Ticket creado correctamente"];
        }
        return ["status" => "error", "message" => "Error interno al insertar en la base de datos"];
    }

    /**
     * Gestiona el cambio de estado de un ticket.
     * @param string $id Identificador del ticket.
     * @param string $estado Nuevo estado.
     */
    public function cambiar_estado($id, $estado) {
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
        if ($this->modelo->eliminar($id)) {
            return ["status" => "success", "message" => "Ticket eliminado"];
        }
        return ["status" => "error", "message" => "No se pudo eliminar el ticket"];
    }
}
?>
