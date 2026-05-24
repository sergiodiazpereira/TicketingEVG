<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Enrutador principal de la API. Dirige las peticiones al controlador adecuado.
 */

require_once __DIR__ . '/Controllers/C_Ticket.php';
require_once __DIR__ . '/Controllers/C_Usuario.php';
require_once __DIR__ . '/Controllers/C_Dashboard.php';
require_once __DIR__ . '/Controllers/C_Categoria.php';
require_once __DIR__ . '/Controllers/C_Operario.php';
require_once __DIR__ . '/Views/V_Ticket.php';
require_once __DIR__ . '/Views/V_Usuario.php';
require_once __DIR__ . '/Views/V_Categoria.php';
require_once __DIR__ . '/Views/V_Operario.php';

// Cabeceras CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

// Capturar el método y la acción de la URL
$metodo = $_SERVER['REQUEST_METHOD'];
$entidad = $_GET['entidad'] ?? 'ticket'; // Por defecto ticket para mantener retrocompatibilidad
$accion = $_GET['accion'] ?? '';

// Manejo de peticiones OPTIONS (Preflight para CORS)
if ($metodo === 'OPTIONS') {
    V_Ticket::responder(["status" => "ok"]);
}

// Enrutamiento principal por entidad
switch ($entidad) {
    case 'dashboard':
        $controlador = new C_Dashboard();
        if ($accion === 'obtener_datos') {
            $respuesta = $controlador->obtenerDatos();
            V_Ticket::responder($respuesta);
        } else {
            V_Ticket::responder(["error" => "Acción no reconocida para dashboard"]);
        }
        break;
        
    case 'usuario':
        $controlador = new C_Usuario();
        if ($accion === 'listar_operarios') {
            $respuesta = $controlador->listar_operarios();
            V_Usuario::responder($respuesta);
        } else if ($accion === 'estadisticas') {
            $respuesta = $controlador->get_estadisticas();
            V_Usuario::responder($respuesta);
        } else if ($accion === 'guardar' && ($metodo === 'POST' || $metodo === 'PUT')) {
            $input = json_decode(file_get_contents('php://input'), true);
            $respuesta = $controlador->guardar($input);
            V_Usuario::responder($respuesta);
        } else if ($accion === 'eliminar' && $metodo === 'DELETE') {
            $id = $_GET['id'] ?? null;
            $respuesta = $controlador->borrar($id);
            V_Usuario::responder($respuesta);
        } else {
            V_Usuario::responder(["error" => "Acción no reconocida para usuario"]);
        }
        break;

    case 'categorias':
        $controlador = new C_Categoria();
        if ($accion === 'obtener') {
            $respuesta = $controlador->listar();
            V_Categoria::responder($respuesta);
        } else if ($accion === 'crear' && $metodo === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $respuesta = $controlador->guardar($input);
            V_Categoria::responder($respuesta);
        } else if ($accion === 'actualizar' && $metodo === 'PUT') {
            $input = json_decode(file_get_contents('php://input'), true);
            $respuesta = $controlador->guardar($input);
            V_Categoria::responder($respuesta);
        } else if ($accion === 'eliminar' && $metodo === 'DELETE') {
            $id = $_GET['id'] ?? null;
            $respuesta = $controlador->borrar($id);
            V_Categoria::responder($respuesta);
        } else {
            V_Categoria::responder(["error" => "Acción no reconocida para categorías"]);
        }
        break;

    case 'operario':
        $controlador = new C_Operario();
        if ($accion === 'listar') {
            $respuesta = $controlador->listar_operarios();
            V_Operario::exito($respuesta, "Operarios obtenidos");
        } else if ($accion === 'obtener') {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                V_Operario::error("ID de operario no especificado", 400);
            }
            $respuesta = $controlador->obtener_operario($id);
            if ($respuesta) {
                V_Operario::exito($respuesta, "Operario obtenido");
            } else {
                V_Operario::error("Operario no encontrado", 404);
            }
        } else if ($accion === 'tickets') {
            $id_operario = $_GET['id'] ?? null;
            $estado = $_GET['estado'] ?? null;
            if (!$id_operario) {
                V_Operario::error("ID de operario no especificado", 400);
            }
            $respuesta = $controlador->obtener_tickets_operario($id_operario, $estado);
            V_Operario::exito($respuesta, "Tickets obtenidos");
        } else if ($accion === 'ticket_detalle') {
            $id_ticket = $_GET['id'] ?? null;
            if (!$id_ticket) {
                V_Operario::error("ID de ticket no especificado", 400);
            }
            $respuesta = $controlador->obtener_ticket_detalle($id_ticket);
            if ($respuesta) {
                V_Operario::exito($respuesta, "Ticket obtenido");
            } else {
                V_Operario::error("Ticket no encontrado", 404);
            }
        } else if ($accion === 'estadisticas') {
            $id_operario = $_GET['id'] ?? null;
            if (!$id_operario) {
                V_Operario::error("ID de operario no especificado", 400);
            }
            $respuesta = $controlador->obtener_estadisticas($id_operario);
            V_Operario::exito($respuesta, "Estadísticas obtenidas");
        } else if ($accion === 'disponibles') {
            $limite = $_GET['limite'] ?? 10;
            $respuesta = $controlador->obtener_tickets_disponibles($limite);
            V_Operario::exito($respuesta, "Tickets disponibles obtenidos");
        } else if ($accion === 'asignar' && $metodo === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!isset($input['id_ticket']) || !isset($input['id_operario'])) {
                V_Operario::error("Datos incompletos", 400);
            }
            $respuesta = $controlador->asignar_ticket($input['id_ticket'], $input['id_operario']);
            V_Operario::responder($respuesta);
        } else if ($accion === 'actualizar_estado' && $metodo === 'PUT') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!isset($input['id_ticket']) || !isset($input['estado']) || !isset($input['id_operario'])) {
                V_Operario::error("Datos incompletos", 400);
            }
            $respuesta = $controlador->actualizar_estado_ticket($input['id_ticket'], $input['estado'], $input['id_operario']);
            V_Operario::responder($respuesta);
        } else if ($accion === 'buscar') {
            $id_operario = $_GET['id'] ?? null;
            $termino = $_GET['termino'] ?? '';
            if (!$id_operario) {
                V_Operario::error("ID de operario no especificado", 400);
            }
            $respuesta = $controlador->buscar_tickets($id_operario, $termino);
            V_Operario::exito($respuesta, "Búsqueda completada");
        } else {
            V_Operario::error("Acción no reconocida para operarios", 400);
        }
        break;

    case 'ticket':
    default:
        $controlador = new C_Ticket();
        switch ($accion) {
            case 'listar':
                $id_usuario = $_GET['usuario_id'] ?? null;
                $respuesta = $controlador->listar($id_usuario);
                V_Ticket::responder($respuesta);
                break;
            case 'crear':
                if ($metodo === 'POST') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $respuesta = $controlador->guardar($input);
                    V_Ticket::responder($respuesta);
                }
                break;
            case 'actualizar':
                if ($metodo === 'PUT') {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $respuesta = $controlador->cambiar_estado($input['id'], $input['estado']);
                    V_Ticket::responder($respuesta);
                }
                break;
            case 'eliminar':
                if ($metodo === 'DELETE') {
                    $id = $_GET['id'] ?? '';
                    $respuesta = $controlador->borrar($id);
                    V_Ticket::responder($respuesta);
                }
                break;
            default:
                V_Ticket::responder(["error" => "Acción API no reconocida o método no permitido"]);
                break;
        }
        break;
}
?>
