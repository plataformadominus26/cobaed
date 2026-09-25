<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin Pendientes - Horarios de Profesores</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1a2a6c;
            --secondary-color: #b21f1f;
            --accent-color: #fdbb2d;
            --bg-color: #f8f9fa;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --sidebar-bg: linear-gradient(135deg, #1a2a6c, #b21f1f);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Nunito', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            color: #333;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .side-nav {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: var(--sidebar-bg);
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .nav-logo {
            padding: 20px;
            font-size: 1.5rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
        }
        
        .logo-img {
            max-width: 180px;
            height: auto;
        }
        
        .nav-menu {
            padding: 20px 0;
            height: calc(100vh - 80px);
            overflow-y: auto;
        }
        
        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-link {
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: var(--accent-color);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .rotate-180 {
            transform: rotate(180deg);
            transition: transform 0.3s ease;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Header */
        .top-header {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header-icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .icon-badge {
            position: relative;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: background 0.3s;
        }
        
        .icon-badge:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        .icon-badge .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.7rem;
            padding: 3px 6px;
        }
        
        .user-menu img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
            background: white;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px 25px;
            border-radius: 12px 12px 0 0 !important;
        }
        
        .card-body {
            padding: 25px;
        }
        
        /* Button Styles */
        .btn-action {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s;
        }
        
        .btn-add {
            background: linear-gradient(135deg, var(--primary-color), #2a3a8c);
        }
        
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 42, 108, 0.3);
        }
        
        /* Table Styles */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            padding: 15px;
        }
        
        .table td {
            padding: 15px;
            vertical-align: middle;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(26, 42, 108, 0.03);
        }
        
        /* Badge Styles */
        .badge {
            font-size: 0.75rem;
            padding: 6px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .badge-role {
            font-size: 0.7rem;
        }
        
        .badge-admin {
            background: linear-gradient(135deg, #6f42c1, #8c63d3);
            color: white;
        }
        
        /* Action Buttons */
        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.3s;
        }
        
        .btn-edit {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .btn-edit:hover {
            background: #28a745;
            color: white;
        }
        
        .btn-delete {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .btn-delete:hover {
            background: #dc3545;
            color: white;
        }
        
        /* Schedule Table Styles */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .schedule-table th, .schedule-table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: center;
            vertical-align: middle;
        }
        
        .schedule-table th {
            background: linear-gradient(135deg, var(--primary-color), #2a3a8c);
            color: white;
            font-weight: 600;
        }
        
        .schedule-table td {
            background: white;
            transition: all 0.3s;
            min-height: 60px;
        }
        
        .schedule-table td:hover {
            background: rgba(26, 42, 108, 0.05);
            cursor: pointer;
        }
        
        .time-slot {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .class-scheduled {
            background: rgba(40, 167, 69, 0.15);
            border-left: 4px solid #28a745;
            font-weight: 500;
        }
        
        .class-break {
            background: rgba(108, 117, 125, 0.1);
            font-style: italic;
            color: #6c757d;
        }
        
        /* Footer */
        .main-footer {
            margin-top: auto;
            padding: 15px 30px;
            background: var(--glass-bg);
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        /* Modal Styles */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), #2a3a8c);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 20px 25px;
        }
        
        .btn-close-white {
            filter: invert(1);
        }
        
        .form-floating {
            margin-bottom: 1rem;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .side-nav {
                transform: translateX(-100%);
            }
            
            .side-nav.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .nav-toggle {
                display: block !important;
            }
        }
        
        /* Custom styles for schedule module */
        .schedule-controls {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .teacher-info-card {
            background: linear-gradient(135deg, var(--primary-color), #2a3a8c);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .schedule-legend {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }
        
        .legend-color {
            width: 15px;
            height: 15px;
            border-radius: 3px;
        }
        
        .legend-class {
            background: rgba(40, 167, 69, 0.15);
            border-left: 3px solid #28a745;
        }
        
        .legend-break {
            background: rgba(108, 117, 125, 0.1);
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="side-nav">
        <div class="nav-logo text-white">
            <img src="https://via.placeholder.com/180x40/1a2a6c/ffffff?text=sinPendientes" alt="Logo" class="logo-img">
        </div>
        
        <div class="nav-menu">
            <div class="nav-item">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-easel"></i> Dashboard
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#scheduleSubMenu" class="nav-link text-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse" aria-expanded="true">
                    <span>
                        <i class="bi bi-calendar-week"></i> Horarios
                    </span>
                    <i class="bi bi-chevron-down ms-2"></i>
                </a>
                <div class="collapse show" id="scheduleSubMenu">
                    <ul class="nav flex-column ms-4">
                        <li class="nav-item">
                            <a href="#" class="nav-link text-white active">
                                <i class="bi bi-person-video3"></i> Profesores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link text-white">
                                <i class="bi bi-building"></i> Aulas
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-people-fill"></i> Estudiantes
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link text-white">
                    <i class="bi bi-journal-text"></i> Cursos
                </a>
            </div>
            
            <div class="position-absolute bottom-0 w-100 mb-4">
                <div class="nav-item">
                    <a href="#" class="nav-link text-white">
                        <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="top-header glass-panel">
            <button class="btn btn-sm btn-outline-secondary nav-toggle d-none">
                <i class="bi bi-list"></i>
            </button>
            
            <div class="locale-selector">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="localeDropdown" data-bs-toggle="dropdown">
                    <i class="bi bi-globe2"></i> Español (MX)
                </button>
                <div class="dropdown-menu">
                    <h6 class="dropdown-header">Idioma/Región</h6>
                    <a class="dropdown-item" href="#">Español (MX)</a>
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">Zona Horaria</h6>
                    <a class="dropdown-item" href="#">GMT-6 (Centro)</a>
                    <a class="dropdown-item" href="#">GMT-7 (Pacífico)</a>
                </div>
            </div>
            
            <div class="header-icons d-flex align-items-center">
                <div class="icon-badge">
                    <i class="bi bi-bell-fill"></i>
                    <span class="badge bg-danger">3</span>
                </div>
                <div class="icon-badge">
                    <i class="bi bi-envelope-fill"></i>
                    <span class="badge bg-primary">5</span>
                </div>
                
                <div class="user-menu dropdown ms-4">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User">
                        <span class="d-none d-md-inline">Admin</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Administrador</h6></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person-fill me-2"></i> Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear-fill me-2"></i> Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-left"></i> Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Teacher Schedule Module -->
        <div class="container-fluid py-4">
            <!-- Action Header -->
            <div class="card">
                <div class="card-header">
                    <div class="header-actions d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="mb-0">Horarios de Profesores</h5>
                        
                        <div class="d-flex flex-wrap gap-2">
                            <!-- Add Button -->
                            <button class="btn btn-add btn-action text-white" id="btAgregar">
                                <i class="fas fa-plus me-1"></i> Nuevo Horario
                            </button>
                            
                            <!-- Export Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-warning btn-action dropdown-toggle" type="button" id="dropdownExport" data-bs-toggle="dropdown">
                                    <i class="fas fa-download me-1"></i> Exportar
                                </button>
                                <ul class="dropdown-menu">
                                    <li><h6 class="dropdown-header">Formato de exportación</h6></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2 text-success"></i> Excel</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2 text-danger"></i> PDF</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-csv me-2 text-primary"></i> CSV</a></li>
                                </ul>
                            </div>
                            
                            <!-- Teacher Filter -->
                            <div class="dropdown">
                                <button class="btn btn-primary btn-action dropdown-toggle" type="button" id="dropdownFilter" data-bs-toggle="dropdown">
                                    <i class="fas fa-filter me-1"></i> Profesores
                                </button>
                                <ul class="dropdown-menu">
                                    <li><h6 class="dropdown-header">Filtrar por profesor:</h6></li>
                                    <li><a class="dropdown-item" href="#">Todos</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#">Prof. Carlos Mendoza</a></li>
                                    <li><a class="dropdown-item" href="#">Prof. Ana López</a></li>
                                    <li><a class="dropdown-item" href="#">Prof. Javier Ruiz</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Teacher Info -->
                    <div class="teacher-info-card">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4>Prof. Carlos Mendoza</h4>
                                <p class="mb-0">Matemáticas | Departamento de Ciencias Exactas</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-outline-light">
                                    <i class="bi bi-pencil-square me-1"></i> Editar Información
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Schedule Controls -->
                    <div class="schedule-controls">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="semesterSelect">
                                        <option selected>Semestre 2025-1</option>
                                        <option>Semestre 2024-2</option>
                                        <option>Semestre 2024-1</option>
                                    </select>
                                    <label for="semesterSelect">Semestre</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="weekSelect">
                                        <option selected>Semana 1-15 (Completa)</option>
                                        <option>Semana 1-7 (Primera Mitad)</option>
                                        <option>Semana 8-15 (Segunda Mitad)</option>
                                    </select>
                                    <label for="weekSelect">Rango de Semanas</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-action h-100 w-100">
                                    <i class="fas fa-sync-alt me-1"></i> Actualizar Horario
                                </button>
                            </div>
                        </div>
                        
                        <!-- Legend -->
                        <div class="schedule-legend">
                            <div class="legend-item">
                                <div class="legend-color legend-class"></div>
                                <span>Clase Programada</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color legend-break"></div>
                                <span>Descanso/No Clase</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Schedule Table -->
                    <div class="table-responsive">
                        <table class="schedule-table">
                            <thead>
                                <tr>
                                    <th width="12%">HORA</th>
                                    <th width="17.6%">LUNES</th>
                                    <th width="17.6%">MARTES</th>
                                    <th width="17.6%">MIÉRCOLES</th>
                                    <th width="17.6%">JUEVES</th>
                                    <th width="17.6%">VIERNES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="time-slot">7:00 - 8:00</td>
                                    <td></td>
                                    <td>Matemáticas I<br><small>Aula 201</small></td>
                                    <td></td>
                                    <td>Matemáticas I<br><small>Aula 201</small></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="time-slot">8:00 - 9:00</td>
                                    <td>Cálculo Diferencial<br><small>Aula 305</small></td>
                                    <td>Matemáticas I<br><small>Aula 201</small></td>
                                    <td>Cálculo Diferencial<br><small>Aula 305</small></td>
                                    <td>Matemáticas I<br><small>Aula 201</small></td>
                                    <td>Cálculo Diferencial<br><small>Aula 305</small></td>
                                </tr>
                                <tr>
                                    <td class="time-slot">9:00 - 10:00</td>
                                    <td>Cálculo Diferencial<br><small>Aula 305</small></td>
                                    <td class="class-break">Descanso</td>
                                    <td>Cálculo Diferencial<br><small>Aula 305</small></td>
                                    <td class="class-break">Descanso</td>
                                    <td>Cálculo Diferencial<br><small>Aula 305</small></td>
                                </tr>
                                <tr>
                                    <td class="time-slot">10:00 - 11:00</td>
                                    <td>Álgebra Lineal<br><small>Aula 402</small></td>
                                    <td>Álgebra Lineal<br><small>Aula 402</small></td>
                                    <td>Álgebra Lineal<br><small>Aula 402</small></td>
                                    <td>Álgebra Lineal<br><small>Aula 402</small></td>
                                    <td>Álgebra Lineal<br><small>Aula 402</small></td>
                                </tr>
                                <tr>
                                    <td class="time-slot">11:00 - 12:00</td>
                                    <td class="class-break">Comida</td>
                                    <td class="class-break">Comida</td>
                                    <td class="class-break">Comida</td>
                                    <td class="class-break">Comida</td>
                                    <td class="class-break">Comida</td>
                                </tr>
                                <tr>
                                    <td class="time-slot">1:00 - 2:00</td>
                                    <td>Matemáticas Avanzadas<br><small>Lab. 105</small></td>
                                    <td></td>
                                    <td>Matemáticas Avanzadas<br><small>Lab. 105</small></td>
                                    <td></td>
                                    <td>Matemáticas Avanzadas<br><small>Lab. 105</small></td>
                                </tr>
                                <tr>
                                    <td class="time-slot">2:00 - 3:00</td>
                                    <td>Matemáticas Avanzadas<br><small>Lab. 105</small></td>
                                    <td>Tutorías<br><small>Oficina 23</small></td>
                                    <td>Matemáticas Avanzadas<br><small>Lab. 105</small></td>
                                    <td>Tutorías<br><small>Oficina 23</small></td>
                                    <td>Matemáticas Avanzadas<br><small>Lab. 105</small></td>
                                </tr>
                                <tr>
                                    <td class="time-slot">3:00 - 4:00</td>
                                    <td></td>
                                    <td>Tutorías<br><small>Oficina 23</small></td>
                                    <td></td>
                                    <td>Tutorías<br><small>Oficina 23</small></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="time-slot">4:00 - 5:00</td>
                                    <td>Reunión Depto.<br><small>Sala 10</small></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer glass-panel">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">© 2025 sinPendientes - Sistema de Administración</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0">Versión 1.01 | Última actualización: 15/06/2025</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Schedule Modal -->
    <div class="modal fade modal-user" id="modalSchedule" tabindex="-1" aria-labelledby="modalScheduleLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalScheduleLabel">Nueva Clase en Horario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="scheduleForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="courseSelect">
                                        <option selected>Seleccionar curso</option>
                                        <option>Matemáticas I</option>
                                        <option>Cálculo Diferencial</option>
                                        <option>Álgebra Lineal</option>
                                        <option>Matemáticas Avanzadas</option>
                                    </select>
                                    <label for="courseSelect">Curso</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="classroomSelect">
                                        <option selected>Seleccionar aula</option>
                                        <option>Aula 201</option>
                                        <option>Aula 305</option>
                                        <option>Aula 402</option>
                                        <option>Lab. 105</option>
                                        <option>Oficina 23</option>
                                    </select>
                                    <label for="classroomSelect">Aula/Laboratorio</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="daySelect">
                                        <option selected>Seleccionar día</option>
                                        <option>Lunes</option>
                                        <option>Martes</option>
                                        <option>Miércoles</option>
                                        <option>Jueves</option>
                                        <option>Viernes</option>
                                    </select>
                                    <label for="daySelect">Día de la semana</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="startTime">
                                        <option selected>Seleccionar hora</option>
                                        <option>7:00</option>
                                        <option>8:00</option>
                                        <option>9:00</option>
                                        <option>10:00</option>
                                        <option>11:00</option>
                                        <option>1:00</option>
                                        <option>2:00</option>
                                        <option>3:00</option>
                                        <option>4:00</option>
                                    </select>
                                    <label for="startTime">Hora de inicio</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="endTime">
                                        <option selected>Seleccionar hora</option>
                                        <option>8:00</option>
                                        <option>9:00</option>
                                        <option>10:00</option>
                                        <option>11:00</option>
                                        <option>12:00</option>
                                        <option>2:00</option>
                                        <option>3:00</option>
                                        <option>4:00</option>
                                        <option>5:00</option>
                                    </select>
                                    <label for="endTime">Hora de fin</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" placeholder="Notas adicionales" id="notes" style="height: 100px"></textarea>
                                    <label for="notes">Notas adicionales</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btGuardar">Guardar Clase</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Initialize the schedule module
        document.addEventListener('DOMContentLoaded', function() {
            // Handle sidebar collapse/expand
            $('[data-bs-toggle="collapse"]').click(function (e) {
                const target = $(this).attr('href') || $(this).data('bs-target');
                $('.collapse.show').each(function () {
                    if ('#' + this.id !== target) {
                        $(this).collapse('hide');
                    }
                });
            });
            
            // Rotate chevron on collapse
            $('.collapse')
                .on('show.bs.collapse', function () {
                    const trigger = $('[href="#' + this.id + '"], [data-bs-target="#' + this.id + '"]');
                    trigger.find('.bi-chevron-down').addClass('rotate-180');
                })
                .on('hide.bs.collapse', function () {
                    const trigger = $('[href="#' + this.id + '"], [data-bs-target="#' + this.id + '"]');
                    trigger.find('.bi-chevron-down').removeClass('rotate-180');
                });
            
            // Handle schedule cell clicks
            $('.schedule-table td:not(.time-slot)').click(function() {
                // Remove previous selection
                $('.schedule-table td').removeClass('selected');
                
                // Add selection to current cell
                $(this).addClass('selected');
                
                // Show modal for editing/adding class
                $('#modalSchedule').modal('show');
            });
            
            // Handle "Nuevo Horario" button
            $('#btAgregar').click(function() {
                $('#modalSchedule').modal('show');
            });
            
            // Handle form submission
            $('#btGuardar').click(function() {
                // Here you would typically save the schedule data
                alert('Horario guardado exitosamente');
                $('#modalSchedule').modal('hide');
            });
            
            // Responsive sidebar toggle for mobile
            $('.nav-toggle').click(function() {
                $('.side-nav').toggleClass('active');
            });
        });
    </script>
</body>
</html>