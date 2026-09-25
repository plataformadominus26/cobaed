<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/logosp92x92.png">
    <title><?php require_once __DIR__ . '/../config.php'; echo plantel_nombre(); ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css?v=1.11">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css">
    <link rel="stylesheet" href="this.css?v=1.02">

    <!-- Custom CSS -->
     <style>
        .h24 {
    direction: ltr; /* Ensure left-to-right display */
}
    </style>
    
</head>
<body>
    <?php include_once("../menu.php"); ?>

        <!-- User Management Card -->
        <div class="card card-usuarios">
            <!-- Action Header -->
            <div class="header-actions d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0">
                    <i class="fas fa-layer-group me-2"></i> Areas
                </h5>
                
                <div class="d-flex flex-wrap gap-2">
                    <!-- Add Button -->
                    <button class="btn btn-add btn-action text-white" id="btAgregar">
                        <i class="fas fa-plus me-1"></i> Nueva Área
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
                    
                    <!-- Quick Filter -->
                    <div class="dropdown">
                        <button class="btn btn-primary btn-action dropdown-toggle" type="button" id="dropdownFilter" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i> Filtros
                        </button>
                        <form class="dropdown-menu p-3" style="min-width: 250px;">
                             <div class="mb-3">
                                <label for="filterSucursal" class="form-label mb-1">Sucursal</label>
                                <select class="form-select" id="filterSucursal" name="sucursal">
                                    <option value="">Todas</option>
                                    <option value="1">Sucursal 1</option>
                                    <option value="2">Sucursal 2</option>
                                    <option value="3">Sucursal 3</option>
                                    <!-- Agrega más opciones según tus sucursales -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="filterTexto" class="form-label mb-1">Texto</label>
                                <input type="text" class="form-control" id="filterTexto" name="texto" placeholder="Buscar...">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">Aplicar</button>
                                <button type="reset" class="btn btn-secondary btn-sm">Limpiar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Users Table -->
            <div class="table-responsive">
                <table id="dataTable" class="table table-usuarios table-hover">
                    <thead>
                        <tr>
                           <th>Acciones</th>
                            
                            <th>Área</th>
                            <th>Tipo</th>
                            <th>Lun</th>
                            <th>Mar</th>
                            <th>Mie</th>
                            <th>Jue</th>
                            <th>Vie</th>
                            <th>Sab</th>
                            <th>Total</th>
                            <th>Tareas</th>
                           
                        </tr>
                        
                    </thead>
                    <tbody>
                       <!-- <tr>
                            <td>1001</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://randomuser.me/api/portraits/men/32.jpg" class="rounded-circle me-2" width="32" height="32">
                                    <span>Carlos Mendoza</span>
                                </div>
                            </td>
                            <td>carlos.m@restaurante.com</td>
                            <td><span class="badge badge-role badge-admin">Administrador</span></td>
                            <td>Gerencia</td>
                            <td>15/03/2020</td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td>
                                <button class="action-btn btn-edit me-1" data-bs-toggle="modal" data-bs-target="#modalWindow">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="action-btn btn-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr> -->
                        <!-- More rows... -->
                    </tbody>
                </table>
            </div>
        </div>

    <?php include_once("../footer.php"); ?>

    <!-- User Modal (Add/Edit) -->
    <div class="modal fade modal-user" id="modalWindow" tabindex="-1" aria-labelledby="modalWindowLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalWindowLabel">Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                   
                        <!-- Avatar Section -->
                        <div class="row mb-1">
                             
                            <div class="col d-flex align-items-center">
                                <div class="w-100">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control dta" id="nombre" >
                                        <label for="username">Nombre del Área</label>
                                    </div>

 
                             
                            <div class="col-6 offset-3">
                                <div class="form-floating">
                                    <select class="form-select dta" id="tipo_id">
                                        <option value="1">Aula</option>
                                        <option value="2">Laboratorio</option>
                                        <option value="3">Taller</option>
                                        <option value="4">Biblioteca</option>
                                        <option value="5">Oficina</option>
                                        <option value="6">Baños</option>
                                        <option value="7">Area Verde</option>
                                        <option value="8">Pasillo</option>
                                        <option value="9">Bodega</option>
                                        <option value="10">Otro</option>
                                    </select>
                                    <label for="tipo_id">Tipo</label>
                                </div>
                            </div>


                                </div>
                            </div>
                            
                        </div>
                        <button class="btn btn-warning mb-2" type="button" id="btAddTarea">
                            <i class="fas fa-plus me-1"></i> Agregar Tarea
                        </button>
                        <div class="row mb-3">
                            <div class="col-12" id="dTodo">
                                <table class="table table-bordered table-striped" id="tareasTable">
                                    <thead class ="sticky-top bg-success text-white">
                                        <tr class="textCenter">
                                            <th><i class="fas fa-trash"></i></th>
                                            <th><i class="fas fa-camera"></i></th>
                                            <th>Tarea</th>
                                            <th>D</th>
                                            <th>L</th>
                                            <th>M</th>
                                            <th>M</th>
                                            <th>J</th>
                                            <th>V</th>
                                            <th>S</th>
                                            <th>Hora</th>
                                            <th><i class="bi bi-exclamation-circle-fill text-warning" title="Prioridad"></i></th>
                                        </tr>
                                        
                                    </thead>
                                    <tbody>
 
                                </tbody>
                                </table>
                             
                        </div>
                        
                        
                          </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btRegistrar">Registrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery, Bootstrap JS, DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>

    
    <script src="this.js?v=1.45"></script>
       
</body>
</html>