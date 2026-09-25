
<?php
   include_once("../rbh/conexion1.php");
?>
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
    <link rel="stylesheet" href="../assets/css/main.css?v=1.19">
    <link rel="stylesheet" href="this.css?v=1.01">

    <!-- Custom CSS -->
     
 
</head>
<body>
    <?php include_once("../menu.php"); ?>

        <!-- User Management Card -->
        <div class="card card-usuarios">
            <!-- Action Header -->
            <div class="header-actions d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0" id="dHd" style="float:left;">Listado de Alumnos</h5>
                <div class="d-flex align-items-center flex-nowrap gap-2" style="float:right;">
                    <!-- Add Button -->
                    <button class="btn btn-add btn-action text-white me-3" id="btAgregar" style="white-space: nowrap;">
                        <i class="fas fa-plus me-1"></i> Nuevo Alumno
                    </button>
                    <div class="input-group" style="max-width: 250px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Buscar maestro...">
                    </div>
                    <div id="alumniToolbar" class="d-flex align-items-center gap-2 ms-2">
                        <button class="btn btn-outline-primary" id="btFiltroAvanzado">Filtro</button>
                        <button class="btn btn-outline-success" id="btMensajesBatch">Mensajes</button>
                        <span id="filtroBadge" class="badge bg-secondary">Sin filtro</span>
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
                            <th>Semestre</th>
                            <th>Grupo</th>
                            <th>Telefono</th>
                            <th>Sexo</th>
                            
                            <th>Estado</th>
                            <th>En Campus</th>
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
<div class="modal fade" id="modalWindow" tabindex="-1" aria-labelledby="modalWindowLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered"> <div class="modal-content">
            
            <div class="modal-header text-white" style="background-color: var(--cobaed-green, #005931);">
                <h5 class="modal-title" id="modalWindowLabel">
                    <i class="fas fa-user-graduate me-2"></i>Gestión de Alumno
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0"> <form id="userForm">
                    <div class="row g-0 h-100">
                        
                        <div class="col-md-3">
                            <div class="modal-user-sidebar h-100">
                                <div class="avatar-wrapper">
                                    <div class="avatar-preview" style="background-image: url('https://randomuser.me/api/portraits/men/32.jpg');"></div>
                                    <div class="avatar-edit">
                                        <input type="file" id="imageUpload" accept=".png, .jpg, .jpeg" hidden>
                                        <label for="imageUpload" class="avatar-edit-btn">
                                            <i class="fas fa-camera"></i>
                                        </label>
                                    </div>
                                </div>
                                <h5 class="text-center fw-bold mt-2 mb-0" id="previewNombre">Nombre Alumno</h5>
                                <p class="text-muted small">En Campus : SI</p>
                                
                                <div class="mt-3">
                                    <span class="badge bg-success rounded-pill px-3 py-2">Activo</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <div class="p-4">
                                <ul class="nav nav-tabs mb-4" id="alumnoTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                                                            <button class="nav-link active" id="datos-tab" data-bs-toggle="tab" data-bs-target="#datos" type="button" role="tab" aria-selected="true">
                                                                                <i class="bi bi-person-vcard me-2"></i>Datos Personales
                                                                            </button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link" id="historial-tab" data-bs-toggle="tab" data-bs-target="#historial" type="button" role="tab" aria-selected="false">
                                                                                <i class="bi bi-clock-history me-2"></i>Mas Datos
                                                                            </button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link" id="redes-tab" data-bs-toggle="tab" data-bs-target="#redes" type="button" role="tab" aria-selected="false">
                                                                                <i class="bi bi-share me-2"></i>Redes
                                                                            </button>
                                                                        </li>
                                </ul>

                                <div class="tab-content" id="alumnoTabContent">
                                    
                                    <div class="tab-pane fade show active" id="datos" role="tabpanel" aria-labelledby="datos-tab">
                                        
                                        <h6 class="text-muted text-uppercase small fw-bold mb-3">Identidad</h6>
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control dta" id="nombre" placeholder="Nombre">
                                                    <label>Nombre(s)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control dta" id="paterno" placeholder="Apellido Paterno">
                                                    <label>Apellido Paterno</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control dta" id="materno" placeholder="Apellido Materno">
                                                    <label>Apellido Materno</label>
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="text-muted text-uppercase small fw-bold mb-3 border-top pt-3">Contacto y Ubicación</h6>
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="email" class="form-control dta" id="email" placeholder="Email">
                                                    <label>Correo Electrónico</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="tel" class="form-control dta" id="telefono" placeholder="Teléfono">
                                                    <label>Teléfono</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control dta" id="direccion" placeholder="Dirección">
                                                    <label>Dirección Completa</label>
                                                </div>
                                            </div>
                                        </div>

                                        <h6 class="text-muted text-uppercase small fw-bold mb-3 border-top pt-3">Información Académica</h6>
                                        <div class="row g-3">
                                                <div class="col-8">
                                                    <div class="form-floating">
                                                        <select class="form-select dta" id="plantel_id">
                                                            <option value="">Seleccionar Plantel</option>
                                                             <?php
                                                              $sql="select * from planteles order by nombre";
                                                              $result = $db->query($sql);
                                                              while($row = $result->fetch_assoc()) {
                                                                  echo "<option value='".$row['plantel_id']."'>".$row['nombre']."</option>";
                                                              }
                                                             ?>
                                                        </select>
                                                        <label>Plantel</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                <div class="form-floating">
                                                    <select class="form-select dta" id="semestre">
                                                        <option value="1">1</option>
                                                        <option value="3" selected>3</option>
                                                        </select>
                                                    <label>Semestre</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control dta" id="grupo" placeholder="Grupo">
                                                    <label>Grupo</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <select class="form-select dta" id="turno_id">
                                                        <option value="1">Matutino</option>
                                                        <option value="2">Vespertino</option>
                                                        <option value="3">Nocturno</option>
                                                    </select>
                                                    <label>Turno</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <select class="form-select dta" id="estado">
                                                        <option value="1">Activo</option>
                                                        <option value="0">Baja</option>
                                                    </select>
                                                    <label>Estado</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select dta" id="sexo">
                                                        <option value="1">Masculino</option>
                                                        <option value="0">Femenino</option>
                                                    </select>
                                                    <label>Sexo</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control dta" id="matricula" placeholder="Matricula">
                                                    <label>Matrícula</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                         <div class="tab-pane fade" id="historial" role="tabpanel" aria-labelledby="historial-tab">
    <div class="p-3">
        <h6 class="mb-3 text-danger"><i class="fas fa-heartbeat me-2"></i>Información Médica</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-floating">
                    <select class="form-select dta" id="sangre_id">
                        <option value="-1">Seleccionar tipo de sangre</option>
                        <?php
                        $sql = "SELECT * FROM tipos_sangre order by orden";
                        $result = $db->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['sangre_id'] . "'>" . $row['nombre'] . "</option>";
                        }
                        ?>
                        </select>
                    <label>Tipo de Sangre</label>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-floating">
                    <input type="text" class="form-control dta" id="seguro" placeholder="NSS / IMSS">
                    <label>Seguro (NSS / IMSS)</label>
                </div>
            </div>
            <div class="col-12">
                <div class="form-floating">
                    <input type="text" class="form-control dta" id="alergias" placeholder="Alergias">
                    <label>Alergias (Penicilina, polen, etc.)</label>
                </div>
            </div>
            <div class="col-12">
                <div class="form-floating">
                    <input type="text" class="form-control dta" id="padecimientos" placeholder="Padecimientos">
                    <label>Padecimientos Crónicos</label>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="mb-3 text-primary"><i class="fas fa-users me-2"></i>Contactos de Emergencia</h6>

             

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control dta" id="contacto1" placeholder="Nombre Contacto 1">
                    <label>Nombre Contacto 1</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-floating">
                    <select class="form-select dta" id="parentesco1">
                        <option value="-1">Parentesco...</option>
                        <?php    
                            $sql = "SELECT * FROM cat_parentescos order by nombre";
                        $result = $db->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['parentesco_id'] . "'>" . $row['nombre'] . "</option>";
                        }
                        ?>  
                        </select>
                    <label>Parentesco</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-floating">
                    <input type="tel" class="form-control dta" id="tel1" placeholder="Teléfono">
                    <label>Teléfono 1</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control dta" id="contacto2" placeholder="Nombre Contacto 2">
                    <label>Nombre Contacto 2</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-floating">
                    <select class="form-select dta" id="parentesco2">
                         <option value="-1">Parentesco...</option>
                        <?php    
                            $sql = "SELECT * FROM cat_parentescos order by nombre";
                        $result = $db->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['parentesco_id'] . "'>" . $row['nombre'] . "</option>";
                        }
                        ?>  
                        </select>
                    <label>Parentesco</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-floating">
                    <input type="tel" class="form-control dta" id="tel2" placeholder="Teléfono">
                    <label>Teléfono 2</label>
                </div>
            </div>
        </div>
    </div>
</div>

                                    <div class="tab-pane fade" id="redes" role="tabpanel" aria-labelledby="redes-tab">
                                        <h6 class="text-muted mb-3">Vínculos Sociales</h6>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white"><i class="fab fa-facebook text-primary"></i></span>
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="fb_link" placeholder="Facebook">
                                                        <label>Perfil de Facebook</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white"><i class="fab fa-instagram text-danger"></i></span>
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" id="ig_link" placeholder="Instagram">
                                                        <label>Usuario Instagram</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div> </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary px-4" id="btRegistrar" style="background-color: var(--cobaed-green, #005931); border:none;">
                    <i class="fas fa-save me-2"></i>Registrar Alumno
                </button>
            </div>
        </div>
    </div>
</div>
    <!-- QR Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px; overflow:hidden;">
                <div class="modal-header" style="background: linear-gradient(90deg, #007bff 0%, #00c6ff 100%); color: #fff;">
                    <h5 class="modal-title" id="qrModalLabel">
                        <i class="fas fa-qrcode me-2"></i> Identificación QR
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center" style="background: #f8f9fa;">
                    <div style="margin-bottom: 15px;">
                        <img src="../assets/img/qr-banner.png" alt="QR Banner" style="width:100%;max-width:320px;border-radius:12px;" id="qrImage">
                    </div>
                    <div id="studentQrContainer" style="margin-bottom: 15px;">
                        <!-- QR code will be injected here -->
                    </div>
                    <div class="fw-semibold" style="font-size:1.1rem;">
                        Escanea este QR con tu celular para identificarte
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <script>
    function showStudentQr(qrData) {
        var qr = new QRious({
            element: document.createElement('canvas'),
            value: qrData,
            size: 180,
            background: '#fff',
            foreground: '#007bff'
        });
        var container = document.getElementById('studentQrContainer');
        container.innerHTML = '';
        container.appendChild(qr.element);
        var qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
        qrModal.show();
    }
    // Example usage: showStudentQr('AlumnoID-12345');
    </script>

    <!-- jQuery, Bootstrap JS, DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script src="this.js?v=1.34"></script>
       
</body>
</html>