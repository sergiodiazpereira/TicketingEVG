<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Manuel Vega Purificación
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Vista (V) para transformar datos de operarios a JSON.
 */

class V_Operario {
    /**
     * Responde con datos JSON.
     * @param mixed $datos Información a enviar.
     */
    public static function responder($datos) {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Responde con un error.
     * @param string $mensaje Mensaje de error.
     * @param int $codigo Código HTTP.
     */
    public static function error($mensaje, $codigo = 400) {
        http_response_code($codigo);
        self::responder(["status" => "error", "message" => $mensaje]);
    }

    /**
     * Responde con éxito.
     * @param mixed $datos Datos de respuesta.
     * @param string $mensaje Mensaje.
     */
    public static function exito($datos, $mensaje = "") {
        $respuesta = ["status" => "success"];
        if (!empty($mensaje)) $respuesta["message"] = $mensaje;
        $respuesta["data"] = $datos;
        self::responder($respuesta);
    }
}
?>
