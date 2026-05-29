<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Modelo para gestionar los Usuarios/Operarios y sincronización con Intranet.
 */

require_once __DIR__ . '/../config/Conexion.php';

class M_Usuario {
	private $db;

	public function __construct() {
		$this->db = Conexion::conectar();
	}

	public function listar_operarios() {
		try {
			$sql = "SELECT u.id, LOWER(r.nombre) as rol,
				   (SELECT COUNT(*) FROM Categoria_Usuario cu WHERE cu.id_usuario = u.id) as num_categorias,
				   (SELECT GROUP_CONCAT(c.nombre SEPARATOR ', ') FROM Categoria_Usuario cu JOIN Categoria c ON cu.id_categoria = c.id WHERE cu.id_usuario = u.id) as categorias_nombres,
				   (SELECT COUNT(*) FROM Ticket t WHERE t.id_usuario_encargado = u.id AND t.estado != 'resuelto') as tickets_asignados
				FROM Usuario u 
				JOIN Rol r ON u.id_rol = r.id
				WHERE LOWER(r.nombre) IN ('responsable', 'trabajador', 'operario')";
			$resultado = $this->db->query($sql);
			if (!$resultado) return [];
			
			$operarios = $resultado->fetch_all(MYSQLI_ASSOC);

			// Ahora sacamos el personal de la Intranet para asignar nombre y email
			require_once __DIR__ . '/M_Intranet.php';
			$m_intranet = new M_Intranet();
			$personal_intranet = $m_intranet->listar_personal();
			
			$personal_indexado = [];
			foreach ($personal_intranet as $p) {
				$personal_indexado[$p['id']] = $p;
			}

			foreach ($operarios as &$op) {
				$id = (int)$op['id'];
				if (isset($personal_indexado[$id])) {
					$op['nombre'] = $personal_indexado[$id]['nombre'];
					$op['email'] = $personal_indexado[$id]['correo'];
				} else {
					$op['nombre'] = 'TRABAJADOR-' . $id;
					$op['email'] = '';
				}
			}

			usort($operarios, function($a, $b) {
				return strcasecmp($a['nombre'], $b['nombre']);
			});

			return $operarios;
		} catch (Exception $e) {
			return ['error' => 'Error al listar operarios: ' . $e->getMessage()];
		}
	}
	
	/**
	 * Recopila estadísticas generales de tickets y usuarios.
	 * @return array
	 */
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
		if ($res && $row = $res->fetch_assoc())
			$stats['total_visitas'] = $row['total'] ?? 0;

		$res = $this->db->query("SELECT COUNT(*) as total FROM Usuario");
		if ($res && $row = $res->fetch_assoc())
			$stats['total_usuarios'] = $row['total'] ?? 0;

		$res = $this->db->query("SELECT COUNT(*) as total FROM Categoria");
		if ($res && $row = $res->fetch_assoc())
			$stats['total_categorias'] = $row['total'] ?? 0;

		$res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE estado != 'resuelto'");
		if ($res && $row = $res->fetch_assoc())
			$stats['tickets_activos'] = $row['total'] ?? 0;
		
		$res = $this->db->query("SELECT COUNT(*) as total FROM Ticket");
		if ($res && $row = $res->fetch_assoc())
			$stats['total_tickets'] = $row['total'] ?? 0;
		
		$res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE estado = 'resuelto'");
		if ($res && $row = $res->fetch_assoc())
			$stats['tickets_resueltos'] = $row['total'] ?? 0;
		
		$res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE prioridad = 'a' AND estado != 'resuelto'");
		if ($res && $row = $res->fetch_assoc())
			$stats['prioridad_alta'] = $row['total'] ?? 0;
		
		$res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE prioridad = 'm' AND estado != 'resuelto'");
		if ($res && $row = $res->fetch_assoc())
			$stats['prioridad_media'] = $row['total'] ?? 0;
		
		$res = $this->db->query("SELECT COUNT(*) as total FROM Ticket WHERE prioridad = 'b' AND estado != 'resuelto'");
		if ($res && $row = $res->fetch_assoc())
			$stats['prioridad_baja'] = $row['total'] ?? 0;
		
		// Operarios disponibles (sin tickets asignados en proceso)
		$sql = "SELECT COUNT(u.id) as total FROM Usuario u JOIN Rol r ON u.id_rol = r.id WHERE r.nombre IN ('responsable', 'trabajador', 'operario') AND u.id NOT IN (SELECT id_usuario_encargado FROM Ticket WHERE id_usuario_encargado IS NOT NULL AND estado IN ('asignado', 'proceso'))";
		$res = $this->db->query($sql);
		if ($res && $row = $res->fetch_assoc())
			$stats['operarios_disponibles'] = $row['total'] ?? 0;

		return $stats;
	}

	/**
	 * Obtiene el id_Rol a partir del nombre del rol.
	 * Devuelve null si es el rol genérico 'profesor' (solicitante) u otro inexistente.
	 * @param string $nombre_rol Nombre del rol (responsable, trabajador...).
	 * @return int|null
	 */
	private function obtener_id_rol($nombre_rol) {
		if (empty($nombre_rol) || strtolower($nombre_rol) === 'profesor')
			return null;
		$stmt = $this->db->prepare("SELECT id FROM Rol WHERE LOWER(nombre) = ?");
		$stmt->bind_param("s", $nombre_rol);
		$stmt->execute();
		$res = $stmt->get_result();
		if ($res && $row = $res->fetch_assoc())
			return (int) $row['id'];
		return null;
	}

	/**
	 * Crea un nuevo usuario en el sistema con el ID provisto por la Intranet y rol opcional.
	 * @param array $datos Datos del formulario (id, rol).
	 * @return int|false ID insertado o false en caso de error.
	 */
	public function crear($datos) {
		$id_rol = $this->obtener_id_rol(strtolower($datos['rol'] ?? ''));
		if (empty($datos['id']))
			return false;

		if ($id_rol === null) {
			$stmt = $this->db->prepare(
				"INSERT INTO Usuario (id, id_rol) VALUES (?, NULL)"
			);
			$stmt->bind_param("i", $datos['id']);
		} else {
			$stmt = $this->db->prepare(
				"INSERT INTO Usuario (id, id_rol) VALUES (?, ?)"
			);
			$stmt->bind_param("ii", $datos['id'], $id_rol);
		}

		if ($stmt->execute())
			return (int) $datos['id'];
		return false;
	}

	/**
	 * Busca un usuario local por su ID de la intranet.
	 * @param int $id
	 * @return array|null
	 */
	public function buscar_por_id($id) {
		$stmt = $this->db->prepare("SELECT u.*, LOWER(r.nombre) as rol FROM Usuario u LEFT JOIN Rol r ON u.id_rol = r.id WHERE u.id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$res = $stmt->get_result();
		if ($res && $row = $res->fetch_assoc())
			return $row;
		return null;
	}

	/**
	 * Actualiza los datos de un usuario existente (únicamente el rol, admitiendo NULL).
	 * @param int $id ID del usuario.
	 * @param array $datos Datos a actualizar (rol).
	 * @return bool
	 */
	public function actualizar($id, $datos) {
		$id_rol = $this->obtener_id_rol(strtolower($datos['rol'] ?? ''));

		if ($id_rol === null) {
			$stmt = $this->db->prepare(
				"UPDATE Usuario SET id_rol = NULL WHERE id = ?"
			);
			$stmt->bind_param("i", $id);
		} else {
			$stmt = $this->db->prepare(
				"UPDATE Usuario SET id_rol = ? WHERE id = ?"
			);
			$stmt->bind_param("ii", $id_rol, $id);
		}
		return $stmt->execute();
	}

	/**
	 * Elimina un usuario si no tiene tickets activos asignados.
	 * @param int $id ID del usuario.
	 * @return array Resultado con status y message.
	 */
	public function eliminar($id) {
		// Comprobar tickets activos asignados antes de eliminar
		$stmt = $this->db->prepare(
			"SELECT COUNT(*) as total FROM Ticket WHERE id_usuario_encargado = ? AND estado != 'resuelto'"
		);
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$res = $stmt->get_result()->fetch_assoc();

		if ($res['total'] > 0)
			return ['status' => 'error', 'message' => 'No se puede eliminar: el operario tiene tickets activos asignados.'];

		// Primero eliminar las relaciones de categorías
		$stmt_cat = $this->db->prepare("DELETE FROM Categoria_Usuario WHERE id_usuario = ?");
		$stmt_cat->bind_param("i", $id);
		$stmt_cat->execute();

		// En lugar de borrar el usuario de la BBDD local (lo que rompería la integridad de los tickets creados),
		// simplemente le quitamos el rol de operario (id_rol = NULL), dejándolo como usuario básico ("profesor").
		$stmt_usr = $this->db->prepare("UPDATE Usuario SET id_rol = NULL WHERE id = ?");
		$stmt_usr->bind_param("i", $id);
		if ($stmt_usr->execute())
			return ['status' => 'success', 'message' => 'Rol de operario revocado correctamente.'];
		return ['status' => 'error', 'message' => 'Error al revocar los permisos de operario.'];
	}

	/**
	 * Reemplaza las categorías asignadas a un usuario.
	 * Borra las existentes e inserta las nuevas.
	 * @param int $id_usuario ID del usuario.
	 * @param array $ids_categorias Array de IDs de categorías seleccionadas.
	 * @return bool
	 */
	public function asignar_categorias($id_usuario, $ids_categorias) {
		// Borrar asignaciones anteriores
		$stmt = $this->db->prepare("DELETE FROM Categoria_Usuario WHERE id_usuario = ?");
		$stmt->bind_param("i", $id_usuario);
		$stmt->execute();

		if (empty($ids_categorias))
			return true;

		// Insertar las nuevas asignaciones
		$stmt_ins = $this->db->prepare(
			"INSERT INTO Categoria_Usuario (id_usuario, id_categoria) VALUES (?, ?)"
		);
		foreach ($ids_categorias as $id_cat) {
			$stmt_ins->bind_param("ii", $id_usuario, $id_cat);
			$stmt_ins->execute();
		}
		return true;
	}

	/**
	 * Obtiene la plantilla del personal de la Intranet que aún no está registrada en Ticketing,
	 * consultando directamente la base de datos de la Intranet mediante M_Intranet.
	 * @return array
	 */
	public function listar_personal_intranet_no_registrado() {
		require_once __DIR__ . '/M_Intranet.php';
		$m_intranet = new M_Intranet();
		$usuarios_api = $m_intranet->listar_personal();

		if (!empty($usuarios_api)) {
			$res_local = $this->db->query("SELECT id, id_rol FROM Usuario");
			$registrados_con_rol = [];
			$registrados_sin_rol = [];

			if ($res_local) {
				while ($row = $res_local->fetch_assoc()) {
					if ($row['id_rol'] === null) {
						$registrados_sin_rol[] = (int) $row['id'];
					} else {
						$registrados_con_rol[] = (int) $row['id'];
					}
				}
			}

			$disponibles = [];
			foreach ($usuarios_api as $u) {
				$id_u = $u['id'];
				
				// Si ya tiene un rol asignado en Ticketing (ya es operario responsable/trabajador), lo omitimos
				if (in_array($id_u, $registrados_con_rol)) {
					continue;
				}
				
				// Si está en Ticketing pero con rol NULL (es profesor/usuario normal), lo incluimos
				if (in_array($id_u, $registrados_sin_rol)) {
					$disponibles[] = $u;
					continue;
				}
				
				// Si no está en Ticketing (nuevo usuario), solo lo incluimos si es tipo_personal_id = 3 (Servicio)
				if (isset($u['tipo_personal_id']) && $u['tipo_personal_id'] == 3) {
					$disponibles[] = $u;
				}
			}

			return $disponibles;
		}

		return [];
	}
}
?>
