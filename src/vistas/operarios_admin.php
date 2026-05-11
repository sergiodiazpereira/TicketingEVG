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
    <link rel="stylesheet" href="../css/estiloAdmin.css">
</head>

<body id="pagina-dashboard">

    <!-- Truco CSS puro: Checkbox oculto para colapsar el sidebar sin JS -->
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
                    <a class="nav-link active" href="operarios_admin.php" title="Operarios">
                        <i class="bi bi-person-badge-fill me-3"></i>
                        <span class="link-texto">Operarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categorias_admin.php" title="Categorías">
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

            <header
                class="bg-white p-4 mb-4 border-bottom shadow-sm d-flex justify-content-between align-items-center sticky-top">
                <div class="d-flex align-items-center">
                    <h1 class="h5 fw-bold mb-0" style="color: #003366;">Gestión de responsables y trabajadores</h1>
                </div>
            </header>

            <div class="container-fluid px-4">
                <div class="row">
                    <div class="col-9">
                        <h2 class="fw-bold">LISTADO DE PERSONAL TÉCNICO</h2>
                        <p class="text-muted small">Aquí podrás gestionar los responsables y trabajadores del sistema de
                            ticketing.</p>
                    </div>
                    <div class="col-3 d-flex align-items-center">
                        <button type="button" class="btn btn-evg w-100 p-3 rounded-4 shadow fw-bold">
                            <i class="bi bi-plus-circle me-2"></i>
                            AÑADIR OPERARIOS
                        </button>
                    </div>

                </div>
            </div>
            <div class="row m-4">
                <div class="col-12">
                    <div class="card card-premium">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 tabla-operarios">
                                    <thead class="text-uppercase border-bottom">
                                        <tr>
                                            <th scope="col" class="border-0 ps-4 py-3">Personal y tipo</th>
                                            <th scope="col" class="border-0 py-3">Categorías</th>
                                            <th scope="col" class="border-0 py-3">Correo electrónico</th>
                                            <th scope="col" class="border-0 py-3 text-center">Tickets</th>
                                            <th scope="col" class="border-0 py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        <!-- Fila 1 -->
                                        <tr>
                                            <td class="ps-4 border-0 border-bottom">
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name=Carlos+Ruiz&background=random"
                                                        class="rounded-circle me-3 shadow-sm" width="42" height="42"
                                                        alt="Avatar">
                                                    <div>
                                                        <span class="fw-bold text-dark me-2">Carlos Ruiz</span>
                                                        <span class="badge badge-responsable rounded-pill px-2 py-1"
                                                            style="font-size: 0.65rem;">RESPONSABLE</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-0 border-bottom">
                                                <div class="d-flex align-items-center text-muted">
                                                    <div
                                                        class="icono-categoria rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="bi bi-person"
                                                            style="color: var(--amarillo); font-size: 1.1rem;"></i>
                                                    </div>
                                                    <span class="fw-medium text-dark me-1" style="font-size: 0.9rem;">3
                                                        CATEGORÍAS</span>
                                                    <i class="bi bi-chevron-down small ms-1"></i>
                                                </div>
                                            </td>
                                            <td class="border-0 border-bottom text-muted" style="font-size: 0.9rem;">
                                                <i class="bi bi-envelope me-2"></i> carlos.ruiz@evg.es
                                            </td>
                                            <td class="border-0 border-bottom text-center">
                                                <div
                                                    class="d-flex flex-column align-items-center justify-content-center">
                                                    <span class="fw-bold fs-5 text-dark lh-1 mb-1">5</span>
                                                    <span class="text-muted small fw-bold"
                                                        style="font-size: 0.6rem; letter-spacing: 0.5px;">ASIGNADOS</span>
                                                </div>
                                            </td>
                                            <td class="border-0 border-bottom text-end pe-4">
                                                <button class="btn btn-link text-muted p-0"><i
                                                        class="bi bi-three-dots-vertical"></i></button>
                                            </td>
                                        </tr>

                                        <!-- Fila 2 -->
                                        <tr>
                                            <td class="ps-4 border-0 border-bottom">
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name=Ana+Martinez&background=random"
                                                        class="rounded-circle me-3 shadow-sm" width="42" height="42"
                                                        alt="Avatar">
                                                    <div>
                                                        <span class="fw-bold text-dark me-2">Ana Martínez</span>
                                                        <span class="badge badge-trabajador rounded-pill px-2 py-1"
                                                            style="font-size: 0.65rem;">TRABAJADOR</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-0 border-bottom">
                                                <div class="d-flex align-items-center text-muted">
                                                    <div
                                                        class="icono-categoria rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="bi bi-briefcase text-secondary"
                                                            style="font-size: 1.1rem;"></i>
                                                    </div>
                                                    <span class="fw-medium text-dark me-1" style="font-size: 0.9rem;">2
                                                        CATEGORÍAS</span>
                                                    <i class="bi bi-chevron-down small ms-1"></i>
                                                </div>
                                            </td>
                                            <td class="border-0 border-bottom text-muted" style="font-size: 0.9rem;">
                                                <i class="bi bi-envelope me-2"></i> ana.martinez@evg.es
                                            </td>
                                            <td class="border-0 border-bottom text-center">
                                                <div
                                                    class="d-flex flex-column align-items-center justify-content-center">
                                                    <span class="fw-bold fs-5 text-dark lh-1 mb-1">3</span>
                                                    <span class="text-muted small fw-bold"
                                                        style="font-size: 0.6rem; letter-spacing: 0.5px;">ASIGNADOS</span>
                                                </div>
                                            </td>
                                            <td class="border-0 border-bottom text-end pe-4">
                                                <button class="btn btn-link text-muted p-0"><i
                                                        class="bi bi-three-dots-vertical"></i></button>
                                            </td>
                                        </tr>

                                        <!-- Fila 3 -->
                                        <tr>
                                            <td class="ps-4 border-0 border-bottom">
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name=Roberto+Gomez&background=random"
                                                        class="rounded-circle me-3 shadow-sm" width="42" height="42"
                                                        alt="Avatar">
                                                    <div>
                                                        <span class="fw-bold text-dark me-2">Roberto Gómez</span>
                                                        <span class="badge badge-trabajador rounded-pill px-2 py-1"
                                                            style="font-size: 0.65rem;">TRABAJADOR</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-0 border-bottom">
                                                <div class="d-flex align-items-center text-muted">
                                                    <div
                                                        class="icono-categoria rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="bi bi-briefcase text-secondary"
                                                            style="font-size: 1.1rem;"></i>
                                                    </div>
                                                    <span class="fw-medium text-dark me-1" style="font-size: 0.9rem;">1
                                                        CATEGORÍA</span>
                                                    <i class="bi bi-chevron-down small ms-1"></i>
                                                </div>
                                            </td>
                                            <td class="border-0 border-bottom text-muted" style="font-size: 0.9rem;">
                                                <i class="bi bi-envelope me-2"></i> roberto.gomez@evg.es
                                            </td>
                                            <td class="border-0 border-bottom text-center">
                                                <div
                                                    class="d-flex flex-column align-items-center justify-content-center">
                                                    <span class="fw-bold fs-5 text-dark lh-1 mb-1">2</span>
                                                    <span class="text-muted small fw-bold"
                                                        style="font-size: 0.6rem; letter-spacing: 0.5px;">ASIGNADOS</span>
                                                </div>
                                            </td>
                                            <td class="border-0 border-bottom text-end pe-4">
                                                <button class="btn btn-link text-muted p-0"><i
                                                        class="bi bi-three-dots-vertical"></i></button>
                                            </td>
                                        </tr>

                                        <!-- Fila 4 -->
                                        <tr>
                                            <td class="ps-4 border-0">
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name=Elena+Sanz&background=random"
                                                        class="rounded-circle me-3 shadow-sm" width="42" height="42"
                                                        alt="Avatar">
                                                    <div>
                                                        <span class="fw-bold text-dark me-2">Elena Sanz</span>
                                                        <span class="badge badge-responsable rounded-pill px-2 py-1"
                                                            style="font-size: 0.65rem;">RESPONSABLE</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-0">
                                                <div class="d-flex align-items-center text-muted">
                                                    <div
                                                        class="icono-categoria rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="bi bi-person"
                                                            style="color: var(--amarillo); font-size: 1.1rem;"></i>
                                                    </div>
                                                    <span class="fw-medium text-dark me-1" style="font-size: 0.9rem;">2
                                                        CATEGORÍAS</span>
                                                    <i class="bi bi-chevron-down small ms-1"></i>
                                                </div>
                                            </td>
                                            <td class="border-0 text-muted" style="font-size: 0.9rem;">
                                                <i class="bi bi-envelope me-2"></i> elena.sanz@evg.es
                                            </td>
                                            <td class="border-0 text-center">
                                                <div
                                                    class="d-flex flex-column align-items-center justify-content-center">
                                                    <span class="fw-bold fs-5 text-dark lh-1 mb-1">0</span>
                                                    <span class="text-muted small fw-bold"
                                                        style="font-size: 0.6rem; letter-spacing: 0.5px;">ASIGNADOS</span>
                                                </div>
                                            </td>
                                            <td class="border-0 text-end pe-4">
                                                <button class="btn btn-link text-muted p-0"><i
                                                        class="bi bi-three-dots-vertical"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Tabla -->
                    <div class="text-center mt-4 mb-5">
                        <span class="texto-footer-tabla text-uppercase">Mostrando 4 registros del equipo técnico y
                            responsables de la organización</span>
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
