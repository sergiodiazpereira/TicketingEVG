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
require_once __DIR__ . '/Controllers/C_Categoria.php';
require_once __DIR__ . '/Views/V_Ticket.php';

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
    case 'usuario':
        $controlador = new C_Usuario();
        if ($accion === 'listar_operarios') {
            $respuesta = $controlador->listar_operarios();
            V_Ticket::responder($respuesta);
        } else if ($accion === 'estadisticas') {
            $respuesta = $controlador->get_estadisticas();
            V_Ticket::responder($respuesta);
        } else {
            V_Ticket::responder(["error" => "Acción no reconocida para usuario"]);
        }
        break;

    case 'categoria':
        $controlador = new C_Categoria();
        if ($accion === 'listar') {
            $respuesta = $controlador->listar();
            V_Ticket::responder($respuesta);
        } else {
            V_Ticket::responder(["error" => "Acción no reconocida para categoria"]);
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
