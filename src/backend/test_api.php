<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Script de prueba para validar la API de la Intranet de Aitor mediante cURL y X-Auth-Token.
 */

header('Content-Type: application/json');

// El token JWT real de pruebas de Joseph proporcionado para el SSO
$token_sso = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3Nzk5OTgyODIsImV4cCI6MTc4MDA4NDY4MiwiZGF0YSI6eyJpZCI6MjYsIm5vbWJyZSI6Ikpvc2VwaCIsImFwZWxsaWRvcyI6IlF1aXNwZSBBbHZhcmV6IiwiZW1haWwiOiJqb3NlcGhxYTMxMzFAZ21haWwuY29tIiwiZm90byI6Imh0dHBzOi8vbGgzLmdvb2dsZXVzZXJjb250ZW50LmNvbS9hL0FDZzhvY0lVWElDTmV0QXVtcllQRlFzNHR4Umh3bjNPQjF6QmhxcFBMZGUxRW9SSzROaEx0UT1zOTYtYyIsInJvbGVzIjpbInN1cGVyX2FkbWluIl0sInRpcG9fcGVyc29uYWwiOiJEb2NlbnRlIn19.8AztDl3ghxpfHfavU2n__BLmqio77L3KWrNIxmF-D9Q';

$url_api = 'https://17.daw.esvirgua.com/api/index.php?c=Usuarios&m=listar';

// Inicializamos cURL para hacer la llamada HTTP segura a la Intranet
$ch = curl_init($url_api);

// Configuramos las opciones de cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Evitamos fallos de certificados SSL locales
curl_setopt($ch, CURLOPT_HTTPHEADER, [
	'Content-Type: application/json',
	'X-Auth-Token: ' . $token_sso // Inyectamos la cabecera manual solicitada por Aitor
]);

$respuesta = curl_exec($ch);
$codigo_estado = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error_curl = curl_error($ch);

curl_close($ch);

// Evaluamos los resultados del test
if ($error_curl) {
	echo json_encode([
		'status' => 'error',
		'message' => 'Fallo en la conexión cURL con la Intranet: ' . $error_curl
	], JSON_PRETTY_PRINT);
	exit;
}

$datos_respuesta = json_decode($respuesta, true);

echo json_encode([
	'status' => 'success',
	'test_url' => $url_api,
	'http_status_code' => $codigo_estado,
	'api_raw_response_length' => strlen($respuesta),
	'api_decoded_response' => $datos_respuesta ?? 'No se pudo decodificar la respuesta como JSON. Respuesta original: ' . $respuesta
], JSON_PRETTY_PRINT);
?>
