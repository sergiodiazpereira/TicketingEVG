<?php
/*
 * Proyecto: TicketingEVG
 * Alumno: Manuel Vega Purificación
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Vista detallada de un ticket para operarios con opciones de actualización.
 */

session_start();
$id_operario = $_SESSION['id_operario'] ?? 1;
$id_ticket = $_GET['id'] ?? null;

if (!$id_ticket) {
    header('Location: lista-tickets-operarios.php');
    exit;
}

require_once __DIR__ . '/../backend/Models/M_Operario.php';

$modelo_operario = new M_Operario();
$ticket = $modelo_operario->obtener_ticket_detalle($id_ticket);

if (!$ticket) {
    header('Location: lista-tickets-operarios.php');
    exit;
}

// Procesar actualización de estado
$mensaje = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_estado'])) {
    $nuevo_estado = $_POST['nuevo_estado'];
    $estados_validos = ['asignado', 'proceso', 'resuelto'];
    
    if (in_array($nuevo_estado, $estados_validos)) {
        if ($modelo_operario->actualizar_estado_ticket($id_ticket, $nuevo_estado, $id_operario)) {
            $mensaje = "Estado actualizado correctamente a: " . ucfirst($nuevo_estado);
            $ticket['estado'] = $nuevo_estado;
        } else {
            $error = "Error al actualizar el estado del ticket.";
        }
    } else {
        $error = "Estado no válido.";
    }
}

$es_incidencia = strpos($ticket['id'], 'I') === 0;
$tipo_texto = $es_incidencia ? 'INCIDENCIA' : 'PETICIÓN DE SERVICIO';
$icono = $es_incidencia ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-circle-info';
$color_prioridad = ($ticket['prioridad'] === 'a') ? 'alta' : (($ticket['prioridad'] === 'm') ? 'media' : 'baja');
$etiqueta_prioridad = ($ticket['prioridad'] === 'a') ? 'ALTA' : (($ticket['prioridad'] === 'm') ? 'MEDIA' : 'BAJA');
?>
<!DOCTYPE html>
<html class="sergio" lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Detalles del Ticket – TicketingEVG</title>
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
		<div class="flex-grow-1"></div>
		<a href="#" class="btn-cerrar-sesion">
			<i class="fa-solid fa-right-from-bracket"></i>
			<span class="etiqueta-nav">Cerrar sesión</span>
		</a>
	</header>

	<main>
		<div class="container-lg" style="max-width: 900px;">
			<div class="d-flex align-items-center gap-3 mb-4 cabecera-pagina">
				<a href="lista-tickets-operarios.php" class="btn-retroceso"><i class="fa-solid fa-arrow-left"></i></a>
				<div>
					<h1>Detalles del Ticket</h1>
					<p>Información y opciones de actualización.</p>
				</div>
			</div>

			<?php if (!empty($mensaje)): ?>
				<div class="alert alert-success" role="alert">
					<i class="fa-solid fa-check-circle"></i> <?php echo $mensaje; ?>
				</div>
			<?php endif; ?>

			<?php if (!empty($error)): ?>
				<div class="alert alert-danger" role="alert">
					<i class="fa-solid fa-exclamation-circle"></i> <?php echo $error; ?>
				</div>
			<?php endif; ?>

			<div class="tarjeta-formulario">
				<div class="cabecera-seccion-form">
					<i class="<?php echo $icono; ?>"></i>
					<h2><?php echo $tipo_texto; ?></h2>
				</div>
				<div class="cuerpo-formulario">
					<div class="row g-4">
						<div class="col-12">
							<h3><?php echo htmlspecialchars($ticket['titulo']); ?></h3>
						</div>
						
						<div class="col-md-6">
							<label class="etiqueta-form">ID del Ticket</label>
							<p class="form-control-plaintext bg-light p-2 rounded"><?php echo htmlspecialchars($ticket['id']); ?></p>
						</div>

						<div class="col-md-6">
							<label class="etiqueta-form">Estado actual</label>
							<p class="form-control-plaintext">
								<span class="badge bg-<?php echo ($ticket['estado'] === 'proceso') ? 'danger' : (($ticket['estado'] === 'asignado') ? 'warning' : 'success'); ?>">
									<?php echo ucfirst($ticket['estado']); ?>
								</span>
							</p>
						</div>

						<div class="col-md-6">
							<label class="etiqueta-form">Prioridad</label>
							<p class="form-control-plaintext">
								<span class="badge bg-<?php echo ($ticket['prioridad'] === 'a') ? 'danger' : (($ticket['prioridad'] === 'm') ? 'warning' : 'success'); ?>">
									<?php echo $etiqueta_prioridad; ?>
								</span>
							</p>
						</div>

						<div class="col-md-6">
							<label class="etiqueta-form">Categoría</label>
							<p class="form-control-plaintext"><?php echo htmlspecialchars($ticket['categoria_nombre'] ?? 'Sin categoría'); ?></p>
						</div>

						<div class="col-md-6">
							<label class="etiqueta-form">Creado por</label>
							<p class="form-control-plaintext"><?php echo htmlspecialchars($ticket['usuario_creador_nombre'] ?? 'Sistema'); ?></p>
						</div>

						<div class="col-md-6">
							<label class="etiqueta-form">Fecha de creación</label>
							<p class="form-control-plaintext"><?php echo date('d/m/Y H:i', strtotime($ticket['fecha_creacion'])); ?></p>
						</div>

						<div class="col-12">
							<label class="etiqueta-form">Descripción</label>
							<div class="bg-light p-3 rounded" style="min-height: 120px;">
								<?php echo htmlspecialchars($ticket['descripcion']); ?>
							</div>
						</div>
					</div>
				</div>

				<div class="modal-pie-premium border-top bg-light" style="padding: 1.5rem 2rem;">
					<form method="POST" class="d-flex gap-3">
						<div class="flex-grow-1">
							<label class="etiqueta-form">Cambiar estado</label>
							<select name="nuevo_estado" class="control-form control-select">
								<option value="">-- Seleccionar nuevo estado --</option>
								<?php if ($ticket['estado'] !== 'proceso'): ?>
									<option value="proceso">Iniciar (En proceso)</option>
								<?php endif; ?>
								<?php if ($ticket['estado'] !== 'resuelto'): ?>
									<option value="resuelto">Marcar como Resuelto</option>
								<?php endif; ?>
								<?php if ($ticket['estado'] !== 'asignado'): ?>
									<option value="asignado">Volver a Asignado</option>
								<?php endif; ?>
							</select>
						</div>
						<div class="d-flex align-items-end gap-2">
							<button type="submit" class="boton-enviar"><i class="fa-solid fa-save"></i> Actualizar</button>
							<a href="lista-tickets-operarios.php" class="boton-cancelar">Volver</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</main>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<?php include_once('footer.php'); ?>
</body>
</html>
