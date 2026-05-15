<?php
/*
 * IdentificaciÃ³n: Sergio DÃ­az Pereira
 * Asignatura: Desarrollo de Aplicaciones Web
 * Curso: 2025-2026
 * DescripciÃ³n: Panel de control vacÃ­o cuando no hay registros.
 */
?>
<!DOCTYPE html>
<html class="sergio" lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>TicketingEVG â€“ Dashboard VacÃ­o</title>
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
	<link rel="stylesheet" href="../css/estiloUsuario.css">
</head>
<body>
	<header>
		<div class="d-flex align-items-center gap-2 me-4">
			<div class="icono-logo"><i class="fa-solid fa-school"></i></div>
			<div class="icono-texto">
				<strong>TicketingEVG</strong>
				<span class="info-estadistica">Portal de gestiÃ³n</span>
			</div>
		</div>
		<a href="portal-tickets-profesores.php" class="boton-nav activo">
			<i class="fa-solid fa-table-cells-large"></i>
			<span class="etiqueta-nav">Inicio</span>
		</a>
		<a href="lista-tickets-vacio.php" class="boton-nav">
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
					<h1>Dashboard</h1>
					<p>Resumen operativo y estado de las solicitudes de soporte.</p>
				</div>
				<a href="crear-tickets.php" class="boton-crear"><i class="fa-solid fa-plus"></i> Crear ticket</a>
			</div>

			<div class="row g-3 mb-4">
				<div class="col-6 col-md-3">
					<div class="tarjeta-stats">
						<div class="icono-stats amarillo"><i class="fa-solid fa-layer-group"></i></div>
						<div class="info-estadistica">Total tickets</div>
						<div class="valor-stats">0</div>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<div class="tarjeta-stats">
						<div class="icono-stats rojo"><i class="fa-solid fa-circle-exclamation"></i></div>
						<div class="info-estadistica">Incidencias</div>
						<div class="valor-stats">0</div>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<div class="tarjeta-stats">
						<div class="icono-stats azul"><i class="fa-solid fa-circle-info"></i></div>
						<div class="info-estadistica">Peticiones de servicio</div>
						<div class="valor-stats">0</div>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<div class="tarjeta-stats">
						<div class="icono-stats verde"><i class="fa-solid fa-gear"></i></div>
						<div class="info-estadistica">En proceso</div>
						<div class="valor-stats">0</div>
					</div>
				</div>
			</div>

			<div class="tarjeta-tickets">
				<div class="cabecera-tickets">
					<div>
						<h2>Tickets recientes</h2>
						<p>Tus Ãºltimos tickets y actualizaciones de estado.</p>
					</div>
					<a href="lista-tickets-vacio.php" class="ver-todo">Ver todo <i class="fa-solid fa-arrow-up-right-from-square fa-xs"></i></a>
				</div>
				<div class="cuerpo-tickets">
					<div class="estado-vacio"><p>No hay tickets registrados</p></div>
				</div>
			</div>
		</div>
	</main>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<?php include_once('footer.php'); ?>
</body>
</html>
