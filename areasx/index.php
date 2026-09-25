<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/favico.png"> 
    <title><?php require_once __DIR__ . '/../config.php'; echo plantel_nombre(); ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css?v=1.17">

    <!-- Custom CSS -->
 
</head>
<body>
    <?php include_once("../menu.php"); ?>

        <!-- User Management Card -->
        <div class="card card-usuarios">
            <!-- Action Header -->
            <div class="header-actions d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0" id="dHd" style="float:left;">Areas</h5>
                <div class="d-flex align-items-center flex-nowrap gap-2" style="float:right;">
                    <!-- Add Button -->
                    <button class="btn btn-add btn-action text-white me-3" id="btAgregar" style="white-space: nowrap;">
                        <i class="fas fa-plus me-1"></i> Nueva Area
                    </button>
                    <div class="input-group" style="max-width: 250px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Buscar maestro...">
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
                            <th>Tipo</th>
                            <th>Lun</th>
                            <th>Mar</th>
                            <th>Mie</th>
                            <th>Jue</th>
                            <th>Vie</th>
                            <th>Sab</th>
                            <th>Total</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>

    <?php include_once("../footer.php"); ?>

    <!-- User Modal (Add/Edit) -->
    <div class="modal fade modal-users modal-sm" id="modalWindow" tabindex="-1" aria-labelledby="modalWindowLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalWindowLabel">Nueva Area</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        <!-- Avatar Section -->
                        <div class="row mb-4">
                             
                            <div class="col-12 d-flex align-items-center">
                                <div class="w-100">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control dta" id="nombre" placeholder="Nombre de el empleado">
                                        <label for="username">Nombre</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        
                        <!-- Contact Info -->
                         
                        
                        
                        <!-- Status and Dates -->
                        <div class="row g-3">
                             
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
    
    <script src="this.js?v=1.21"></script>
       
</body>
</html>