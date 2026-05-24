<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Sergio Díaz Pereira
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Vista para la transformación de datos de Usuarios/Operarios a JSON.
 */

class V_Usuario {
    /**
     * Envía una respuesta JSON al frontend.
     * @param mixed $datos Datos a enviar.
     */
    public static function responder($datos) {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');

        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>