<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Sergio Díaz Pereira
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para gestionar las peticiones de Categorías.
 */

require_once __DIR__ . '/../Models/M_Categoria.php';

class C_Categoria {
	private $modelo;

	public function __construct() {
		$this->modelo = new M_Categoria();
	}

	/**
	 * Obtiene todas las categorías de la base de datos.
	 * @return array
	 */
	public function listar() {
		return $this->modelo->listar_categorias();
	}

	/**
	 * Crea o actualiza una categoría según si existe el campo 'id'.
	 * @param array $datos Datos recibidos en formato JSON/Array.
	 * @return array
	 */
	public function guardar($datos) {
		if (empty($datos['nombre'])) // Verificacion de nombre vacío
			return ['status' => 'error', 'message' => 'Falta el nombre de la categoría.']; 

		if (strlen($datos['nombre']) > 50) // Verificacion de longitud del nombre
			return ['status' => 'error', 'message' => 'El nombre de la categoría no puede superar los 50 caracteres.'];

		if (isset($datos['descripcion']) && strlen($datos['descripcion']) > 100) // Verificacion de longitud de la descripcion
			return ['status' => 'error', 'message' => 'La descripción no puede superar los 100 caracteres.']; 

		if (!empty($datos['id'])) {
			// Actualizar categoría existente
			$ok = $this->modelo->actualizar((int) $datos['id'], $datos);
			if (!$ok)
				return ['status' => 'error', 'message' => 'Error al actualizar la categoría.'];
			return ['status' => 'success', 'message' => 'Categoría actualizada correctamente.'];
		}

		// Crear nueva categoría
		$nuevo_id = $this->modelo->crear($datos);
		if (!$nuevo_id)
			return ['status' => 'error', 'message' => 'Error al crear la categoría.'];
		return ['status' => 'success', 'id' => $nuevo_id, 'message' => 'Categoría creada correctamente.'];
	}

	/**
	 * Elimina una categoría por su ID.
	 * @param int $id ID de la categoría a eliminar.
	 * @return array
	 */
	public function borrar($id) {
		if (!$id)
			return ['status' => 'error', 'message' => 'ID de categoría no especificado.'];
		return $this->modelo->eliminar((int) $id);
	}
}
?>
