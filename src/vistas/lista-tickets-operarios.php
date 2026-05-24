<?php
/*
 * Proyecto: TicketingEVG
 * Alumno: Manuel Vega Purificación
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Listado detallado de tickets para operarios.
 */

session_start();
$id_operario = $_SESSION['id_operario'] ?? 1;
require_once __DIR__ . '/../backend/Models/M_Operario.php';

$modelo_operario = new M_Operario();
$filtro_estado = $_GET['estado'] ?? null;
$tickets = $modelo_operario->obtener_tickets_operario($id_operario, $filtro_estado);
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
	<link rel="stylesheet" href="../css/estiloAdmin.css">
</head>
<body>
	<header>
		<div class="d-flex align-items-center gap-2 me-2">
			<div class="icono-logo"><i class="fa-solid fa-wrench"></i></div>
			<div class="icono-texto">
				<strong>TicketingEVG</strong>
				<span class="info-estadistica">Portal de Operarios</span>
			</div>
		</div>
		<a href="portal-tickets-operarios.php" class="boton-nav">
			<i class="fa-solid fa-table-cells-large"></i>
			<span class="etiqueta-nav">Dashboard</span>
		</a>
		<a href="lista-tickets-operarios.php" class="boton-nav activo">
			<i class="fa-regular fa-clipboard"></i>
			<span class="etiqueta-nav">Mis tickets</span>
		</a>
		<a href="operarios.php" class="boton-nav">
			<i class="fa-solid fa-users-gear"></i>
			<span class="etiqueta-nav">Equipo</span>
		</a>
		<div class="flex-grow-1"></div>
		<a href="#" class="btn-cerrar-sesion">
			<i class="fa-solid fa-right-from-bracket"></i>
			<span class="etiqueta-nav">Cerrar sesión</span>
		</a>
	</header>

	<main>
		<div class="container-lg">
			<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4 cabecera-pagina">
				<div>
					<h1>Mis tickets</h1>
					<p>Gestión y seguimiento de trabajos técnicos asignados.</p>
				</div>
			</div>

			<div class="barra-busqueda-contenedor mb-4">
				<div class="barra-busqueda">
					<i class="fa-solid fa-magnifying-glass"></i>
					<input type="text" id="busqueda" placeholder="Buscar por ID, asunto o descripción...">
				</div>
				<div class="dropdown">
					<button class="boton-filtro dropdown-toggle" type="button" data-bs-toggle="dropdown">
						<i class="fa-solid fa-filter"></i> 
						<span id="filtro-label">TODOS</span>
					</button>
					<ul class="dropdown-menu">
						<li><a class="dropdown-item" href="lista-tickets-operarios.php">TODOS</a></li>
						<li><a class="dropdown-item" href="?estado=proceso">EN PROCESO</a></li>
						<li><a class="dropdown-item" href="?estado=asignado">ASIGNADO</a></li>
						<li><a class="dropdown-item" href="?estado=resuelto">RESUELTO</a></li>
					</ul>
				</div>
			</div>

			<div class="listado-tickets">
				<?php if (!empty($tickets)): ?>
					<?php foreach ($tickets as $ticket): 
						$es_incidencia = strpos($ticket['id'], 'I') === 0;
						$tipo_texto = $es_incidencia ? 'INCIDENCIA' : 'PETICIÓN DE SERVICIO';
						$icono = $es_incidencia ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-circle-info';
						$clase_tipo = $es_incidencia ? 'incidencia' : 'peticion';
						$color_prioridad = ($ticket['prioridad'] === 'a') ? 'alta' : (($ticket['prioridad'] === 'm') ? 'media' : 'baja');
						$etiqueta_prioridad = ($ticket['prioridad'] === 'a') ? 'ALTA' : (($ticket['prioridad'] === 'm') ? 'MEDIA' : 'BAJA');
					?>
					<div class="tarjeta-ticket-detallada <?php echo $clase_tipo; ?>">
						<div class="cabecera-card-ticket">
							<div class="tipo-ticket">
								<i class="<?php echo $icono; ?>"></i> 
								<span><?php echo $tipo_texto; ?></span>
							</div>
							<div class="badges-ticket">
								<span class="badge-prioridad <?php echo $color_prioridad; ?>"><?php echo $etiqueta_prioridad; ?></span>
								<span class="etiqueta-estado <?php echo $ticket['estado']; ?>">
									<?php echo ucfirst($ticket['estado']); ?>
								</span>
							</div>
						</div>
						<h3 class="titulo-ticket"><?php echo htmlspecialchars($ticket['titulo']); ?></h3>
						<p class="descripcion-ticket"><?php echo htmlspecialchars($ticket['descripcion']); ?></p>
						<div class="pie-card-ticket">
							<div class="categoria-ticket">
								<i class="fa-solid fa-tags"></i> 
								<span><?php echo htmlspecialchars($ticket['categoria_nombre'] ?? 'Sin categoría'); ?></span>
							</div>
							<div class="info-derecha-ticket">
								<span class="id-ticket">ID: <?php echo htmlspecialchars($ticket['id']); ?></span>
								<a href="ticket-detalle-operario.php?id=<?php echo urlencode($ticket['id']); ?>" class="enlace-detalles">
									DETALLES <i class="fa-solid fa-arrow-right"></i>
								</a>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="alert alert-info">
						<i class="fa-solid fa-info-circle"></i>
						<?php 
						if ($filtro_estado) {
							echo "No hay tickets con estado: <strong>" . ucfirst($filtro_estado) . "</strong>";
						} else {
							echo "No hay tickets asignados en este momento.";
						}
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</main>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		document.getElementById('busqueda').addEventListener('keyup', function(e) {
			const termino = e.target.value.toLowerCase();
			const tickets = document.querySelectorAll('.tarjeta-ticket-detallada');
			
			tickets.forEach(ticket => {
				const titulo = ticket.querySelector('.titulo-ticket').textContent.toLowerCase();
				const descripcion = ticket.querySelector('.descripcion-ticket').textContent.toLowerCase();
				const id = ticket.querySelector('.id-ticket').textContent.toLowerCase();
				
				if (titulo.includes(termino) || descripcion.includes(termino) || id.includes(termino)) {
					ticket.style.display = '';
				} else {
					ticket.style.display = 'none';
				}
			});
		});
	</script>
	<?php include_once('footer.php'); ?>
</body>
</html>
