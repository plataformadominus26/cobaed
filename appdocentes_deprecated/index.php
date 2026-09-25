<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Kobai Docente: Studio</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="this.css">
</head>
<body>

    <div class="app-bar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
                 <img src="https://cobaedlomas.com/cobaed/assets/img/kobai/Kobai%20historia.png " alt="Kobai" width="100" height="100">
             <span class="fs-5 fw-bold">COBAED Docentes</span>
        </div>
        <div>
            <i class="bi bi-bell text-white"></i>
            <div class="d-inline-block rounded-circle bg-light ms-2" style="width:30px; height:30px;"></div>
        </div>  
    </div>

    
    <div class="container mt-4">
        
        <div class="card card-cobaed">
            <div class="card-header-gold">
                <i class="bi bi-lightbulb me-2"></i> Crear Nuevo Conocimiento
            </div>
            <div class="card-body pt-3 pb-2">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="form-floating">
                            <select class="form-select form-select-sm border-0 bg-light fw-bold text-secondary" id="academia" >
                                <option>Matemáticas</option>
                                <option>Fisica</option>
                            </select>
                            <label for="floatingSubject">Academia</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating">
                            <select class="form-select form-select-sm border-0 bg-light fw-bold text-secondary" id="rama">
                                <option>Algebra</option>
                                <option>Calculo Integral</option>
                            </select>
                            <label for="floatingSubject">Rama</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating">
                            <select class="form-select form-select-sm border-0 bg-light fw-bold text-secondary" id="tema">
                                <option>+ Nuevo Tema</option>
                                <option>Ecuacion cuadratica</option>
                            </select>
                            <label for="floatingTopic">Tema</label>
                        </div>
                    </div>
                </div>               
            </div>
        </div>

        <div class="card card-cobaed" id="card-input">
            <div class="card-body">
                <ul class="nav nav-tabs" id="regular-tab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="text-tab" data-bs-toggle="tab" data-bs-target="#text"><i class="bi bi-keyboard me-2"></i>Texto</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="voice-tab" data-bs-toggle="tab" data-bs-target="#voice"><i class="bi bi-mic me-2"></i>Configuración</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="cam-tab" data-bs-toggle="tab" data-bs-target="#cam"><i class="bi bi-upload me-2"></i>anexos</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="text">
                        <textarea class="form-control bg-light border-0 p-3" id="raw-text-input" rows="5" placeholder="Escribe aquí apuntes rápidos, ideas sueltas o pega texto de un PDF..."></textarea>
                    </div>
                    <div class="tab-pane fade text-center py-3" id="voice">

                    <form class="w-100 px-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo de Contenido</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includeExercises" checked>
                                <label class="form-check-label" for="includeExercises">
                                    Incluir ejercicios
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includePractice">
                                <label class="form-check-label" for="includePractice">
                                    Incluir ejercicios prácticos
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="focusArea" class="form-label fw-bold">Enfocarse en</label>
                            <select class="form-select form-select-sm bg-light" id="focusArea">
                                <option>Teoría</option>
                                <option>Aplicaciones</option>
                                <option>Problemas</option>
                                <option>Equilibrado</option>
                            </select>
                        </div>
                    </form>
                        <!--
                        <button class="btn btn-record d-flex align-items-center justify-content-center mx-auto mb-3" id="btn-mic">
                            <i class="bi bi-mic"></i>
                        </button>
                        <span class="text-muted small" id="voice-label">Toca para dictar a Kobai</span>
-->
                    </div>
                    <div class="tab-pane fade text-center py-3" id="cam">
                         <button class="btn btn-outline-success w-100 py-4 border-dashed">
                            <i class="bi bi-upload fa-2x mb-2"></i><br>Subir PDF/Imagen
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0 pb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <button class="btn btn-primary w-50 py-2 fw-bold shadow-sm" id="btn-process" ">
                        <i class="bi bi-gear me-2"></i> MEJORAR
                    </button>
                    <span class="p-2"></span>
                    <button class="btn btn-warning w-50 py-2 fw-bold" id="btSugerir">
                        <i class="bi bi-lightbulb me-2"></i> SUGERIR
                    </button>
                </div>
            </div>
        </div>

        <div class="card card-cobaed d-none" id="card-result">
            <div class="card-header-green d-flex justify-content-between align-items-center">
                <span><i class="bi bi-check me-2"></i>Contenido Validado</span>
                <button class="btn btn-sm btn-outline-light border-0" id="btn-close-result"><i class="bi bi-x"></i></button>
            </div>
            <div class="card-body">
                <input type="text" class="form-control fw-bold mb-3 border-0 border-bottom" value="Definición: Teorema de Pitágoras">
                <div id="editor-canvas" contenteditable="true" class="small">
                </div>
                <div class="mt-3">
                    <span class="badge bg-warning text-dark">Geometría</span>
                    <span class="badge bg-light text-dark border">Básico</span>
                </div>
            </div>
            <div class="card-footer bg-white border-0">
                <button class="btn btn-primary w-100 fw-bold" style="background-color: var(--cobaed-green); border:none;">
                    PUBLICAR EN ACADEMIA
                </button>
            </div>
        </div>

    </div>

    <nav class="bottom-nav">
        <a href="#" class="nav-item-cobaed">
            <i class="bi bi-house"></i> Inicio
        </a>
        <a href="#" class="nav-item-cobaed">
            <i class="bi bi-people"></i> Grupos
        </a>
        <div style="width: 20%;"></div> 
        
        <a href="#" class="nav-item-cobaed active">
            <i class="bi bi-book"></i> Biblioteca
        </a>
        <a href="#" class="nav-item-cobaed">
            <i class="bi bi-list"></i> Más
        </a>
    </nav>

    <a href="#" class="fab-main">
        <i class="bi bi-plus"></i>
    </a>

    <div id="ai-loader">
        <img src="https://via.placeholder.com/100?text=Kobai" class="rounded-circle mb-3 shadow" width="80" height="80"> <div class="spinner-border text-success" role="status"></div>
        <h6 class="mt-3 fw-bold text-success">Kobai está estructurando tu clase...</h6>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../api/apiCaller.js?v=1.03"></script>
    <script src="this.js?x=1"></script>
</body>
</html>