<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: Desarrollo Web en Entorno Servidor
 * Curso: 2 DAW
 * Descripcion: Fichero de entrada principal (Front Controller) para la arquitectura MVC.
 */

// Definicion de constantes para rutas
define("CONTROLADORES_RUTAS", "Controllers/");
define("MODELOS_RUTAS", "Models/");

// Controlador y accion por defecto
$controlador_defecto = "Ticket";
$accion_defecto = "mostrar_inicio";

// Obtencion del controlador desde la URL o el valor por defecto
if (isset($_GET["controlador"])) {
	$nombre_controlador = $_GET["controlador"];
} else {
	$nombre_controlador = $controlador_defecto;
}

// Obtencion de la accion desde la URL o el valor por defecto
if (isset($_GET["accion"])) {
	$nombre_accion = $_GET["accion"];
} else {
	$nombre_accion = $accion_defecto;
}

// Construccion del nombre del fichero del controlador
$fichero_controlador = CONTROLADORES_RUTAS . $nombre_controlador . "Controller.php";

// Verificacion de existencia del fichero del controlador
if (file_exists($fichero_controlador)) {
	require_once $fichero_controlador;
	$clase_controlador = $nombre_controlador . "Controller";

	// Verificacion de existencia de la clase
	if (class_exists($clase_controlador)) {
		$objeto_controlador = new $clase_controlador();

		// Verificacion de existencia del metodo (accion)
		if (method_exists($objeto_controlador, $nombre_accion)) {
			$objeto_controlador->$nombre_accion();
		} else {
			// Si la accion no existe, se ejecuta la accion por defecto
			$objeto_controlador->$accion_defecto();
		}
	} else {
		echo "Error: La clase " . $clase_controlador . " no esta definida.";
	}
} else {
	// Si el controlador no existe, se podria mostrar una pagina de error
	echo "Error: El controlador " . $nombre_controlador . " no existe.";
}
?>
