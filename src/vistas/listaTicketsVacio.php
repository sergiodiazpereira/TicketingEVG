<?php
/*
 * Identificación: Sergio Díaz Pereira
 * Asignatura: Desarrollo de Aplicaciones Web
 * Curso: 2025-2026
 * Descripción: Listado de tickets vacío con estado de "Sin resultados".
 */
?>
<!DOCTYPE html>
<html class="sergio" lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Mis tickets – TicketingEVG</title>
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/estiloUsuario.css">
</head>
<body>
	<header>
		<div class="d-flex align-items-center gap-2 me-2">
			<div class="icono-logo"><i class="fa-solid fa-school"></i></div>
			<div class="icono-texto">
				<strong>TicketingEVG</strong>
				<span class="info-estadistica">Portal de gestión</span>
			</div>
		</div>
		<a href="inicioTicketsProfesores.php" class="boton-nav"><i class="fa-solid fa-table-cells-large"></i> <span class="etiqueta-nav">Inicio</span></a>
		<a href="listaTicketsVacio.php" class="boton-nav activo"><i class="fa-regular fa-clipboard"></i> <span class="etiqueta-nav">Mis tickets</span></a>
		<div class="flex-grow-1"></div>
		<a href="#" class="btn-cerrar-sesion"><i class="fa-solid fa-right-from-bracket"></i> <span class="etiqueta-nav">Cerrar sesión</span></a>
	</header>

	<main>
		<div class="container-lg">
			<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4 cabecera-pagina">
				<div>
					<h1>Listado de tickets</h1>
					<p>Gestión y seguimiento de las intervenciones técnicas registradas.</p>
				</div>
				<a href="creacionTicket.php" class="boton-crear"><i class="fa-solid fa-plus"></i> Crear ticket</a>
			</div>

			<div class="barra-busqueda-contenedor mb-4">
				<div class="barra-busqueda">
					<i class="fa-solid fa-magnifying-glass"></i>
					<input type="text" placeholder="Buscar por asunto o descripción...">
				</div>
				<button class="boton-filtro"><i class="fa-solid fa-filter"></i> <span>TODOS</span></button>
			</div>

			<div class="estado-vacio con-borde">
				<div class="icono-vacio"><i class="fa-solid fa-magnifying-glass"></i></div>
				<p>Sin resultados</p>
				<span>No hay tickets que coincidan con los criterios de búsqueda actuales.</span>
			</div>
		</div>
	</main>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<?php include_once('footer.php'); ?>
</body>
</html>
