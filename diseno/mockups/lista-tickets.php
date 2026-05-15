<?php
/*
 * IdentificaciÃ³n: Sergio DÃ­az Pereira
 * Asignatura: Desarrollo de Aplicaciones Web
 * Curso: 2025-2026
 * DescripciÃ³n: Listado detallado de tickets para el usuario.
 */
?>
<!DOCTYPE html>
<html class="sergio" lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Mis tickets â€“ TicketingEVG</title>
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
	<link rel="stylesheet" href="estiloUsuario.css">
</head>
<body>
	<header>
		<div class="d-flex align-items-center gap-2 me-2">
			<div class="icono-logo"><i class="fa-solid fa-school"></i></div>
			<div class="icono-texto">
				<strong>TicketingEVG</strong>
				<span class="info-estadistica">Portal de gestiÃ³n</span>
			</div>
		</div>
		<a href="portal-tickets-profesores.php" class="boton-nav fantasma">
			<i class="fa-solid fa-table-cells-large"></i>
			<span class="etiqueta-nav">Inicio</span>
		</a>
		<a href="lista-tickets.php" class="boton-nav activo">
			<i class="fa-regular fa-clipboard"></i>
			<span class="etiqueta-nav">Mis tickets</span>
		</a>
		<div class="flex-grow-1"></div>
		<a href="#" class="btn-cerrar-sesion">
			<i class="fa-solid fa-right-from-bracket"></i>
			<span class="etiqueta-nav">Cerrar sesiÃ³n</span>
		</a>
	</header>

	<main>
		<div class="container-lg">
			<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4 cabecera-pagina">
				<div>
					<h1>Mis tickets</h1>
					<p>GestiÃ³n y seguimiento de las intervenciones tÃ©cnicas registradas.</p>
				</div>
				<a href="crear-tickets.php" class="boton-crear"><i class="fa-solid fa-plus"></i> Crear ticket</a>
			</div>

			<div class="barra-busqueda-contenedor mb-4">
				<div class="barra-busqueda">
					<i class="fa-solid fa-magnifying-glass"></i>
					<input type="text" placeholder="Buscar por asunto o descripciÃ³n...">
				</div>
				<button class="boton-filtro"><i class="fa-solid fa-filter"></i> <span>TODOS</span></button>
			</div>

			<div class="listado-tickets">
				<div class="tarjeta-ticket-detallada incidencia" style="animation-delay: .1s">
					<div class="cabecera-card-ticket">
						<div class="tipo-ticket"><i class="fa-solid fa-circle-exclamation"></i> <span>INCIDENCIA</span></div>
						<div class="badges-ticket">
							<span class="badge-prioridad alta">ALTA</span>
							<span class="etiqueta-estado proceso">EN PROCESO</span>
						</div>
					</div>
					<h3 class="titulo-ticket">ORDENADOR NO ENCIENDE EN AULA 203</h3>
					<div class="asignacion-ticket"><i class="fa-solid fa-user-gear"></i> <span>ASIGNADO A: <strong>TRABAJADOR-1</strong></span></div>
					<p class="descripcion-ticket">El ordenador del profesor en el Aula 203 no enciende. He revisado que estÃ¡ conectado a la corriente pero no responde al pulsar el botÃ³n de encendido.</p>
					<div class="pie-card-ticket">
						<div class="categoria-ticket"><i class="fa-solid fa-tags"></i> <span>SOPORTE SOFTWARE</span></div>
						<div class="info-derecha-ticket">
							<span class="localizacion">LOCALIZACIÃ“N: <strong>AULA 203, EDIFICIO B</strong></span>
							<a href="#" class="enlace-detalles">DETALLES <i class="fa-solid fa-arrow-right"></i></a>
						</div>
					</div>
				</div>

				<div class="tarjeta-ticket-detallada peticion" style="animation-delay: .2s">
					<div class="cabecera-card-ticket">
						<div class="tipo-ticket"><i class="fa-solid fa-circle-info"></i> <span>PETICIÃ“N DE SERVICIO</span></div>
						<div class="badges-ticket">
							<span class="badge-prioridad media">MEDIA</span>
							<span class="etiqueta-estado pendiente">PENDIENTE</span>
						</div>
					</div>
					<h3 class="titulo-ticket">SOLICITUD DE MARCADORES PARA PIZARRA</h3>
					<p class="descripcion-ticket">Necesito 10 marcadores de colores variados para la pizarra blanca del Aula 105. Los actuales estÃ¡n secos y no escriben bien.</p>
					<div class="pie-card-ticket">
						<div class="categoria-ticket"><i class="fa-solid fa-tags"></i> <span>MANTENIMIENTO GENERAL</span></div>
						<div class="info-derecha-ticket">
							<span class="localizacion">LOCALIZACIÃ“N: <strong>AULA 105, EDIFICIO A</strong></span>
							<a href="#" class="enlace-detalles">DETALLES <i class="fa-solid fa-arrow-right"></i></a>
						</div>
					</div>
				</div>

				<div class="tarjeta-ticket-detallada incidencia" style="animation-delay: .3s">
					<div class="cabecera-card-ticket">
						<div class="tipo-ticket"><i class="fa-solid fa-circle-exclamation"></i> <span>INCIDENCIA</span></div>
						<div class="badges-ticket">
							<span class="badge-prioridad alta">ALTA</span>
							<span class="etiqueta-estado asignado">ASIGNADO</span>
						</div>
					</div>
					<h3 class="titulo-ticket">PROYECTOR MUESTRA IMAGEN BORROSA</h3>
					<div class="asignacion-ticket"><i class="fa-solid fa-user-gear"></i> <span>ASIGNADO A: <strong>TRABAJADOR-1</strong></span></div>
					<p class="descripcion-ticket">El proyector del Aula 102 no enfoca correctamente a pesar de ajustar la lente. Se requiere revisiÃ³n tÃ©cnica urgente.</p>
					<div class="pie-card-ticket">
						<div class="categoria-ticket"><i class="fa-solid fa-tags"></i> <span>SISTEMAS AUDIOVISUALES</span></div>
						<div class="info-derecha-ticket">
							<span class="localizacion">LOCALIZACIÃ“N: <strong>AULA 102, EDIFICIO A</strong></span>
							<a href="#" class="enlace-detalles">DETALLES <i class="fa-solid fa-arrow-right"></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<?php include_once('footer.php'); ?>
</body>
</html>
