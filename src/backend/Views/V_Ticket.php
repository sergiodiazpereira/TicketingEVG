<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Vista (V) encargada de transformar los datos a formato JSON para la API.
 */

class V_Ticket {
    /**
     * Finaliza la ejecución enviando una respuesta JSON estructurada.
     * @param mixed $datos Información a enviar al frontend.
     */
    public static function responder($datos) {
        // Establecer cabeceras para JSON y permitir peticiones desde el frontend (CORS si fuera necesario)
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *'); // Ajustar según seguridad en producción
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>
