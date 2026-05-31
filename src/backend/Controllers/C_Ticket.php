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

        $titulo = trim($json_data['titulo'] ?? '');
        $descripcion = trim($json_data['descripcion'] ?? '');
        $ubicacion = isset($json_data['ubicacion']) ? trim($json_data['ubicacion']) : '';

        if (empty($titulo)) {
            return ["status" => "error", "message" => "El título no puede estar vacío ni contener sólo espacios en blanco"];
        }
        if (empty($descripcion)) {
            return ["status" => "error", "message" => "La descripción no puede estar vacía ni contener sólo espacios en blanco"];
        }
        if (isset($json_data['ubicacion']) && $json_data['ubicacion'] !== '' && $json_data['ubicacion'] !== null && empty($ubicacion)) {
            return ["status" => "error", "message" => "La ubicación no puede contener sólo espacios en blanco"];
        }

        $id = $this->modelo->crear($json_data);
        if ($id) {
            return ["status" => "success", "id" => $id, "message" => "Ticket creado correctamente"];
        }
        return ["status" => "error", "message" => "Error interno al insertar en la base de datos"];
    }

    /**
     * Comprueba si un trabajador debe ser tratado como profesor por ser el creador y no estar asignado.
     * @param array $ticket Datos del ticket.
     * @return bool
     */
    private function debe_ver_como_profesor($ticket) {
        $usuario = $GLOBALS['usuario_sesion'] ?? null;
        if (!$usuario) return false;

        $rol = $usuario['rol'] ?? 'profesor';
        $es_trabajador = in_array(strtolower($rol ?? ''), ['trabajador', 'operario']);
        $es_creador = (int)$ticket['id_usuario_creador'] === (int)$usuario['id'];

        if ($es_trabajador && $es_creador) {
            $es_asignado_a_si_mismo = (int)$ticket['id_usuario_encargado'] === (int)$usuario['id'];
            return !$es_asignado_a_si_mismo;
        }

        return false;
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
        $es_tecnico = in_array(strtolower($rol ?? ''), ['administrador', 'admin', 'responsable', 'trabajador', 'operario']);
        if ($this->debe_ver_como_profesor($ticket))
            $es_tecnico = false;

        if ($es_tecnico) return true;

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

        $usuario = $GLOBALS['usuario_sesion'] ?? null;
        $rol = $usuario['rol'] ?? 'profesor';
        if (!$this->debe_ver_como_profesor($ticket_actual) && in_array(strtolower($rol ?? ''), ['trabajador', 'operario']))
            return ["status" => "error", "message" => "Los trabajadores no tienen permisos para editar la información base del ticket"];

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

        $es_tecnico = in_array(strtolower($rol ?? ''), ['administrador', 'admin', 'responsable', 'trabajador', 'operario']);
        if ($this->debe_ver_como_profesor($ticket_actual))
            $es_tecnico = false;

        if (!$es_tecnico) {
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
        $es_tecnico = in_array(strtolower($rol ?? ''), ['administrador', 'admin', 'responsable', 'trabajador', 'operario']);
        if (!$es_tecnico) {
            return ["status" => "error", "message" => "No tienes permisos para eliminar tickets físicamente. Usa cancelar en su lugar."];
        }

        if ($this->modelo->eliminar($id)) {
            return ["status" => "success", "message" => "Ticket eliminado"];
        }
        return ["status" => "error", "message" => "No se pudo eliminar el ticket"];
    }

    /**
     * Asigna un técnico/responsable a un ticket.
     * @param string $id Identificador del ticket.
     * @param int $id_usuario_encargado Identificador del usuario a asignar.
     */
    public function asignar($id, $id_usuario_encargado) {
        $usuario = $GLOBALS['usuario_sesion'] ?? null;
        $rol = $usuario['rol'] ?? 'profesor';

        if ($rol !== 'administrador' && $rol !== 'admin' && $rol !== 'responsable') {
            return ["status" => "error", "message" => "Solo administradores y responsables pueden asignar tickets"];
        }

        $ticket_actual = $this->modelo->buscar_por_id($id);
        if (!$ticket_actual) return ["status" => "error", "message" => "Ticket no encontrado"];

        if ($this->modelo->asignar_operario($id, $id_usuario_encargado)) {
            return ["status" => "success", "message" => "Ticket asignado correctamente"];
        }
        return ["status" => "error", "message" => "No se pudo asignar el ticket"];
    }

	/**
	 * Lista los comentarios asociados a un ticket.
	 * @param string $id_ticket Identificador del ticket.
	 */
	public function listar_comentarios($id_ticket) {
		$usuario = $GLOBALS['usuario_sesion'] ?? null;
		if (!$usuario)
			return ["status" => "error", "message" => "Usuario no autenticado"];

		$comentarios = $this->modelo->obtener_comentarios($id_ticket);
		return ["status" => "success", "data" => $comentarios];
	}

	/**
	 * Guarda un nuevo comentario en un ticket.
	 * @param array $json_data Datos del comentario.
	 */
	public function guardar_comentario($json_data) {
		$usuario = $GLOBALS['usuario_sesion'] ?? null;
		if (!$usuario)
			return ["status" => "error", "message" => "Usuario no autenticado"];

		$id_ticket = $json_data['id_ticket'] ?? null;
		$texto = $json_data['texto'] ?? null;

		if (!$id_ticket || !$texto || empty(trim($texto)))
			return ["status" => "error", "message" => "Faltan campos obligatorios o el comentario está vacío"];

		// Validar que el ticket existe
		$ticket = $this->modelo->buscar_por_id($id_ticket);
		if (!$ticket)
			return ["status" => "error", "message" => "El ticket no existe"];

		if ($this->modelo->crear_comentario($id_ticket, (int)$usuario['id'], trim($texto)))
			return ["status" => "success", "message" => "Comentario guardado correctamente"];
		
		return ["status" => "error", "message" => "Error al guardar el comentario en la base de datos"];
	}
}
?>

