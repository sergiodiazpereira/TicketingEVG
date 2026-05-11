<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Enrutador principal de la API. Dirige las peticiones al controlador adecuado.
 */

require_once __DIR__ . '/controladores/C_Ticket.php';
require_once __DIR__ . '/vistas/V_Ticket.php';

// Capturar el método y la acción de la URL
$metodo = $_SERVER['REQUEST_METHOD'];
$accion = $_GET['accion'] ?? '';

// Inicializar controlador
$controlador = new C_Ticket();

// Manejo de peticiones OPTIONS (Preflight para CORS)
if ($metodo === 'OPTIONS') {
    V_Ticket::responder(["status" => "ok"]);
}

// Enrutamiento básico basado en el parámetro 'accion'
switch ($accion) {
    case 'listar':
        // Ejemplo: index.php?accion=listar&usuario_id=3
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
?>
