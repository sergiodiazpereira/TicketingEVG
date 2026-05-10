<!DOCTYPE html>
<html class="joseph" lang="es">

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
    <link rel="stylesheet" href="css/estiloAdmin.css">
</head>

<body id="pagina-dashboard">

    <input type="checkbox" id="toggle-sidebar" class="d-none">

    <div class="d-flex overflow-hidden">
        <!-- Sidebar Dinámico -->
        <nav id="sidebarMenu" class="sidebar d-flex flex-column vh-100 sticky-top">
            <!-- Header Sidebar -->
            <div class="sidebar-header p-4 mb-3 d-flex align-items-center">
                <label for="toggle-sidebar" class="boton-candado rounded-3 p-2 me-3 flex-shrink-0"
                    style="cursor: pointer; background-color: var(--amarillo);" title="Abrir/Cerrar menú">
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
                    <a class="nav-link" href="dashboard_admin.php" title="Dashboard">
                        <i class="bi bi-grid-fill me-3"></i>
                        <span class="link-texto">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="operarios_admin.php" title="Operarios">
                        <i class="bi bi-person-badge-fill me-3"></i>
                        <span class="link-texto">Operarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="categorias_admin.php" title="Categorías">
                        <i class="bi bi-tag-fill me-3"></i>
                        <span class="link-texto">Categorías</span>
                    </a>
                <li class="nav-item">
                    <a class="nav-link" href="#" title="Cerrar sesión">
                        <i class="bi bi-box-arrow-left me-3"></i>
                        <span class="link-texto">Cerrar sesión</span>
                    </a>
                </li>
            </ul>

        </nav>

        <!-- Contenido Principal -->
        <main class="flex-grow-1 vh-100 overflow-auto">

            <header class="bg-white p-4 mb-4 border-bottom shadow-sm d-flex justify-content-between align-items-center sticky-top">
                <div class="d-flex align-items-center">
                    <h1 class="h5 fw-bold mb-0" style="color: #003366;">Gestión de categorías de soporte</h1>
                </div>
            </header>

            <div class="container-fluid px-4">
                <div class="row">
                    <div class="col-9">
                        <h2 class="fw-bold text-uppercase" style="color: #003366;">GESTIÓN DE CATEGORÍAS</h2>
                        <p class="text-muted small">Administra las áreas técnicas para la clasificación de personal y tickets.</p>
                    </div>
                    <div class="col-3 d-flex align-items-center">
                        <button type="button" class="btn btn-evg w-100 p-3 rounded-4 shadow fw-bold text-uppercase">
                            <i class="bi bi-plus-lg me-2"></i>
                            AÑADIR CATEGORÍA
                        </button>
                    </div>
                </div>

                <div class="row mt-4 mb-4">
                    <div class="col-12">
                        <div class="card card-premium">
                            <div class="card-body p-0">
                                <div class="table-responsive tabla-scroll-container">
                                    <table class="table align-middle mb-0 tabla-categorias">
                                        <thead class="text-uppercase">
                                            <tr>
                                                <th scope="col" class="border-0 text-center py-3" style="width: 25%;">Categoría</th>
                                                <th scope="col" class="border-0 py-3" style="width: 50%;">Descripción</th>
                                                <th scope="col" class="border-0 py-3 text-center" style="width: 20%;">En uso</th>
                                                <th scope="col" class="border-0 py-3 text-end pe-4" style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="border-top-0">
                                            <!-- Fila 1 -->
                                            <tr>
                                                <td class="border-0 border-bottom text-center">
                                                    <div class="d-flex flex-column align-items-center justify-content-center py-2">
                                                        <div class="icono-categoria-grande rounded-4 d-flex align-items-center justify-content-center mb-2">
                                                            <i class="bi bi-tag" style="transform: rotate(-45deg);"></i>
                                                        </div>
                                                        <span class="fw-bold" style="color: #003366; font-size: 0.85rem;">SOPORTE NIVEL 1</span>
                                                    </div>
                                                </td>
                                                <td class="border-0 border-bottom">
                                                    <div class="d-flex align-items-start text-muted">
                                                        <i class="bi bi-file-earmark-text me-3 mt-1" style="color: #aab2bd; font-size: 1.1rem;"></i>
                                                        <p class="mb-0" style="font-size: 0.95rem;">Atención primaria y resolución de incidencias básicas de primer contacto.</p>
                                                    </div>
                                                </td>
                                                <td class="border-0 border-bottom">
                                                    <div class="d-flex gap-4 justify-content-center">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <span class="badge badge-operarios rounded-pill d-flex align-items-center justify-content-center mb-1">
                                                                <i class="bi bi-person me-1"></i> 8
                                                            </span>
                                                            <span class="text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">OPERARIOS</span>
                                                        </div>
                                                        <div class="d-flex flex-column align-items-center">
                                                            <span class="badge badge-tickets rounded-pill d-flex align-items-center justify-content-center mb-1">
                                                                <i class="bi bi-ticket-detailed me-1"></i> 15
                                                            </span>
                                                            <span class="text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">TICKETS</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="border-0 border-bottom text-end pe-4">
                                                    <button class="btn btn-link text-muted p-0"><i class="bi bi-three-dots-vertical"></i></button>
                                                </td>
                                            </tr>

                                            <!-- Fila 2 -->
                                            <tr>
                                                <td class="border-0 border-bottom text-center">
                                                    <div class="d-flex flex-column align-items-center justify-content-center py-2">
                                                        <div class="icono-categoria-grande rounded-4 d-flex align-items-center justify-content-center mb-2">
                                                            <i class="bi bi-tag" style="transform: rotate(-45deg);"></i>
                                                        </div>
                                                        <span class="fw-bold" style="color: #003366; font-size: 0.85rem;">SOPORTE NIVEL 2</span>
                                                    </div>
                                                </td>
                                                <td class="border-0 border-bottom">
                                                    <div class="d-flex align-items-start text-muted">
                                                        <i class="bi bi-file-earmark-text me-3 mt-1" style="color: #aab2bd; font-size: 1.1rem;"></i>
                                                        <p class="mb-0" style="font-size: 0.95rem;">Escalado técnico para problemas complejos de sistemas y software corporativo.</p>
                                                    </div>
                                                </td>
                                                <td class="border-0 border-bottom">
                                                    <div class="d-flex gap-4 justify-content-center">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <span class="badge badge-operarios rounded-pill d-flex align-items-center justify-content-center mb-1">
                                                                <i class="bi bi-person me-1"></i> 5
                                                            </span>
                                                            <span class="text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">OPERARIOS</span>
                                                        </div>
                                                        <div class="d-flex flex-column align-items-center">
                                                            <span class="badge badge-tickets rounded-pill d-flex align-items-center justify-content-center mb-1">
                                                                <i class="bi bi-ticket-detailed me-1"></i> 8
                                                            </span>
                                                            <span class="text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">TICKETS</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="border-0 border-bottom text-end pe-4">
                                                    <button class="btn btn-link text-muted p-0"><i class="bi bi-three-dots-vertical"></i></button>
                                                </td>
                                            </tr>

                                            <!-- Fila 3 -->
                                            <tr>
                                                <td class="border-0 border-bottom text-center">
                                                    <div class="d-flex flex-column align-items-center justify-content-center py-2">
                                                        <div class="icono-categoria-grande rounded-4 d-flex align-items-center justify-content-center mb-2">
                                                            <i class="bi bi-tag" style="transform: rotate(-45deg);"></i>
                                                        </div>
                                                        <span class="fw-bold" style="color: #003366; font-size: 0.85rem;">MANTENIMIENTO GENERAL</span>
                                                    </div>
                                                </td>
                                                <td class="border-0 border-bottom">
                                                    <div class="d-flex align-items-start text-muted">
                                                        <i class="bi bi-file-earmark-text me-3 mt-1" style="color: #aab2bd; font-size: 1.1rem;"></i>
                                                        <p class="mb-0" style="font-size: 0.95rem;">Reparaciones físicas e infraestructura de la escuela.</p>
                                                    </div>
                                                </td>
                                                <td class="border-0 border-bottom">
                                                    <div class="d-flex gap-4 justify-content-center">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <span class="badge badge-operarios rounded-pill d-flex align-items-center justify-content-center mb-1">
                                                                <i class="bi bi-person me-1"></i> 12
                                                            </span>
                                                            <span class="text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">OPERARIOS</span>
                                                        </div>
                                                        <div class="d-flex flex-column align-items-center">
                                                            <span class="badge badge-tickets rounded-pill d-flex align-items-center justify-content-center mb-1">
                                                                <i class="bi bi-ticket-detailed me-1"></i> 24
                                                            </span>
                                                            <span class="text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">TICKETS</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="border-0 border-bottom text-end pe-4">
                                                    <button class="btn btn-link text-muted p-0"><i class="bi bi-three-dots-vertical"></i></button>
                                                </td>
                                            </tr>

                                            <!-- Fila 4 -->
                                            <tr>
                                                <td class="border-0 border-bottom text-center">
                                                    <div class="d-flex flex-column align-items-center justify-content-center py-2">
                                                        <div class="icono-categoria-grande rounded-4 d-flex align-items-center justify-content-center mb-2">
                                                            <i class="bi bi-tag" style="transform: rotate(-45deg);"></i>
                                                        </div>
                                                        <span class="fw-bold" style="color: #003366; font-size: 0.85rem;">REDES Y SISTEMAS</span>
                                                    </div>
                                                </td>
                                                <td class="border-0 border-bottom">
                                                    <div class="d-flex align-items-start text-muted">
                                                        <i class="bi bi-file-earmark-text me-3 mt-1" style="color: #aab2bd; font-size: 1.1rem;"></i>
                                                        <p class="mb-0" style="font-size: 0.95rem;">Gestión de infraestructura de red, Wi-Fi y conectividad general.</p>
                                                    </div>
                                                </td>
                                                <td class="border-0 border-bottom">
                                                    <div class="d-flex gap-4 justify-content-center">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <span class="badge badge-operarios rounded-pill d-flex align-items-center justify-content-center mb-1">
                                                                <i class="bi bi-person me-1"></i> 4
                                                            </span>
                                                            <span class="text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">OPERARIOS</span>
                                                        </div>
                                                        <div class="d-flex flex-column align-items-center">
                                                            <span class="badge badge-tickets rounded-pill d-flex align-items-center justify-content-center mb-1">
                                                                <i class="bi bi-ticket-detailed me-1"></i> 6
                                                            </span>
                                                            <span class="text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">TICKETS</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="border-0 border-bottom text-end pe-4">
                                                    <button class="btn btn-link text-muted p-0"><i class="bi bi-three-dots-vertical"></i></button>
                                                </td>
                                            </tr>

                                            <!-- Fila 5 -->
                                            <tr>
                                                <td class="border-0 text-center">
                                                    <div class="d-flex flex-column align-items-center justify-content-center py-2">
                                                        <div class="icono-categoria-grande rounded-4 d-flex align-items-center justify-content-center mb-2">
                                                            <i class="bi bi-tag" style="transform: rotate(-45deg);"></i>
                                                        </div>
                                                        <span class="fw-bold" style="color: #003366; font-size: 0.85rem;">SEGURIDAD Y ACCESOS</span>
                                                    </div>
                                                </td>
                                                <td class="border-0">
                                                    <div class="d-flex align-items-start text-muted">
                                                        <i class="bi bi-file-earmark-text me-3 mt-1" style="color: #aab2bd; font-size: 1.1rem;"></i>
                                                        <p class="mb-0" style="font-size: 0.95rem;">Sistemas de videovigilancia e infraestructura de seguridad.</p>
                                                    </div>
                                                </td>
                                                <td class="border-0">
                                                    <div class="d-flex gap-4 justify-content-center">
                                                        <div class="d-flex flex-column align-items-center">
                                                            <span class="badge badge-operarios rounded-pill d-flex align-items-center justify-content-center mb-1">
                                                                <i class="bi bi-person me-1"></i> 3
                                                            </span>
                                                            <span class="text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">OPERARIOS</span>
                                                        </div>
                                                        <div class="d-flex flex-column align-items-center">
                                                            <span class="badge badge-tickets rounded-pill d-flex align-items-center justify-content-center mb-1">
                                                                <i class="bi bi-ticket-detailed me-1"></i> 2
                                                            </span>
                                                            <span class="text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">TICKETS</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="border-0 text-end pe-4">
                                                    <button class="btn btn-link text-muted p-0"><i class="bi bi-three-dots-vertical"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Tabla -->
                        <div class="text-center mt-4 mb-5">
                            <span class="texto-footer-tabla text-uppercase">SISTEMA DE CLASIFICACIÓN TÉCNICA TICKETINGEVG</span>
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
    
    <?php include 'footer.php'; ?>
</body>

</html>
