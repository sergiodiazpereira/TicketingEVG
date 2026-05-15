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
}
?>
