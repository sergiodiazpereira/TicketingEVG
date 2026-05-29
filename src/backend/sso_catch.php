<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Interceptor de SSO para recibir peticiones POST de la Intranet y redirigirlas a la SPA Angular.
 */

// Construir la URL base del servidor actual de forma dinámica
// (evita depender de variables de entorno que pueden apuntar a localhost)
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$url_frontend = $protocolo . '://' . $host;

// Posibles nombres de parámetro que pueda estar usando la Intranet
$token = $_POST['auth_token'] ?? $_POST['token'] ?? $_POST['jwt'] ?? $_POST['id_token'] ?? $_GET['auth_token'] ?? $_GET['token'] ?? $_GET['jwt'] ?? '';

if (!empty($token)) {
	// Redirigir a la URL absoluta del frontend para que Angular gestione la ruta
	$url_destino = $url_frontend . '/sso-callback?token=' . urlencode($token);
	header('Location: ' . $url_destino);
	exit;
}

// MODO DEBUG: Si no llega ningún token conocido, imprimimos en pantalla qué nos están enviando
echo "<div style='font-family: sans-serif; padding: 20px;'>";
echo "<h2 style='color: #d9534f;'>TicketingEVG - Modo Debug Interceptor SSO</h2>";
echo "<p>No se encontró el parámetro 'token'. A continuación se muestran los datos exactos que la Intranet acaba de enviar:</p>";

echo "<h3 style='color: #0275d8;'>Datos recibidos por POST:</h3><pre style='background: #f4f4f4; padding: 10px; border-radius: 5px;'>";
var_export($_POST);
echo "</pre>";

echo "<h3 style='color: #0275d8;'>Datos recibidos por GET:</h3><pre style='background: #f4f4f4; padding: 10px; border-radius: 5px;'>";
var_export($_GET);
echo "</pre>";

echo "</div>";
?>

