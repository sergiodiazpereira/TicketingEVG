<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Manuel Vega Purificación
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Modelo de datos (M) para la gestión de operarios y sus tickets.
 */

require_once __DIR__ . '/../config/Conexion.php';

class M_Operario {
    /** @var mysqli Instancia de conexión a la base de datos */
    private $db;

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    /**
     * Obtiene todos los operarios (trabajadores y responsables).
     * @return array Lista de operarios.
     */
    public function listar_operarios() {
        $sql = "SELECT u.id, u.nombre, u.correo, u.activo, u.visitas_totales,
                   (SELECT COUNT(*) FROM Ticket WHERE id_Usuario_Encargado = u.id AND estado != 'resuelto') as tickets_activos,
                   (SELECT COUNT(*) FROM Ticket WHERE id_Usuario_Encargado = u.id AND estado = 'resuelto') as tickets_resueltos,
                   (SELECT COUNT(*) FROM Ticket WHERE id_Usuario_Encargado = u.id AND prioridad = 'a' AND estado != 'resuelto') as tickets_alta_prioridad
                FROM Usuario u
                JOIN Rol r ON u.id_Rol = r.id
                WHERE LOWER(r.nombre) IN ('responsable', 'trabajador', 'operario')
                ORDER BY u.nombre ASC";
        
        $resultado = $this->db->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Obtiene un operario específico por su ID.
     * @param int $id_operario ID del operario.
     * @return array|null Datos del operario.
     */
    public function obtener_operario($id_operario) {
        $sql = "SELECT u.*, r.nombre as rol_nombre,
                   (SELECT COUNT(*) FROM Ticket WHERE id_Usuario_Encargado = u.id AND estado != 'resuelto') as tickets_activos,
                   (SELECT COUNT(*) FROM Ticket WHERE id_Usuario_Encargado = u.id AND estado = 'resuelto') as tickets_resueltos
                FROM Usuario u
                JOIN Rol r ON u.id_Rol = r.id
                WHERE u.id = ? AND LOWER(r.nombre) IN ('responsable', 'trabajador', 'operario')";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_operario);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Obtiene los tickets asignados a un operario con filtros.
     * @param int $id_operario ID del operario.
     * @param string|null $estado Filtro por estado.
     * @param int $limite Número máximo de resultados.
     * @param int $offset Punto de inicio.
     * @return array Lista de tickets.
     */
    public function obtener_tickets_operario($id_operario, $estado = null, $limite = 999, $offset = 0) {
        $sql = "SELECT t.*, c.nombre as categoria_nombre, u.nombre as usuario_creador_nombre
                FROM Ticket t
                LEFT JOIN Categoria c ON t.id_Categoria = c.id
                LEFT JOIN Usuario u ON t.id_Usuario_Creador = u.id
                WHERE t.id_Usuario_Encargado = ?";
        
        if ($estado) {
            $sql .= " AND t.estado = ?";
        }
        
        $sql .= " ORDER BY 
                    CASE WHEN t.estado = 'proceso' THEN 1
                         WHEN t.estado = 'asignado' THEN 2
                         WHEN t.estado = 'pendiente' THEN 3
                         ELSE 4 END ASC,
                    CASE WHEN t.prioridad = 'a' THEN 1
                         WHEN t.prioridad = 'm' THEN 2
                         ELSE 3 END ASC,
                    t.fecha_creacion DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        
        if ($estado) {
            $stmt->bind_param("isii", $id_operario, $estado, $limite, $offset);
        } else {
            $stmt->bind_param("iii", $id_operario, $limite, $offset);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene un ticket específico con todos sus detalles.
     * @param string $id_ticket ID del ticket.
     * @return array|null Datos del ticket con detalles.
     */
    public function obtener_ticket_detalle($id_ticket) {
        $sql = "SELECT t.*, 
                   c.nombre as categoria_nombre,
                   u_creador.nombre as usuario_creador_nombre,
                   u_creador.correo as usuario_creador_correo,
                   u_encargado.nombre as usuario_encargado_nombre
                FROM Ticket t
                LEFT JOIN Categoria c ON t.id_Categoria = c.id
                LEFT JOIN Usuario u_creador ON t.id_Usuario_Creador = u_creador.id
                LEFT JOIN Usuario u_encargado ON t.id_Usuario_Encargado = u_encargado.id
                WHERE t.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $id_ticket);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Actualiza el estado de un ticket por un operario.
     * @param string $id_ticket ID del ticket.
     * @param string $nuevo_estado Nuevo estado.
     * @param int $id_operario ID del operario (para validación).
     * @return bool Verdadero si se actualizó.
     */
    public function actualizar_estado_ticket($id_ticket, $nuevo_estado, $id_operario) {
        // Validar que el operario está asignado al ticket
        $stmt_check = $this->db->prepare("SELECT id FROM Ticket WHERE id = ? AND id_Usuario_Encargado = ?");
        $stmt_check->bind_param("si", $id_ticket, $id_operario);
        $stmt_check->execute();
        
        if (!$stmt_check->get_result()->fetch_assoc()) {
            return false;
        }

        $fecha_actualizacion = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("UPDATE Ticket SET estado = ?, fecha_creacion = ? WHERE id = ?");
        $stmt->bind_param("sss", $nuevo_estado, $fecha_actualizacion, $id_ticket);
        return $stmt->execute();
    }

    /**
     * Obtiene estadísticas generales para un operario.
     * @param int $id_operario ID del operario.
     * @return array Estadísticas.
     */
    public function obtener_estadisticas($id_operario) {
        $stats = [
            'total_asignados' => 0,
            'en_proceso' => 0,
            'pendientes_asignacion' => 0,
            'resueltos' => 0,
            'alta_prioridad' => 0,
            'disponibles' => 0
        ];

        // Total asignados
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE id_Usuario_Encargado = $id_operario");
        if ($res && $row = $res->fetch_assoc()) $stats['total_asignados'] = $row['total'] ?? 0;

        // En proceso
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE id_Usuario_Encargado = $id_operario AND estado = 'proceso'");
        if ($res && $row = $res->fetch_assoc()) $stats['en_proceso'] = $row['total'] ?? 0;

        // Pendientes de asignación
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE id_Usuario_Encargado = $id_operario AND estado = 'asignado'");
        if ($res && $row = $res->fetch_assoc()) $stats['pendientes_asignacion'] = $row['total'] ?? 0;

        // Resueltos
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE id_Usuario_Encargado = $id_operario AND estado = 'resuelto'");
        if ($res && $row = $res->fetch_assoc()) $stats['resueltos'] = $row['total'] ?? 0;

        // Alta prioridad activos
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE id_Usuario_Encargado = $id_operario AND prioridad = 'a' AND estado != 'resuelto'");
        if ($res && $row = $res->fetch_assoc()) $stats['alta_prioridad'] = $row['total'] ?? 0;

        // Disponibles para asignar
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE id_Usuario_Encargado IS NULL AND estado = 'pendiente'");
        if ($res && $row = $res->fetch_assoc()) $stats['disponibles'] = $row['total'] ?? 0;

        return $stats;
    }

    /**
     * Obtiene tickets disponibles sin operario asignado.
     * @param int $limite Número máximo.
     * @return array Tickets disponibles.
     */
    public function obtener_tickets_disponibles($limite = 10) {
        $sql = "SELECT t.*, c.nombre as categoria_nombre, u.nombre as usuario_creador_nombre
                FROM Ticket t
                LEFT JOIN Categoria c ON t.id_Categoria = c.id
                LEFT JOIN Usuario u ON t.id_Usuario_Creador = u.id
                WHERE t.id_Usuario_Encargado IS NULL AND t.estado = 'pendiente'
                ORDER BY 
                    CASE WHEN t.prioridad = 'a' THEN 1
                         WHEN t.prioridad = 'm' THEN 2
                         ELSE 3 END ASC,
                    t.fecha_creacion ASC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Asigna un ticket a un operario.
     * @param string $id_ticket ID del ticket.
     * @param int $id_operario ID del operario.
     * @return bool Verdadero si se asignó.
     */
    public function asignar_ticket($id_ticket, $id_operario) {
        $estado = 'asignado';
        $fecha_asignacion = date('Y-m-d H:i:s');
        
        $stmt = $this->db->prepare("UPDATE Ticket SET id_Usuario_Encargado = ?, estado = ?, fecha_creacion = ? WHERE id = ?");
        $stmt->bind_param("isss", $id_operario, $estado, $fecha_asignacion, $id_ticket);
        return $stmt->execute();
    }

    /**
     * Busca tickets por título o descripción para un operario.
     * @param int $id_operario ID del operario.
     * @param string $termino Término de búsqueda.
     * @return array Tickets encontrados.
     */
    public function buscar_tickets($id_operario, $termino) {
        $termino = "%$termino%";
        $sql = "SELECT t.*, c.nombre as categoria_nombre, u.nombre as usuario_creador_nombre
                FROM Ticket t
                LEFT JOIN Categoria c ON t.id_Categoria = c.id
                LEFT JOIN Usuario u ON t.id_Usuario_Creador = u.id
                WHERE t.id_Usuario_Encargado = ? 
                AND (t.titulo LIKE ? OR t.descripcion LIKE ? OR t.id LIKE ?)
                ORDER BY t.fecha_creacion DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isss", $id_operario, $termino, $termino, $termino);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>
