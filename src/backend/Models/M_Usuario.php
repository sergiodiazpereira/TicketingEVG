<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Modelo para gestionar los Usuarios/Operarios.
 */

require_once __DIR__ . '/../config/Conexion.php';

class M_Usuario {
    private $db;

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    public function listar_operarios() {
        $sql = "SELECT u.id, u.nombre, u.correo, r.nombre as rol,
                   (SELECT COUNT(*) FROM Categoria_Usuario cu WHERE cu.id_Usuario = u.id) as num_categorias,
                   (SELECT COUNT(*) FROM Ticket t WHERE t.id_Usuario_Encargado = u.id AND t.estado != 'resuelto') as tickets_asignados
                FROM Usuario u 
                JOIN Rol r ON u.id_Rol = r.id
                WHERE LOWER(r.nombre) IN ('responsable', 'trabajador', 'operario')
                ORDER BY u.nombre ASC";
        $resultado = $this->db->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function get_estadisticas() {
        $stats = [
            'total_visitas' => 0,
            'total_usuarios' => 0,
            'total_categorias' => 0,
            'tickets_activos' => 0,
            'operarios_disponibles' => 0,
            'tickets_resueltos' => 0,
            'total_tickets' => 0,
            'prioridad_alta' => 0,
            'prioridad_media' => 0,
            'prioridad_baja' => 0
        ];

        $res = $this->db->query("SELECT SUM(visitas_totales) as total FROM Usuario");
        if ($res && $row = $res->fetch_assoc()) $stats['total_visitas'] = $row['total'] ?? 0;

        $res = $this->db->query("SELECT COUNT(*) as total FROM Usuario");
        if ($res && $row = $res->fetch_assoc()) $stats['total_usuarios'] = $row['total'] ?? 0;

        $res = $this->db->query("SELECT COUNT(*) as total FROM Categoria");
        if ($res && $row = $res->fetch_assoc()) $stats['total_categorias'] = $row['total'] ?? 0;

        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE estado != 'resuelto'");
        if ($res && $row = $res->fetch_assoc()) $stats['tickets_activos'] = $row['total'] ?? 0;
        
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket");
        if ($res && $row = $res->fetch_assoc()) $stats['total_tickets'] = $row['total'] ?? 0;
        
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE estado = 'resuelto'");
        if ($res && $row = $res->fetch_assoc()) $stats['tickets_resueltos'] = $row['total'] ?? 0;
        
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE prioridad = 'a' AND estado != 'resuelto'");
        if ($res && $row = $res->fetch_assoc()) $stats['prioridad_alta'] = $row['total'] ?? 0;
        
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE prioridad = 'm' AND estado != 'resuelto'");
        if ($res && $row = $res->fetch_assoc()) $stats['prioridad_media'] = $row['total'] ?? 0;
        
        $res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE prioridad = 'b' AND estado != 'resuelto'");
        if ($res && $row = $res->fetch_assoc()) $stats['prioridad_baja'] = $row['total'] ?? 0;
        
        // Operarios disponibles (sin tickets asignados en proceso)
        $sql = "SELECT COUNT(u.id) as total FROM Usuario u JOIN Rol r ON u.id_Rol = r.id WHERE r.nombre IN ('responsable', 'trabajador', 'operario') AND u.id NOT IN (SELECT id_Usuario_Encargado FROM Ticket WHERE id_Usuario_Encargado IS NOT NULL AND estado IN ('asignado', 'proceso'))";
        $res = $this->db->query($sql);
        if ($res && $row = $res->fetch_assoc()) $stats['operarios_disponibles'] = $row['total'] ?? 0;

        return $stats;
    }
}
?>
