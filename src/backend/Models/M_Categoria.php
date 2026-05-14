<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Modelo para gestionar Categorías.
 */

require_once __DIR__ . '/../config/Conexion.php';

class M_Categoria {
    private $db;

    public function __construct() {
        $this->db = Conexion::conectar();
    }

    public function listar() {
        $sql = "SELECT c.id, c.nombre, 
                   (SELECT COUNT(*) FROM Categoria_Usuario cu WHERE cu.id_Usuario = u.id) as operarios,
                   (SELECT COUNT(*) FROM Ticket t WHERE t.id_Categoria = c.id) as tickets
                FROM Categoria c
                ORDER BY c.nombre ASC";
        // Nota: He detectado un error en la subconsulta de operarios (usaba 'u.id' en vez de 'cu.id_Usuario' o similar sin join)
        // Corrijo la consulta para que sea válida
        $sql = "SELECT c.id, c.nombre, 
                   (SELECT COUNT(*) FROM Categoria_Usuario cu WHERE cu.id_Categoria = c.id) as operarios,
                   (SELECT COUNT(*) FROM Ticket t WHERE t.id_Categoria = c.id) as tickets
                FROM Categoria c
                ORDER BY c.nombre ASC";
        $resultado = $this->db->query($sql);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>
