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
    <link rel="stylesheet" href="../assets/css/main.css?v=1.18">

    <!-- Custom CSS -->
     
 
</head>
<body>
    <?php include_once("../menu.php"); ?>

        <!-- User Management Card -->
        <div class="card ">
            <!-- Action Header -->
      
            
            <!-- Users Table -->
             <div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <h5 class="text-success fw-bold"><i class="fas fa-plus-circle me-2"></i>Registro de Conocimiento</h5>
    </div>
    
    <div class="card-body">
        <form id="knowledgeForm">
            
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">Academia</label>
                    <select class="form-select" id="academia_id" required>
                        <option selected disabled>Seleccione Academia...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">Materia</label>
                    <select class="form-select" id="materia_id" required>
                        <option selected disabled>Seleccione Materia...</option>
                        </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">Unidad</label>
                    <select class="form-select" id="unidad_id" required>
                        <option selected disabled>Seleccione Unidad...</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small text-muted">Temas Asociados</label>
                <div class="input-group">
                     <select class="form-select" id="temas_id" required>
                        <option selected disabled>Seleccione Tema...</option>
                    </select>
                    <button class="btn btn-success text-white" type="button" id="btnAgregaTema">
                        <i class="fas fa-plus"></i> Agregar Tema
                    </button>
                </div>
                 
            </div>

            <div class="card border mb-4">
                <img src="../assets/img/kobai/kobai_ai.png" alt="Kobai AI" style="position: absolute; top: -15px; right: 10px; width: 140px; height: auto; z-index: 10;">
                <button class="btn btn-warning btn-sm" style="position: absolute; top: 25px; right: 140px; z-index: 11;" title="Ayuda" id="btAiDo">
                    <i class="bi bi-stars"></i> Hazlo con AI
                </button>
               
                <div class="card-header bg-light p-0">
                    <ul class="nav nav-tabs card-header-tabs m-0" id="contentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold text-success" id="texto-tab" data-bs-toggle="tab" data-bs-target="#tab-texto" type="button" role="tab">
                                <i class="fas fa-paragraph me-2"></i>Texto / Teoría
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-secondary" id="ejercicios-tab" data-bs-toggle="tab" data-bs-target="#tab-ejercicios" type="button" role="tab">
                                <i class="fas fa-calculator me-2"></i>Ejercicios
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-secondary" id="video-tab" data-bs-toggle="tab" data-bs-target="#tab-video" type="button" role="tab">
                                <i class="fas fa-video me-2"></i>Videos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-secondary" id="archivos-tab" data-bs-toggle="tab" data-bs-target="#tab-archivos" type="button" role="tab">
                                <i class="fas fa-paperclip me-2"></i>Adjuntos
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="contentTabsContent">
                        
                        <div class="tab-pane fade show active" id="tab-texto" role="tabpanel">
                            <div class="mb-3">
                                <label class="small text-muted mb-2">Escribe el contenido teórico. Este texto será vectorizado para Kobai.</label>
                                <textarea class="form-control" name="contenido_texto" rows="12" placeholder="Desarrolla el tema aquí..."></textarea>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-ejercicios" role="tabpanel">
                            <div class="alert alert-info py-2 small">
                                <i class="fas fa-info-circle"></i> Agrega problemas para que Kobai practique con el alumno.
                            </div>
                            <div id="exercises-container">
                                <div class="card bg-light border-0 mb-2 p-3">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <textarea class="form-control mb-2" rows="3" placeholder="Pregunta / Planteamiento del problema"></textarea>
                                            <textarea class="form-control form-control-sm" rows="5" placeholder="Respuesta correcta (para validación interna)"></textarea>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <button type="button" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success mt-2">
                                <i class="fas fa-plus"></i> Agregar Otro Ejercicio
                            </button>
                        </div>

                        <div class="tab-pane fade" id="tab-video" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Enlace de YouTube / Vimeo</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-youtube"></i></span>
                                        <input type="url" class="form-control" name="video_url" placeholder="https://youtube.com/...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-archivos" role="tabpanel">
                            <div class="border-2 border-dashed border-secondary rounded p-5 text-center bg-light">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5>Arrastra archivos aquí o haz clic para subir</h5>
                                <p class="text-muted small">Soporta PDF, JPG, PNG (Max 5MB)</p>
                                <input type="file" class="form-control d-none" id="fileUpload">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('fileUpload').click()">Seleccionar Archivos</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="form-check form-switch pt-4">
                        <input class="form-check-input" type="checkbox" id="checkEjercicios">
                        <label class="form-check-label" for="checkEjercicios">¿Requiere validación matemática?</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">Horas Estimadas</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="far fa-clock"></i></span>
                        <input type="number" class="form-control" name="horas" placeholder="Ej: 2">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">Referencia Bibliográfica</label>
                    <input type="text" class="form-control" name="referencia" placeholder="Ej: Baldor, A. (2008). Álgebra.">
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="card p-3 border cursor-pointer h-100 radio-card selected-card">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estatus" id="statusBorrador" value="draft" checked>
                            <label class="form-check-label w-100" for="statusBorrador">
                                <span class="d-block fw-bold text-dark">Borrador</span>
                                <span class="small text-muted">Guardar para continuar editando luego. No visible para alumnos.</span>
                            </label>
                        </div>
                    </label>
                </div>
                <div class="col-md-6">
                    <label class="card p-3 border cursor-pointer h-100 radio-card">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="estatus" id="statusPublicado" value="published">
                            <label class="form-check-label w-100" for="statusPublicado">
                                <span class="d-block fw-bold text-success">Aprobado por Academia</span>
                                <span class="small text-muted">Listo para ser publicado a alumnos y procesado por Kobai.</span>
                            </label>
                        </div>
                    </label>
                </div>
            </div>

            <div class="d-flex justify-content-end border-top pt-3">
                <button type="button" class="btn btn-light text-muted me-2 border">Cancelar</button>
                <button type="submit" class="btn btn-success fw-bold px-4">
                    <i class="fas fa-save me-2"></i> Guardar Conocimiento
                </button>
            </div>

        </form>
    </div>
</div>

<style>
    /* Pequeños ajustes CSS para dar feedback visual en la selección de Estatus */
    .radio-card { transition: all 0.2s; }
    .radio-card:hover { background-color: #f8f9fa; }
    .input-group-text { background-color: #f8f9fa; }
    
    /* Cuando el radio está seleccionado, pintar el borde verde */
    input[type="radio"]:checked + label { color: #198754; }
    
    /* Ajuste para el estilo dashed del upload */
    .border-dashed { border-style: dashed !important; }
</style>
        </div>

    <?php include_once("../footer.php"); ?>

    <!-- User Modal (Add/Edit) -->
    <div class="modal fade modal-user" id="modalWindow" tabindex="-1" aria-labelledby="modalWindowLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalWindowLabel">Nuevo Alumno</h5>
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
                                    <input type="tel" class="form-control dta" id="telefono" placeholder="Teléfono">
                                    <label for="phone">Teléfono</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control dta" id="direccion" placeholder="Dirección">
                                    <label for="direccion">Dirección</label>
                                </div>
                            </div>
                        </div>
                        
                        
                        <!-- Status and Dates -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select dta" id="estado">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                        <option value="2">Vacaciones</option>
                                    </select>
                                    <label for="estado">Estado</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control dta" id="semestre" min="1" max="12" placeholder="Semestre">
                                    <label for="semestre">Semestre</label>
                                </div>
                            </div>
                             
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control dta" id="grupo" placeholder="Grupo">
                                    <label for="grupo">Grupo</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select dta" id="sexo">
                                        <option value="1">Masculino</option>
                                        <option value="0">Femenino</option>
                                         
                                    </select>
                                    <label for="sexo">Sexo</label>
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
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script src="../api/apiCaller.js?v=1.12"></script>
    
    <script src="this.js?v=1.29"></script>
       
</body>
</html>