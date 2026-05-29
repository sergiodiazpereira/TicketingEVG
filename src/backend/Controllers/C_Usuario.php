<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para las peticiones de Usuarios y sincronización de Operarios.
 */

require_once __DIR__ . '/../Models/M_Usuario.php';

class C_Usuario {
	private $modelo;

	public function __construct() {
		$this->modelo = new M_Usuario();
	}

	/**
	 * Obtiene la lista de operarios del sistema.
	 * @return array
	 */
	public function listar_operarios() {
		return $this->modelo->listar_operarios();
	}

	/**
	 * Obtiene la plantilla del personal de la Intranet que no está registrada en Ticketing.
	 * @return array
	 */
	public function listar_personal_intranet() {
		return $this->modelo->listar_personal_intranet_no_registrado();
	}

	/**
	 * Obtiene estadísticas globales de los usuarios.
	 * @return array
	 */
	public function get_estadisticas() {
		return $this->modelo->get_estadisticas();
	}

	/**
	 * Crea o actualiza un operario determinando su existencia previa en local.
	 * También gestiona la asignación de categorías.
	 * @param array $datos Datos del formulario enviados por POST/PUT.
	 * @return array
	 */
	public function guardar($datos) {
		if (empty($datos['id']) || empty($datos['rol']))
			return ['status' => 'error', 'message' => 'Faltan campos obligatorios (id, rol).'];

		$id_usuario = (int) $datos['id'];
		$categorias = $datos['categorias'] ?? [];

		// Comprobamos si ya está registrado localmente para decidir si actualizar o crear
		$usuario_existente = $this->modelo->buscar_por_id($id_usuario);

		if ($usuario_existente) {
			// Actualizar operario existente (cambiar su rol local)
			$ok = $this->modelo->actualizar($id_usuario, $datos);
			if (!$ok)
				return ['status' => 'error', 'message' => 'Error al actualizar el operario. Comprueba que el rol es válido.'];
			$this->modelo->asignar_categorias($id_usuario, $categorias);
			return ['status' => 'success', 'message' => 'Operario actualizado correctamente.'];
		}

		// Registrar nuevo operario localmente usando su ID de la Intranet
		$nuevo_id = $this->modelo->crear($datos);
		if (!$nuevo_id)
			return ['status' => 'error', 'message' => 'Error al crear el operario. Comprueba que el rol es válido.'];
		$this->modelo->asignar_categorias($nuevo_id, $categorias);
		return ['status' => 'success', 'id' => $nuevo_id, 'message' => 'Operario creado correctamente.'];
	}

	/**
	 * Elimina un operario por su ID.
	 * @param int $id ID del operario a eliminar.
	 * @return array
	 */
	public function borrar($id) {
		if (!$id)
			return ['status' => 'error', 'message' => 'ID de operario no proporcionado.'];
		return $this->modelo->eliminar((int) $id);
	}
}
?>
