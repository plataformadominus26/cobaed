<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php require_once __DIR__ . '/../config.php'; echo plantel_nombre(); ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css?v=1.02">
</head>
<body>
    <!-- Side Navigation -->
   <?php
    include_once("../menu.php");
   ?>

    <!-- Body Content -->
    <div class="container-fluid">
        <!-- Stats Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="data-card card glass-panel">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Órdenes Hoy</h6>
                                <h3>48</h3>
                                <span class="text-success"><i class="fas fa-arrow-up"></i> 12%</span>
                            </div>
                            <div class="card-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="data-card card glass-panel">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Ingresos</h6>
                                <h3>$12,450</h3>
                                <span class="text-success"><i class="fas fa-arrow-up"></i> 8%</span>
                            </div>
                            <div class="card-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="data-card card glass-panel">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Clientes Nuevos</h6>
                                <h3>9</h3>
                                <span class="text-danger"><i class="fas fa-arrow-down"></i> 3%</span>
                            </div>
                            <div class="card-icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="data-card card glass-panel">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Rating</h6>
                                <h3>4.7 <small>/5</small></h3>
                                <span class="text-success"><i class="fas fa-star"></i> +0.2</span>
                            </div>
                            <div class="card-icon">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Data Area -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card glass-panel mb-4">
                    <div class="card-body">
                        <ul class="nav custom-tabs" id="dataTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button">Órdenes</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="menu-tab" data-bs-toggle="tab" data-bs-target="#menu" type="button">Menú</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button">Personal</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content mt-3" id="dataTabsContent">
                            <div class="tab-pane fade show active" id="orders" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th># Orden</th>
                                                <th>Mesa</th>
                                                <th>Total</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>#1254</td>
                                                <td>5</td>
                                                <td>$1,250.00</td>
                                                <td><span class="badge bg-success">Completada</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>#1253</td>
                                                <td>2</td>
                                                <td>$850.00</td>
                                                <td><span class="badge bg-warning">En Cocina</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>#1252</td>
                                                <td>8</td>
                                                <td>$1,750.00</td>
                                                <td><span class="badge bg-danger">Cancelada</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="menu" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="Buscar en menú...">
                                            <button class="btn btn-outline-secondary" type="button">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Nuevo Plato
                                        </button>
                                    </div>
                                </div>
                                <p>Gestión de menú aparecerá aquí</p>
                            </div>
                            
                            <div class="tab-pane fade" id="staff" role="tabpanel">
                                <p>Gestión de personal aparecerá aquí</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card glass-panel mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Actividad Reciente</h5>
                        <div class="activity-list">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-bell bg-primary text-white p-2 rounded-circle"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <small class="text-muted">Hace 5 min</small>
                                    <p class="mb-0">Nueva orden recibida (#1254)</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user bg-success text-white p-2 rounded-circle"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <small class="text-muted">Hace 1 hora</small>
                                    <p class="mb-0">Cliente registrado: Juan Pérez</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation bg-danger text-white p-2 rounded-circle"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <small class="text-muted">Hace 2 horas</small>
                                    <p class="mb-0">Orden cancelada (#1252)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card glass-panel">
                    <div class="card-body">
                        <h5 class="card-title">Tareas Pendientes</h5>
                        <div class="task-list">
                            <div class="task-item">
                                <div class="d-flex justify-content-between">
                                    <strong>Actualizar menú estacional</strong>
                                    <small class="text-muted">Hoy</small>
                                </div>
                                <p class="mb-0">Agregar nuevos platos de verano</p>
                            </div>
                            <div class="task-item">
                                <div class="d-flex justify-content-between">
                                    <strong>Revisar inventario</strong>
                                    <small class="text-muted">Mañana</small>
                                </div>
                                <p class="mb-0">Verificar niveles de stock</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            <!-- Footer -->
            <?php
             include_once("../footer.php");
            ?>
        
 

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle side navigation on mobile
        document.querySelector('.nav-toggle').addEventListener('click', function() {
            document.querySelector('.side-nav').classList.toggle('show');
        });
        
        // Simulate loading data
        setTimeout(function() {
            // This would be replaced with actual data loading
            console.log("Data loaded");
        }, 1000);
    </script>
</body>
</html>