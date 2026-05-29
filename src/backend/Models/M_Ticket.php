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
     * Obtiene todos los tickets registrados con el nombre de su categoría y tipo derivado.
     * @return array Lista de tickets.
     */
    public function listar() {
        $sql = "SELECT t.*, 
                       CASE WHEN t.id LIKE 'I%' THEN 'incidencia' ELSE 'peticion' END AS tipo,
                       c.nombre AS categoria_nombre 
                FROM Ticket t 
                LEFT JOIN Categoria c ON t.id_categoria = c.id 
                ORDER BY t.fecha_creacion DESC";
        $resultado = $this->db->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Obtiene los tickets creados por un usuario específico con el nombre de su categoría y tipo derivado.
     * @param int $id_usuario ID del creador.
     * @return array Lista de tickets filtrada.
     */
    public function listar_por_usuario($id_usuario) {
        $sql = "SELECT t.*, 
                       CASE WHEN t.id LIKE 'I%' THEN 'incidencia' ELSE 'peticion' END AS tipo,
                       c.nombre AS categoria_nombre 
                FROM Ticket t 
                LEFT JOIN Categoria c ON t.id_categoria = c.id 
                WHERE t.id_usuario_creador = ? 
                ORDER BY t.fecha_creacion DESC";
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
        $tipo_prefijo = ($datos['tipo'] === 'incidencia') ? 'I' : 'PS';
        $fecha_actual = date('dmy');
        
        $id_categoria = $datos['id_categoria'] ?? $datos['id_Categoria'] ?? 1;
        $id_usuario_creador = $datos['id_usuario_creador'] ?? $datos['id_Usuario_Creador'] ?? 1;
        
        $cat_id = str_pad($id_categoria, 2, '0', STR_PAD_LEFT);

        // Contar tickets del mismo tipo y día para el autoincremento manual
        $patron = $tipo_prefijo . $fecha_actual . $cat_id . '%';
        $stmt_contar = $this->db->prepare("SELECT COUNT(*) as total FROM Ticket WHERE id LIKE ?");
        $stmt_contar->bind_param("s", $patron);
        $stmt_contar->execute();
        $total = $stmt_contar->get_result()->fetch_assoc()['total'];
        $inc_id = str_pad($total + 1, 2, '0', STR_PAD_LEFT);

        $id_nuevo = $tipo_prefijo . $fecha_actual . $cat_id . $inc_id;

        $ubicacion = $datos['ubicacion'] ?? null;
        $fecha_prevista = $datos['fecha_prevista'] ?? null;
        $id_usuario_encargado = $datos['id_usuario_encargado'] ?? null;
        $estado = $id_usuario_encargado ? 'asignado' : ($datos['estado'] ?? 'pendiente');

        $sql = "INSERT INTO Ticket (id, id_categoria, titulo, descripcion, prioridad, id_usuario_creador, estado, ubicacion, fecha_prevista, id_usuario_encargado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        // Los tipos son: String, Integer, String, String, String, Integer, String, String, String, Integer
        $stmt->bind_param("sisssisssi", 
            $id_nuevo, 
            $id_categoria, 
            $datos['titulo'], 
            $datos['descripcion'], 
            $datos['prioridad'], 
            $id_usuario_creador,
            $estado,
            $ubicacion,
            $fecha_prevista,
            $id_usuario_encargado
        );

        if ($stmt->execute()) {
            return $id_nuevo;
        }
        return false;
    }

    /**
     * Asigna un operario a un ticket y cambia su estado a asignado.
     * @param string $id_ticket
     * @param int $id_usuario_encargado
     */
    public function asignar_operario($id_ticket, $id_usuario_encargado) {
        $stmt = $this->db->prepare("UPDATE Ticket SET id_usuario_encargado = ?, estado = 'asignado' WHERE id = ?");
        $stmt->bind_param("is", $id_usuario_encargado, $id_ticket);
        return $stmt->execute();
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
     * Busca un ticket por su ID.
     * @param string $id
     * @return array|null
     */
    public function buscar_por_id($id) {
        $stmt = $this->db->prepare("SELECT * FROM Ticket WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : null;
    }

    /**
     * Actualiza los datos generales de un ticket.
     * @param string $id ID del ticket.
     * @param array $datos Datos a actualizar.
     */
    public function actualizar($id, $datos) {
        $id_categoria = $datos['id_categoria'] ?? 1;
        $ubicacion = $datos['ubicacion'] ?? null;
        $sql = "UPDATE Ticket SET id_categoria = ?, titulo = ?, descripcion = ?, prioridad = ?, ubicacion = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isssss", 
            $id_categoria, 
            $datos['titulo'], 
            $datos['descripcion'], 
            $datos['prioridad'], 
            $ubicacion,
            $id
        );
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
