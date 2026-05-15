<?php
/*
 * Identificación: Sergio Díaz Pereira
 * Asignatura: Desarrollo de Aplicaciones Web
 * Curso: 2025-2026
 * Descripción: Formulario para la creación de un nuevo ticket de soporte.
 */
?>
<!DOCTYPE html>
<html class="sergio" lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Nuevo ticket – TicketingEVG</title>
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
				<span class="info-estadistica">Portal de gestión</span>
			</div>
		</div>
		<a href="portal-tickets-profesores.php" class="boton-nav"><i class="fa-solid fa-table-cells-large"></i> <span class="etiqueta-nav">Inicio</span></a>
		<a href="lista-tickets.php" class="boton-nav"><i class="fa-regular fa-clipboard"></i> <span class="etiqueta-nav">Mis tickets</span></a>
		<div class="flex-grow-1"></div>
		<a href="#" class="btn-cerrar-sesion"><i class="fa-solid fa-right-from-bracket"></i> <span class="etiqueta-nav">Cerrar sesión</span></a>
	</header>

	<main>
		<div class="container-lg" style="max-width: 900px;">
			<div class="d-flex align-items-center gap-3 mb-4 cabecera-pagina">
				<a href="lista-tickets.php" class="btn-retroceso"><i class="fa-solid fa-arrow-left"></i></a>
				<div>
					<h1>Nuevo ticket</h1>
					<p>Completa los datos para abrir un nuevo ticket de soporte.</p>
				</div>
			</div>

			<form class="tarjeta-formulario">
				<div class="cabecera-seccion-form">
					<i class="fa-solid fa-circle-plus"></i>
					<h2>Información general</h2>
				</div>
				<div class="cuerpo-formulario">
					<div class="row g-4">
						<div class="col-md-6">
							<label class="etiqueta-form">Tipo de solicitud</label>
							<select class="control-form control-select">
								<option value="incidencia">INCIDENCIA</option>
								<option value="peticion">PETICIÓN DE SERVICIO</option>
							</select>
						</div>
						<div class="col-md-6">
							<label class="etiqueta-form">Prioridad</label>
							<select class="control-form control-select">
								<option value="baja">BAJA</option>
								<option value="media" selected>MEDIA</option>
								<option value="alta">ALTA</option>
								<option value="urgente">URGENTE</option>
							</select>
						</div>
						<div class="col-md-6">
							<label class="etiqueta-form">Fecha límite o esperada (opcional)</label>
							<input type="date" class="control-form">
						</div>
						<div class="col-md-6">
							<label class="etiqueta-form">Categoría técnica</label>
							<select class="control-form control-select">
								<option value="" disabled selected>Selecciona una categoría</option>
								<option value="software">Soporte Software</option>
								<option value="hardware">Hardware / Equipos</option>
								<option value="redes">Redes y Conectividad</option>
								<option value="mantenimiento">Mantenimiento General</option>
							</select>
						</div>
						<div class="col-md-6">
							<label class="etiqueta-form">Ubicación / Aula</label>
							<select class="control-form control-select">
								<option value="" disabled selected>Selecciona ubicación</option>
								<option value="aula101">Aula 101</option>
								<option value="aula102">Aula 102</option>
								<option value="aula201">Aula 201</option>
								<option value="aula202">Aula 202</option>
								<option value="biblioteca">Biblioteca</option>
								<option value="salon_actos">Salón de Actos</option>
								<option value="secretaria">Secretaría / Administración</option>
								<option value="otros">Otros / Zonas comunes</option>
							</select>
						</div>
						<div class="col-12">
							<label class="etiqueta-form">Título breve del asunto</label>
							<input type="text" class="control-form" placeholder="Ej: No funciona la impresora en Aula 203">
						</div>
						<div class="col-12">
							<label class="etiqueta-form">Descripción detallada</label>
							<textarea class="control-form" placeholder="Describe el problema o tu solicitud con el mayor detalle posible..."></textarea>
							<span class="hint-form">Incluye cualquier paso que hayas intentado ya para solucionar el problema.</span>
						</div>
					</div>
				</div>
				<div class="modal-pie-premium border-top bg-light" style="padding: 1.5rem 2rem;">
					<div class="ms-auto d-flex align-items-center gap-3">
						<a href="lista-tickets.php" class="boton-cancelar">Cancelar</a>
						<button type="submit" class="boton-enviar"><i class="fa-solid fa-paper-plane"></i> Enviar ticket</button>
					</div>
				</div>
			</form>
		</div>
	</main>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<?php include_once('footer.php'); ?>
</body>
</html>
