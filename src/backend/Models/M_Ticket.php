<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Modelo de datos (M) para la gestión de tickets en la base de datos.
 */

require_once __DIR__ . '/../config/Conexion.php';

class M_Ticket {
    /** @var mysqli Instancia de conexión a la base de datos */
    private $db;

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    /**
     * Obtiene todos los tickets registrados.
     * @return array Lista de tickets.
     */
    public function listar() {
        $sql = "SELECT * FROM Ticket ORDER BY fecha_creacion DESC";
        $resultado = $this->db->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene los tickets creados por un usuario específico.
     * @param int $id_usuario ID del creador.
     * @return array Lista de tickets filtrada.
     */
    public function listar_por_usuario($id_usuario) {
        $sql = "SELECT * FROM Ticket WHERE id_Usuario_Creador = ? ORDER BY fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Inserta un nuevo ticket generando un ID dinámico según el tipo y fecha.
     * @param array $datos Información del ticket.
     * @return string|bool El ID generado o false si falla.
     */
    public function crear($datos) {
        // Lógica de generación de ID: [Tipo][DDMMYY][CatID][IncID]
        $tipo_prefijo = ($datos['tipo'] === 'incidencia') ? 'I' : 'PC';
        $fecha_actual = date('dmy');
        $cat_id = str_pad($datos['id_Categoria'], 2, '0', STR_PAD_LEFT);

        // Contar tickets del mismo tipo y día para el autoincremento manual
        $patron = $tipo_prefijo . $fecha_actual . $cat_id . '%';
        $stmt_contar = $this->db->prepare("SELECT COUNT(*) as total FROM Ticket WHERE id LIKE ?");
        $stmt_contar->bind_param("s", $patron);
        $stmt_contar->execute();
        $total = $stmt_contar->get_result()->fetch_assoc()['total'];
        $inc_id = str_pad($total + 1, 2, '0', STR_PAD_LEFT);

        $id_nuevo = $tipo_prefijo . $fecha_actual . $cat_id . $inc_id;

        $sql = "INSERT INTO Ticket (id, id_Categoria, titulo, descripcion, prioridad, id_Usuario_Creador, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sisssis", 
            $id_nuevo, 
            $datos['id_Categoria'], 
            $datos['titulo'], 
            $datos['descripcion'], 
            $datos['prioridad'], 
            $datos['id_Usuario_Creador'],
            $datos['estado']
        );

        if ($stmt->execute()) {
            return $id_nuevo;
        }
        return false;
    }

    /**
     * Actualiza el estado de un ticket existente.
     * @param string $id ID del ticket.
     * @param string $nuevo_estado Nuevo estado a asignar.
     */
    public function actualizar_estado($id, $nuevo_estado) {
        $stmt = $this->db->prepare("UPDATE Ticket SET estado = ? WHERE id = ?");
        $stmt->bind_param("ss", $nuevo_estado, $id);
        return $stmt->execute();
    }

    /**
     * Elimina un ticket por su identificador.
     * @param string $id ID del ticket.
     */
    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM Ticket WHERE id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    /**
     * Obtiene un ticket específico por su ID.
     * @param string $id ID del ticket.
     * @return array|null Datos del ticket o null si no existe.
     */
    public function obtener_por_id($id) {
        $stmt = $this->db->prepare("SELECT t.*, c.nombre as categoria_nombre, 
                                            u_creador.nombre as usuario_creador_nombre,
                                            u_encargado.nombre as usuario_encargado_nombre
                                     FROM Ticket t
                                     LEFT JOIN Categoria c ON t.id_Categoria = c.id
                                     LEFT JOIN Usuario u_creador ON t.id_Usuario_Creador = u_creador.id
                                     LEFT JOIN Usuario u_encargado ON t.id_Usuario_Encargado = u_encargado.id
                                     WHERE t.id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    /**
     * Obtiene los tickets asignados a un operario específico.
     * @param int $id_operario ID del operario.
     * @param string|null $estado Filtro opcional por estado.
     * @return array Lista de tickets asignados.
     */
    public function listar_por_operario($id_operario, $estado = null) {
        $sql = "SELECT t.*, c.nombre as categoria_nombre, u.nombre as usuario_creador_nombre
                FROM Ticket t
                LEFT JOIN Categoria c ON t.id_Categoria = c.id
                LEFT JOIN Usuario u ON t.id_Usuario_Creador = u.id
                WHERE t.id_Usuario_Encargado = ?";
        
        if ($estado) {
            $sql .= " AND t.estado = ?";
        }
        
        $sql .= " ORDER BY t.fecha_creacion DESC";
        
        $stmt = $this->db->prepare($sql);
        
        if ($estado) {
            $stmt->bind_param("is", $id_operario, $estado);
        } else {
            $stmt->bind_param("i", $id_operario);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Asigna un operario a un ticket.
     * @param string $id_ticket ID del ticket.
     * @param int $id_operario ID del operario.
     * @return bool Verdadero si la asignación fue exitosa.
     */
    public function asignar_operario($id_ticket, $id_operario) {
        $estado = 'asignado';
        $stmt = $this->db->prepare("UPDATE Ticket SET id_Usuario_Encargado = ?, estado = ? WHERE id = ?");
        $stmt->bind_param("iss", $id_operario, $estado, $id_ticket);
        return $stmt->execute();
    }

    /**
     * Obtiene estadísticas de tickets para un operario.
     * @param int $id_operario ID del operario.
     * @return array Array con estadísticas.
     */
    public function obtener_estadisticas_operario($id_operario) {
        $stats = [
            'total_asignados' => 0,
            'en_proceso' => 0,
            'pendientes' => 0,
            'resueltos' => 0,
            'alta_prioridad' => 0
        ];

        // Total de tickets asignados
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE id_Usuario_Encargado = $id_operario");
        if ($res && $row = $res->fetch_assoc()) $stats['total_asignados'] = $row['total'] ?? 0;

        // Tickets en proceso
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE id_Usuario_Encargado = $id_operario AND estado = 'proceso'");
        if ($res && $row = $res->fetch_assoc()) $stats['en_proceso'] = $row['total'] ?? 0;

        // Tickets pendientes de asignación inicial
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE id_Usuario_Encargado = $id_operario AND estado = 'asignado'");
        if ($res && $row = $res->fetch_assoc()) $stats['pendientes'] = $row['total'] ?? 0;

        // Tickets resueltos
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE id_Usuario_Encargado = $id_operario AND estado = 'resuelto'");
        if ($res && $row = $res->fetch_assoc()) $stats['resueltos'] = $row['total'] ?? 0;

        // Tickets de alta prioridad activos
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE id_Usuario_Encargado = $id_operario AND prioridad = 'a' AND estado != 'resuelto'");
        if ($res && $row = $res->fetch_assoc()) $stats['alta_prioridad'] = $row['total'] ?? 0;

        return $stats;
    }

    /**
     * Obtiene tickets disponibles para asignación (pendientes y sin operario).
     * @return array Lista de tickets disponibles.
     */
    public function obtener_tickets_disponibles() {
        $sql = "SELECT t.*, c.nombre as categoria_nombre, u.nombre as usuario_creador_nombre
                FROM Ticket t
                LEFT JOIN Categoria c ON t.id_Categoria = c.id
                LEFT JOIN Usuario u ON t.id_Usuario_Creador = u.id
                WHERE t.id_Usuario_Encargado IS NULL AND t.estado = 'pendiente'
                ORDER BY 
                    CASE WHEN t.prioridad = 'a' THEN 1
                         WHEN t.prioridad = 'm' THEN 2
                         ELSE 3
                    END ASC,
                    t.fecha_creacion ASC";
        
        $resultado = $this->db->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Actualiza el estado de un ticket validando que el operario esté asignado.
     * @param string $id_ticket ID del ticket.
     * @param string $nuevo_estado Nuevo estado.
     * @param int|null $id_operario ID del operario (para validación).
     * @return bool Verdadero si se actualizó correctamente.
     */
    public function actualizar_estado_validado($id_ticket, $nuevo_estado, $id_operario = null) {
        if ($id_operario) {
            // Validar que el operario esté asignado al ticket
            $stmt_check = $this->db->prepare("SELECT id FROM Ticket WHERE id = ? AND id_Usuario_Encargado = ?");
            $stmt_check->bind_param("si", $id_ticket, $id_operario);
            $stmt_check->execute();
            if (!$stmt_check->get_result()->fetch_assoc()) {
                return false; // El operario no está asignado a este ticket
            }
        }

        return $this->actualizar_estado($id_ticket, $nuevo_estado);
    }
}
?>
