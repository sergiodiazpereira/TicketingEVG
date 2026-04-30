<?php
/*
 * Identificación: Sergio Díaz Pereira
 * Asignatura: Desarrollo de Aplicaciones Web
 * Curso: 2025-2026
 * Descripción: Modal simplificado para el filtrado de tickets.
 */
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtro – TicketingEVG</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="estiloUsuarios.css">
    <style>
        body {
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .modal-filtro {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            width: 220px;
            padding: 6px;
            border: 1px solid var(--border);
        }

        .enlace-filtro {
            display: block;
            padding: 10px 16px;
            color: var(--azul-oscuro);
            text-decoration: none;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            border-radius: 12px;
            transition: all 0.2s;
        }

        .enlace-filtro:hover {
            background: var(--azul);
            color: #fff;
        }
    </style>
</head>

<body>

    <div class="modal-filtro">
        <a href="#" class="enlace-filtro">Mostrar todos</a>
        <a href="#" class="enlace-filtro">Incidencias</a>
        <a href="#" class="enlace-filtro">Peticiones</a>
    </div>

</body>

</html>