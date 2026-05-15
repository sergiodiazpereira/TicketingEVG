<?php
/**
 * Proyecto: TicketingEVG
 * Alumno: Sergio Díaz Pereira
 * Asignatura: Desarrollo de Aplicaciones Web
 * Curso: 2025-2026
 * Descripción: Vista de selección de pantalla para administradores.
 */
?>
<!DOCTYPE html>
<html class="sergio" lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Acceso Administrador - TicketingEVG</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="../css/estiloAdmin.css">
</head>
<body>
	<main class="seleccion-entorno">
		<div class="icono-seguridad">
			<i class="bi bi-shield-lock-fill fs-2"></i>
		</div>

		<h1 class="titulo-acceso">Acceso de administrador</h1>
		<p class="subtitulo-acceso">Selecciona el entorno de trabajo</p>

		<div class="container">
			<div class="row justify-content-center g-4">
				<div class="col-12 col-md-5 col-lg-4">
					<a href="dashboard_admin.php" class="tarjeta-entorno">
						<div class="icono-entorno">
							<i class="bi bi-grid-fill"></i>
						</div>
						<h2 class="titulo-entorno">Consola Admin</h2>
						<p class="desc-entorno">
							Gestión de la aplicación TicketingEVG
						</p>
						<div class="link-acceso">
							Acceder ahora <i class="bi bi-arrow-right"></i>
						</div>
					</a>
				</div>

				<div class="col-12 col-md-5 col-lg-4">
					<a href="portal-tickets-operarios.php" class="tarjeta-entorno">
						<div class="icono-entorno" style="background-color: var(--azul);">
							<i class="bi bi-ticket-perforated-fill"></i>
						</div>
						<h2 class="titulo-entorno">Portal Tickets</h2>
						<p class="desc-entorno">
							Gestión de tickets y resolución de problemas de la Escuela Virgen de Guadalupe
						</p>
						<div class="link-acceso">
							Acceder ahora <i class="bi bi-arrow-right"></i>
						</div>
					</a>
				</div>
			</div>
		</div>
	</main>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<?php include_once('footer.php'); ?>
</body>
</html>
