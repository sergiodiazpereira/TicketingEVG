<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para las peticiones de Usuarios.
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
     * Obtiene estadísticas globales de los usuarios.
     * @return array
     */
    public function get_estadisticas() {
        return $this->modelo->get_estadisticas();
    }

    /**
     * Crea o actualiza un operario según si existe el campo 'id'.
     * También gestiona la asignación de categorías.
     * @param array $datos Datos del formulario enviados por POST/PUT.
     * @return array
     */
    public function guardar($datos) {
        if (empty($datos['nombre']) || empty($datos['correo']) || empty($datos['rol']))
            return ['status' => 'error', 'message' => 'Faltan campos obligatorios (nombre, correo, rol).'];

        $categorias = $datos['categorias'] ?? [];

        if (!empty($datos['id'])) {
            // Actualizar operario existente
            $ok = $this->modelo->actualizar((int) $datos['id'], $datos);
            if (!$ok)
                return ['status' => 'error', 'message' => 'Error al actualizar el operario. Comprueba que el rol es válido.'];
            $this->modelo->asignar_categorias((int) $datos['id'], $categorias);
            return ['status' => 'success', 'message' => 'Operario actualizado correctamente.'];
        }

        // Crear nuevo operario
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
