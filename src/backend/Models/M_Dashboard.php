<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Sergio Díaz Pereira
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Modelo para gestionar Dashboard Admin.
 */

require_once __DIR__ . '/../config/Conexion.php';

class M_Dashboard {
    private $db;

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    /**
     * Recopila todas las estadísticas necesarias para el dashboard.
     * @return array
     */
    public function obtenerDatos() {
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

        $res_visitas = $this->db->query("SELECT SUM(visitas_totales) as total FROM Usuario");
        if ($res_visitas && $row = $res_visitas->fetch_assoc()) $stats['total_visitas'] = ($row['total'] ?? 0);


        $res_usuarios = $this->db->query("SELECT COUNT(*) as total FROM Usuario 
                                          INNER JOIN Rol ON usuario.id_Rol = rol.id
                                          WHERE rol.nombre IN ('Responsable', 'Trabajador')");
        if ($res_usuarios && $row = $res_usuarios->fetch_assoc()) $stats['total_usuarios'] = ($row['total'] ?? 0);


        $res_categorias = $this->db->query("SELECT COUNT(*) as total FROM Categoria");
        if ($res_categorias && $row = $res_categorias->fetch_assoc()) $stats['total_categorias'] = ($row['total'] ?? 0);


        $res_activos = $this->db->query("SELECT COUNT(*) as total FROM Ticket 
                                         WHERE estado != 'resuelto'");
        if ($res_activos && $row = $res_activos->fetch_assoc()) $stats['tickets_activos'] = ($row['total'] ?? 0);
        

        $res_totales = $this->db->query("SELECT COUNT(*) as total FROM Ticket");
        if ($res_totales && $row = $res_totales->fetch_assoc()) $stats['total_tickets'] = ($row['total'] ?? 0);
        

        $res_resueltos = $this->db->query("SELECT COUNT(*) as total FROM Ticket 
                                           WHERE estado = 'resuelto'");
        if ($res_resueltos && $row = $res_resueltos->fetch_assoc()) $stats['tickets_resueltos'] = ($row['total'] ?? 0);
        

        $res_p_alta = $this->db->query("SELECT COUNT(*) as total FROM Ticket 
                                        WHERE prioridad = 'a' AND estado != 'resuelto'");
        if ($res_p_alta && $row = $res_p_alta->fetch_assoc()) $stats['prioridad_alta'] = ($row['total'] ?? 0);
        

        $res_p_media = $this->db->query("SELECT COUNT(*) as total FROM Ticket
                                         WHERE prioridad = 'm' AND estado != 'resuelto'");
        if ($res_p_media && $row = $res_p_media->fetch_assoc()) $stats['prioridad_media'] = ($row['total'] ?? 0);
        

        $res_p_baja = $this->db->query("SELECT COUNT(*) as total FROM Ticket
                                        WHERE prioridad = 'b' AND estado != 'resuelto'");
        if ($res_p_baja && $row = $res_p_baja->fetch_assoc()) $stats['prioridad_baja'] = ($row['total'] ?? 0);
        
        // Operarios disponibles (sin tickets asignados en proceso)
        $sql_operarios = "SELECT COUNT(usuario.id) as total 
                         FROM Usuario 
                         JOIN Rol ON usuario.id_Rol = rol.id 
                         WHERE rol.nombre IN ('Responsable', 'Trabajador') 
                         AND usuario.id NOT IN (
                            SELECT id_Usuario_Encargado 
                            FROM Ticket 
                            WHERE id_Usuario_Encargado IS NOT NULL 
                            AND estado IN ('asignado', 'proceso')
                         )";
        $res_disponibles = $this->db->query($sql_operarios);
        if ($res_disponibles && $row = $res_disponibles->fetch_assoc()) $stats['operarios_disponibles'] = ($row['total'] ?? 0);

        return $stats;
    }
}
?>