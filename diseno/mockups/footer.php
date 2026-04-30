<?php
function _obtener_payload_jwt(string $token): ?array
{
	$partes = explode('.', $token);

	if (count($partes) !== 3)
		return null;

	$b64 = strtr($partes[1], '-_', '+/');
	$padded = str_pad($b64, strlen($b64) + (4 - strlen($b64) % 4) % 4, '=');
	$raw = base64_decode($padded, true);

	return ($raw !== false) ? json_decode($raw, true) : null;
}

$_payload = !empty($_COOKIE['auth_token']) ? _obtener_payload_jwt($_COOKIE['auth_token']) : null;
$_datos = $_payload['data'] ?? null;

$_nombre = $_datos['nombre'] ?? null;
$_email = $_datos['email'] ?? null;
$_foto = $_datos['foto'] ?? null;
$_rol = $_datos['rol'] ?? null;

$_roles = [
	'admin' => ['Administrador', 'badge-admin'],
	'profesor' => ['Profesor/a', 'badge-profesor'],
	'alumno' => ['Alumno/a', 'badge-alumno'],
	'staff' => ['Personal', 'badge-staff'],
];

[$_rol_label, $_rol_class] = $_roles[$_rol] ?? [($_rol ? htmlspecialchars($_rol) : null), ''];
?>
<style>
	:root {
		--footer-h: 42px;
	}

	body {
		padding-bottom: calc(var(--footer-h) + 8px) !important;
	}

	.intranet-footer {
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		height: var(--footer-h);
		z-index: 1055;
		background: #fff;
		border-top: 1.5px solid var(--border);
		box-shadow: 0 -2px 8px rgba(0, 0, 0, .07);
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 0 1.25rem;
		gap: 1rem;
		font-size: .78rem;
		color: var(--gris-subtitulos);
	}

	.if-user {
		display: flex;
		align-items: center;
		gap: .55rem;
		min-width: 0;
	}

	.if-avatar {
		width: 26px;
		height: 26px;
		border-radius: 50%;
		object-fit: cover;
		border: 1.5px solid var(--azul-claro);
		flex-shrink: 0;
	}

	.if-avatar-ph {
		width: 26px;
		height: 26px;
		border-radius: 50%;
		background: var(--azul-claro);
		color: var(--azul);
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: .8rem;
		flex-shrink: 0;
	}

	.if-nombre {
		font-weight: 600;
		color: var(--azul-oscuro);
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: 200px;
	}

	.if-sep {
		opacity: .3;
	}

	.if-email {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: 240px;
	}

	.if-right {
		display: flex;
		align-items: center;
		gap: .6rem;
		flex-shrink: 0;
	}

	.if-badge {
		padding: .1rem .55rem;
		border-radius: 99px;
		font-size: .7rem;
		font-weight: 600;
		background: var(--azul-claro);
		color: var(--azul);
	}

	.badge-admin {
		background: #fef3c7;
		color: #92400e;
	}

	.badge-profesor {
		background: #d1fae5;
		color: #065f46;
	}

	.badge-alumno {
		background: #ede9fe;
		color: #5b21b6;
	}

	.badge-staff {
		background: var(--azul-claro);
		color: var(--azul);
	}

	.if-copy {
		font-size: .7rem;
		opacity: .5;
	}

	@media (max-width: 576px) {

		.if-email,
		.if-sep,
		.if-copy {
			display: none;
		}

		.if-nombre {
			max-width: 140px;
		}
	}
</style>

<footer class="intranet-footer" role="contentinfo">
	<div class="if-user">
		<?php if ($_foto): ?>
			<img src="<?= htmlspecialchars($_foto) ?>" alt="" class="if-avatar" referrerpolicy="no-referrer">
		<?php else: ?>
			<div class="if-avatar-ph" aria-hidden="true">
				<i class="bi bi-person-fill"></i>
			</div>
		<?php endif; ?>

		<?php if ($_nombre): ?>
			<span class="if-nombre"><?= htmlspecialchars($_nombre) ?></span>
		<?php endif; ?>

		<?php if ($_nombre && $_email): ?>
			<span class="if-sep">·</span>
		<?php endif; ?>

		<?php if ($_email): ?>
			<span class="if-email"><?= htmlspecialchars($_email) ?></span>
		<?php endif; ?>

		<?php if (!$_nombre && !$_email): ?>
			<span class="fst-italic">Sin sesión</span>
		<?php endif; ?>
	</div>

	<div class="if-right">
		<?php if ($_rol_label): ?>
			<span class="if-badge <?= $_rol_class ?>"><?= $_rol_label ?></span>
		<?php endif; ?>
		<span class="if-copy">Intranet &copy; <?= date('Y') ?></span>
	</div>
</footer>