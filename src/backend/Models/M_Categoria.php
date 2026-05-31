<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Modelo para gestionar las Categorías de la base de datos.
 */

require_once __DIR__ . '/../config/Conexion.php';

class M_Categoria {
	private $db;

	public function __construct() {
		$this->db = Conexion::conectar();
	}

	/**
	 * Obtiene la lista completa de categorías con conteo de operarios y tickets.
	 * @return array
	 */
	public function listar_categorias() {
		$sql = "SELECT c.id, c.nombre, c.descripcion,
			    (SELECT COUNT(*) FROM Categoria_Usuario cu WHERE cu.id_categoria = c.id) as operarios,
			    (SELECT COUNT(*) FROM Ticket t WHERE t.id_categoria = c.id) as tickets
				FROM Categoria c";
		$resultado = $this->db->query($sql);
		return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
	}

	/**
	 * Crea una nueva categoría.
	 * @param array $datos Datos de la categoría (nombre).
	 * @return int|false ID insertado o false.
	 */
	public function crear($datos) {
		if (empty($datos['nombre']))
			return false;

		$descripcion = !empty($datos['descripcion']) ? $datos['descripcion'] : null;

		$stmt = $this->db->prepare("INSERT INTO Categoria (nombre, descripcion) VALUES (?, ?)");
		$stmt->bind_param("ss", $datos['nombre'], $descripcion);
		if ($stmt->execute())
			return $this->db->insert_id;
		return false;
	}

	/**
	 * Actualiza el nombre de una categoría.
	 * @param int $id ID de la categoría.
	 * @param array $datos Datos a actualizar.
	 * @return bool
	 */
	public function actualizar($id, $datos) {
		if (empty($datos['nombre']))
			return false;

		$descripcion = !empty($datos['descripcion']) ? $datos['descripcion'] : null;

		$stmt = $this->db->prepare("UPDATE Categoria SET nombre = ?, descripcion = ? WHERE id = ?");
		$stmt->bind_param("ssi", $datos['nombre'], $descripcion, $id);
		return $stmt->execute();
	}

	/**
	 * Elimina una categoría si no tiene operarios ni tickets asociados.
	 * @param int $id ID de la categoría.
	 * @return array Resultado con status y message.
	 */
	public function eliminar($id) {
		// Comprobar si hay operarios asociados a esta categoría
		$stmt_op = $this->db->prepare("SELECT COUNT(*) as total FROM Categoria_Usuario WHERE id_categoria = ?");
		$stmt_op->bind_param("i", $id);
		$stmt_op->execute();
		$res_op = $stmt_op->get_result()->fetch_assoc();

		if ($res_op['total'] > 0)
			return ['status' => 'error', 'message' => 'No se puede eliminar: la categoría tiene operarios asociados.'];

		// Comprobar si hay tickets asociados a esta categoría
		$stmt = $this->db->prepare("SELECT COUNT(*) as total FROM Ticket WHERE id_Categoria = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$res = $stmt->get_result()->fetch_assoc();

		if ($res['total'] > 0)
			return ['status' => 'error', 'message' => 'No se puede eliminar: la categoría tiene tickets asociados.'];

		// Primero eliminar las relaciones con los usuarios operarios
		$stmt_rel = $this->db->prepare("DELETE FROM Categoria_Usuario WHERE id_Categoria = ?");
		$stmt_rel->bind_param("i", $id);
		$stmt_rel->execute();

		// Finalmente eliminar la categoría
		$stmt_cat = $this->db->prepare("DELETE FROM Categoria WHERE id = ?");
		$stmt_cat->bind_param("i", $id);
		if ($stmt_cat->execute())
			return ['status' => 'success', 'message' => 'Categoría eliminada correctamente.'];
		return ['status' => 'error', 'message' => 'Error al eliminar la categoría.'];
	}
}
?>
