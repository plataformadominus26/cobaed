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
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css?v=1.06">

    <!-- Custom CSS -->
 
</head>
<body>
    <?php include_once("../menu.php"); ?>

        <!-- User Management Card -->
        <div class="card card-usuarios">
            <!-- Action Header -->
            <div class="header-actions d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0">Departamentos</h5>
                
                <div class="d-flex flex-wrap gap-2">
                    <!-- Add Button -->
                    <button class="btn btn-add btn-action text-white" id="btAgregar">
                        <i class="fas fa-plus me-1"></i> Nuevo Usuario
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
                        <ul class="dropdown-menu">
                            <li><h6 class="dropdown-header">Filtrar por:</h6></li>
                            <li><a class="dropdown-item" href="#">Todos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Administradores</a></li>
                            <li><a class="dropdown-item" href="#">Chefs</a></li>
                            <li><a class="dropdown-item" href="#">Meseros</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Users Table -->
            <div class="table-responsive">
                <table id="dataTable" class="table table-usuarios table-hover">
                    <thead>
                        <tr>
                            <th>Acciones</th>
                            <th>Nombre</th>
                            <th>Empleados</th>
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
                    <form id="userForm">
                        <!-- Avatar Section -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="avatar-upload">
                                    <div class="avatar-preview" style="background-image: url(https://randomuser.me/api/portraits/men/32.jpg);">
                                    </div>
                                    <div class="avatar-edit">
                                        <input type="file" id="imageUpload" accept=".png, .jpg, .jpeg">
                                        <label for="imageUpload"><i class="fas fa-pencil-alt"></i></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9 d-flex align-items-center">
                                <div class="w-100">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control dta" id="nombre" placeholder="Nombre de el empleado">
                                        <label for="username">Nombre</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        
                        <!-- Contact Info -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control dta" id="email" placeholder="Correo electrónico">
                                    <label for="email">Correo electrónico</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control dta" id="celular" placeholder="Teléfono">
                                    <label for="phone">Teléfono</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Role and Department -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select dta" id="puesto_id">
                                      
                                    </select>
                                    <label for="role">Puesto</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select dta" id="dpto_id">
                                        
                                    </select>
                                    <label for="department">Departamento</label>
                                </div>
                            </div>
                             <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select dta" id="horario_id">
                                         
                                    </select>
                                    <label for="department">Horario</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status and Dates -->
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select dta" id="activo">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                        <option value="2">Vacaciones</option>
                                    </select>
                                    <label for="status">Estado</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="date" class="form-control dta" id="ingreso">
                                    <label for="hireDate">Fecha de Ingreso</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="password" class="form-control dta" id="pwd" placeholder="Contraseña" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false">
                                    <label for="password">Contraseña</label>
                                </div>
                            </div>
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
    
    <script src="this.js?v=1.13"></script>
       
</body>
</html>