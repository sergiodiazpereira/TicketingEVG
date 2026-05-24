<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Enrutador dinámico para la API PHP. 
 */

// Cabeceras y CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    echo json_encode(["status" => "ok"]);
    exit;
}

// 1. Obtener controlador y método directamente de la URL
$entidad = $_GET['entidad'] ?? 'ticket';
$metodo  = $_GET['accion']  ?? '';

$clase   = 'C_' . ucfirst(strtolower($entidad));
$archivo = __DIR__ . "/Controllers/{$clase}.php";

// 2. Cargar e instanciar controlador
if (!file_exists($archivo)) {
    echo json_encode(["error" => "Controlador '{$entidad}' no encontrado."]);
    exit;
}
require_once $archivo;
$ctrl = new $clase();

if (!method_exists($ctrl, $metodo)) {
    echo json_encode(["error" => "Método '{$metodo}' no encontrado en '{$entidad}'."]);
    exit;
}

// 3. Unificar datos de entrada (GET, POST y JSON Body)
$datos = array_merge($_GET, $_POST, json_decode(file_get_contents('php://input'), true) ?? []);

// 4. Inyectar parámetros mediante Reflexión (para pasar argumentos al método)
$ref = new ReflectionMethod($ctrl, $metodo);
$args = [];
$payload = array_diff_key($datos, array_flip(['entidad', 'accion']));

foreach ($ref->getParameters() as $p) {
    $nombre = $p->getName();
    
    if (in_array($nombre, ['json_data', 'datos', 'data', 'input'])) {
        $args[] = $payload;
    } else {
        $valor = null;

        // 1. Buscar coincidencia exacta (ej: id, estado)
        if (isset($datos[$nombre])) {
            $valor = $datos[$nombre];
        } 
        // 2. Si el parámetro en PHP es 'id_usuario', buscar 'usuario_id' en la petición de Angular
        else {
            $variante1 = str_replace('id_', '', $nombre) . '_id';
            if (isset($datos[$variante1])) {
                $valor = $datos[$variante1];
            } 
            // 3. Si el parámetro en PHP es 'usuario_id', buscar 'id_usuario' en la petición
            else {
                $variante2 = 'id_' . str_replace('_id', '', $nombre);
                if (isset($datos[$variante2])) {
                    $valor = $datos[$variante2];
                }
            }
        }

        // 4. Si el valor sigue siendo null, comprobar si el parámetro tiene un valor por defecto en la firma del método
        if ($valor === null) {
            if ($p->isDefaultValueAvailable()) {
                $valor = $p->getDefaultValue();
            }
        }

        $args[] = $valor;
    }
}

// 5. Ejecutar y devolver respuesta directamente en JSON
echo json_encode($ref->invokeArgs($ctrl, $args), JSON_UNESCAPED_UNICODE);
