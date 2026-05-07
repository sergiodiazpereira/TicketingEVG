<!DOCTYPE html>
<html lang="es">

<head>
	<!-- 
	  Proyecto: TicketingEVG
	  Alumno: Joseph Joel Quispe Alvarez
	  Asignatura: 
	  Curso: 2 DAW
	  Descripción: Dashboard con Sidebar colapsable (Mini-Sidebar).
	-->
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Dashboard Administrador - TicketingEVG</title>
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Bootstrap Icons -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
	<!-- Google Fonts: Inter -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
	<!-- Estilos Propios -->
	<link rel="stylesheet" href="../admin.css">
</head>

<body id="pagina-dashboard">

	<!-- Truco CSS puro: Checkbox oculto para colapsar el sidebar sin JS -->
	<input type="checkbox" id="toggle-sidebar" class="d-none">

	<div class="d-flex overflow-hidden">
		<!-- Sidebar Dinámico -->
		<nav id="sidebarMenu" class="sidebar d-flex flex-column vh-100 sticky-top">
			<!-- Header Sidebar -->
			<div class="sidebar-header p-4 mb-3 d-flex align-items-center">
				<label for="toggle-sidebar" class="boton-candado bg-warning rounded-3 p-2 me-3 flex-shrink-0"
					style="cursor: pointer;" title="Abrir/Cerrar menú">
					<i class="bi bi-shield-lock-fill text-white fs-4"></i>
				</label>
				<div class="sidebar-header-texto">
					<div class="fw-bold text-white small lh-1">Administración de</div>
					<div class="fw-bold text-white">TicketingEVG</div>
				</div>
			</div>

			<!-- Links de Navegación -->
			<ul class="nav flex-column mb-auto">
				<li class="nav-item">
					<a class="nav-link active" href="dashboard_admin.html" title="Dashboard">
						<i class="bi bi-grid-fill me-3"></i>
						<span class="link-texto">Dashboard</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="../operarios_admin/operarios_admin.html" title="Operarios">
						<i class="bi bi-person-badge-fill me-3"></i>
						<span class="link-texto">Operarios</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="../categorias_admin/categorias_admin.html" title="Categorías">
						<i class="bi bi-tag-fill me-3"></i>
						<span class="link-texto">Categorías</span>
					</a>
				</li>
			</ul>

			<!-- Footer Sidebar -->
			<div class="p-3 border-top border-white border-opacity-10">
				<a href="#" class="nav-link py-2 text-white-50 small" title="Mi información">
					<i class="bi bi-person-fill me-3"></i>
					<span class="link-texto">Mi información</span>
				</a>
				<a href="login.html" class="nav-link py-2 text-white-50 small" title="Cerrar sesión">
					<i class="bi bi-box-arrow-left me-3"></i>
					<span class="link-texto">Cerrar sesión</span>
				</a>
			</div>
		</nav>

		<!-- Contenido Principal -->
		<main class="flex-grow-1 vh-100 overflow-auto" style="min-width: 0;">

			<!-- Topbar -->
			<header
				class="bg-white p-4 mb-4 border-bottom shadow-sm d-flex justify-content-between align-items-center sticky-top">
				<div class="d-flex align-items-center">
					<h1 class="h5 fw-bold mb-0" style="color: #003366;">Dashboard de administrador</h1>
				</div>
				<div class="d-none d-md-flex align-items-center">
					<div class="text-end me-3">
						<div class="fw-bold small">Joseph Joel</div>
						<div class="text-muted small">Administrador</div>
					</div>
					<img src="https://ui-avatars.com/api/?name=Joseph+Joel&background=003366&color=fff"
						class="rounded-circle" width="38">
				</div>
			</header>

			<div class="container-fluid px-4 pb-4">
				<!-- 4 Tarjetas Superiores -->
				<div class="row g-4 mb-5">
					<div class="col-12 col-sm-6 col-lg-3">
						<div class="card card-premium p-3 h-100 bg-white">
							<div class="icono-circulo mb-3 text-primary">
								<i class="bi bi-eye"></i>
							</div>
							<div class="text-uppercase text-muted fw-bold small" style="letter-spacing: 1px;">Visitas
								Totales</div>
							<div class="h3 fw-bold mb-0">12.450</div>
						</div>
					</div>
					<div class="col-12 col-sm-6 col-lg-3">
						<div class="card card-premium p-3 h-100 bg-white">
							<div class="icono-circulo mb-3 text-warning">
								<i class="bi bi-people"></i>
							</div>
							<div class="text-uppercase text-muted fw-bold small" style="letter-spacing: 1px;">Usuarios
							</div>
							<div class="h3 fw-bold mb-0">842</div>
						</div>
					</div>
					<div class="col-12 col-sm-6 col-lg-3">
						<div class="card card-premium p-3 h-100 bg-white">
							<div class="icono-circulo mb-3 text-success">
								<i class="bi bi-tag"></i>
							</div>
							<div class="text-uppercase text-muted fw-bold small" style="letter-spacing: 1px;">Categorías
							</div>
							<div class="h3 fw-bold mb-0">14</div>
						</div>
					</div>
					<div class="col-12 col-sm-6 col-lg-3">
						<div class="card card-premium p-3 h-100 bg-white">
							<div class="icono-circulo mb-3 text-danger">
								<i class="bi bi-ticket-perforated"></i>
							</div>
							<div class="text-uppercase text-muted fw-bold small" style="letter-spacing: 1px;">Tickets
								Activos</div>
							<div class="h3 fw-bold mb-0">48</div>
						</div>
					</div>
				</div>

				<!-- Sección Media -->
				<div class="row g-4">
					<!-- Columna Izquierda -->
					<div class="col-12 col-xl-8">
						<div class="card card-premium p-4 h-100 bg-white">
							<h5 class="fw-bold mb-4">Estado del servicio de soporte</h5>

							<div class="mb-5">
								<div class="d-flex justify-content-between mb-2">
									<span class="text-uppercase text-muted fw-bold small">Rendimiento de
										resolución</span>
								</div>
								<div class="d-flex align-items-center">
									<div class="w-100 pe-3">
										<div class="text-uppercase text-muted fw-bold small">Tickets resueltos</div>
										<div class="progress progreso-personalizado mt-2">
											<div class="progress-bar bg-primary" style="width: 82%"></div>
										</div>
									</div>
									<span class="fw-bold text-success">82%</span>
								</div>
							</div>

							<div class="row g-4">
								<div class="col-12">
									<span class="text-uppercase text-muted fw-bold small mb-3 d-block">Carga de trabajo
										por tipo</span>
								</div>
								<div class="col-6">
									<div class="tarjeta-interna">
										<i class="bi bi-exclamation-triangle text-danger fs-4"></i>
										<div class="h3 fw-bold mb-0 mt-2">18</div>
										<div class="text-uppercase text-muted fw-bold small">Incidencias</div>
									</div>
								</div>
								<div class="col-6">
									<div class="tarjeta-interna">
										<i class="bi bi-info-circle text-primary fs-4"></i>
										<div class="h3 fw-bold mb-0 mt-2">30</div>
										<div class="text-uppercase text-muted fw-bold small">Peticiones</div>
									</div>
								</div>
							</div>

							<div class="mt-auto pt-4">
								<p class="nota-informativa mb-0">
									Contarán como operarios disponibles tanto los trabajadores como los responsables que
									no tengan ningun ticket asignado
								</p>
							</div>
						</div>
					</div>

					<!-- Columna Derecha -->
					<div class="col-12 col-xl-4">
						<div class="card card-premium p-4 bg-white mb-4">
							<h5 class="fw-bold mb-4">Distribución por prioridad</h5>

							<div class="prioridad-item">
								<div class="d-flex align-items-center">
									<i class="bi bi-flag text-danger me-3"></i>
									<span class="fw-medium">Prioridad alta</span>
								</div>
								<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 small">12
									TICKETS</span>
							</div>

							<div class="prioridad-item">
								<div class="d-flex align-items-center">
									<i class="bi bi-flag text-warning me-3"></i>
									<span class="fw-medium">Prioridad media</span>
								</div>
								<span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 small">24
									TICKETS</span>
							</div>

							<div class="prioridad-item mb-4">
								<div class="d-flex align-items-center">
									<i class="bi bi-flag text-secondary me-3"></i>
									<span class="fw-medium">Prioridad baja</span>
								</div>
								<span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 small">12
									TICKETS</span>
							</div>

							<div
								class="d-flex justify-content-between align-items-center p-3 border rounded-3 bg-light bg-opacity-50">
								<div class="d-flex align-items-center">
									<i class="bi bi-person-check text-success me-3 fs-4"></i>
									<span class="fw-bold small text-uppercase">Operarios disponibles</span>
								</div>
								<span class="text-success fw-bold">8</span>
							</div>
						</div>

						<div class="card card-premium p-3"
							style="background-color: #f0f7ff; border: 1px solid #d0e5ff;">
							<div class="d-flex align-items-start">
								<i class="bi bi-people-fill text-primary fs-4 me-3"></i>
								<div>
									<h6 class="fw-bold text-primary text-uppercase mb-1 small">Gestión de personal</h6>
									<p class="nota-informativa mb-0 small" style="color: #4a7ab5;">
										Contarán como operarios disponibles tanto los trabajadores como los responsables
										que no tengan ningun ticket asignado
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</div>

	<!-- Bootstrap JS Bundle -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script type="module" src="../js/app.js"></script>

	<?php include '../footer.php'; ?>
</body>

</html>