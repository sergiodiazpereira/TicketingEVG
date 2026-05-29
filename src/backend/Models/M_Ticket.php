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
        if (!$resultado) return [];
        $tickets = $resultado->fetch_all(MYSQLI_ASSOC);
        
        try {
            require_once __DIR__ . '/M_Intranet.php';
            $m_intranet = new M_Intranet();
            $personal_intranet = $m_intranet->listar_personal();
            $personal_indexado = [];
            foreach ($personal_intranet as $p) {
                $personal_indexado[$p['id']] = $p;
            }
            foreach ($tickets as &$t) {
                $id_enc = (int)($t['id_usuario_encargado'] ?? 0);
                if ($id_enc > 0) {
                    if (isset($personal_indexado[$id_enc])) {
                        $t['encargado_nombre'] = $personal_indexado[$id_enc]['nombre'];
                    } else {
                        $t['encargado_nombre'] = 'TRABAJADOR ' . $id_enc;
                    }
                } else {
                    $t['encargado_nombre'] = null;
                }
                
                // Mapear el nombre del creador
                $id_cre = (int)($t['id_usuario_creador'] ?? 0);
                if ($id_cre > 0) {
                    if (isset($personal_indexado[$id_cre])) {
                        $t['creador_nombre'] = $personal_indexado[$id_cre]['nombre'];
                    } else {
                        $t['creador_nombre'] = 'USUARIO ' . $id_cre;
                    }
                } else {
                    $t['creador_nombre'] = null;
                }
            }
        } catch (Exception $e) {
            // Ignorar
        }
        
        return $tickets;
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
        $resultado = $stmt->get_result();
        if (!$resultado) return [];
        $tickets = $resultado->fetch_all(MYSQLI_ASSOC);
        
        try {
            require_once __DIR__ . '/M_Intranet.php';
            $m_intranet = new M_Intranet();
            $personal_intranet = $m_intranet->listar_personal();
            $personal_indexado = [];
            foreach ($personal_intranet as $p) {
                $personal_indexado[$p['id']] = $p;
            }
            foreach ($tickets as &$t) {
                $id_enc = (int)($t['id_usuario_encargado'] ?? 0);
                if ($id_enc > 0) {
                    if (isset($personal_indexado[$id_enc])) {
                        $t['encargado_nombre'] = $personal_indexado[$id_enc]['nombre'];
                    } else {
                        $t['encargado_nombre'] = 'TRABAJADOR ' . $id_enc;
                    }
                } else {
                    $t['encargado_nombre'] = null;
                }
                
                // Mapear el nombre del creador
                $id_cre = (int)($t['id_usuario_creador'] ?? 0);
                if ($id_cre > 0) {
                    if (isset($personal_indexado[$id_cre])) {
                        $t['creador_nombre'] = $personal_indexado[$id_cre]['nombre'];
                    } else {
                        $t['creador_nombre'] = 'USUARIO ' . $id_cre;
                    }
                } else {
                    $t['creador_nombre'] = null;
                }
            }
        } catch (Exception $e) {
            // Ignorar
        }
        
        return $tickets;
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
        if (empty($id_usuario_encargado) || $id_usuario_encargado === 'null' || $id_usuario_encargado == 0) {
            $stmt = $this->db->prepare("UPDATE Ticket SET id_usuario_encargado = NULL, estado = 'pendiente' WHERE id = ?");
            $stmt->bind_param("s", $id_ticket);
        } else {
            $stmt = $this->db->prepare("UPDATE Ticket SET id_usuario_encargado = ?, estado = 'asignado' WHERE id = ?");
            $stmt->bind_param("is", $id_usuario_encargado, $id_ticket);
        }
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
        if ($res && $t = $res->fetch_assoc()) {
            $tipo_prefijo = substr($t['id'], 0, 1) === 'I' ? 'incidencia' : 'peticion';
            $t['tipo'] = $tipo_prefijo;
            
            // Get categoria_nombre
            $stmt_cat = $this->db->prepare("SELECT nombre FROM Categoria WHERE id = ?");
            $stmt_cat->bind_param("i", $t['id_categoria']);
            $stmt_cat->execute();
            $res_cat = $stmt_cat->get_result();
            if ($res_cat && $row_cat = $res_cat->fetch_assoc()) {
                $t['categoria_nombre'] = $row_cat['nombre'];
            } else {
                $t['categoria_nombre'] = null;
            }
            
            // Get encargado_nombre and creador_nombre
            try {
                require_once __DIR__ . '/M_Intranet.php';
                $m_intranet = new M_Intranet();
                $personal_intranet = $m_intranet->listar_personal();
                $personal_indexado = [];
                foreach ($personal_intranet as $p) {
                    $personal_indexado[$p['id']] = $p;
                }
                
                $id_enc = (int)($t['id_usuario_encargado'] ?? 0);
                if ($id_enc > 0) {
                    if (isset($personal_indexado[$id_enc])) {
                        $t['encargado_nombre'] = $personal_indexado[$id_enc]['nombre'];
                    } else {
                        $t['encargado_nombre'] = 'TRABAJADOR ' . $id_enc;
                    }
                } else {
                    $t['encargado_nombre'] = null;
                }
                
                $id_cre = (int)($t['id_usuario_creador'] ?? 0);
                if ($id_cre > 0) {
                    if (isset($personal_indexado[$id_cre])) {
                        $t['creador_nombre'] = $personal_indexado[$id_cre]['nombre'];
                    } else {
                        $t['creador_nombre'] = 'USUARIO ' . $id_cre;
                    }
                } else {
                    $t['creador_nombre'] = null;
                }
            } catch (Exception $e) {
                $t['encargado_nombre'] = $id_enc > 0 ? 'TRABAJADOR ' . $id_enc : null;
                $t['creador_nombre'] = $id_cre > 0 ? 'USUARIO ' . $id_cre : null;
            }
            return $t;
        }
        return null;
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

	/**
	 * Obtiene los comentarios de un ticket mapeados con el nombre del usuario de la intranet.
	 * @param string $id_ticket ID del ticket.
	 * @return array Lista de comentarios.
	 */
	public function obtener_comentarios($id_ticket) {
		$stmt = $this->db->prepare("SELECT * FROM Comentario WHERE id_ticket = ? ORDER BY fecha_creacion ASC");
		$stmt->bind_param("s", $id_ticket);
		$stmt->execute();
		$resultado = $stmt->get_result();
		if (!$resultado)
			return [];
		$comentarios = $resultado->fetch_all(MYSQLI_ASSOC);

		try {
			require_once __DIR__ . '/M_Intranet.php';
			$m_intranet = new M_Intranet();
			$personal_intranet = $m_intranet->listar_personal();
			$personal_indexado = [];
			foreach ($personal_intranet as $p)
				$personal_indexado[$p['id']] = $p;
			foreach ($comentarios as &$c) {
				$id_usr = (int)$c['id_usuario'];
				if (isset($personal_indexado[$id_usr]))
					$c['usuario_nombre'] = $personal_indexado[$id_usr]['nombre'];
				else
					$c['usuario_nombre'] = 'Usuario ' . $id_usr;
			}
		} catch (Exception $e) {
			// Ignorar y dejar solo ids
		}
		return $comentarios;
	}

	/**
	 * Crea un nuevo comentario para un ticket.
	 * @param string $id_ticket ID del ticket.
	 * @param int $id_usuario ID del usuario creador del comentario.
	 * @param string $texto Texto del comentario.
	 * @return bool True si se insertó correctamente, false en caso contrario.
	 */
	public function crear_comentario($id_ticket, $id_usuario, $texto) {
		$stmt = $this->db->prepare("INSERT INTO Comentario (id_ticket, id_usuario, texto) VALUES (?, ?, ?)");
		$stmt->bind_param("sis", $id_ticket, $id_usuario, $texto);
		return $stmt->execute();
	}
}
?>

