<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=no">
    <title>Cobaed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="../assets/css/leaflet.css" />

    <link rel="stylesheet" href="this.css?x=1.05">
 
     
</head>
<body>

    <header class="app-header bg-campestre text-white p-3 shadow-sm">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="d-flex align-items-center">
            <img src="https://via.placeholder.com/40" class="rounded-circle border border-2 border-white me-2" alt="Socio" id="imgPerfil">
            <div>
                <h6 class="mb-0 fw-bold" id="h6Nombre"></h6>
                <small class="text-gold"><i class="bi bi-star-fill small"></i><i id="iStatus">Socio Propietario</i></small>
            </div>
            </div>
            <button class="btn text-white ms-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuSocio">
            <i class="bi bi-grid-3x3-gap-fill fs-4"></i>
            </button>
        </div>
    </header>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="menuSocio">
        <div class="offcanvas-header bg-light">
            <h5 class="offcanvas-title fw-bold">Mi Cuenta</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="text-center mb-4">
                <p class="mb-1 text-muted small">Estatus actual:</p>
                <span class="badge bg-success status-badge text-uppercase">Activo</span>
                <p class="mt-2 small text-muted">Próximo pago: <span class="fw-bold text-dark">05 Ene 2026</span></p>
            </div>
            <hr class="border-gold" style="border-top: 2px solid #d4af37; opacity: 1;">
            <ul class="list-group list-group-flush mt-3">
                <a href="#" class="list-group-item list-group-item-action border-0 py-3"><i class="bi bi-megaphone me-3 text-campestre"></i>Noticias</a>
                <a href="#" class="list-group-item list-group-item-action border-0 py-3" id="btAreas"><i class="bi bi-map me-3 text-campestre"></i>Áreas del Club</a>
                <a href="#" class="list-group-item list-group-item-action border-0 py-3 text-danger" id="btSalir"><i class="bi bi-box-arrow-left me-3"></i>Salir</a>
            </ul>
        </div>
    </div>
 <main class="container-fluid p-0" id="mainApp">
    <!-- Hidden tab triggers for programmatic navigation -->
   <ul class="nav nav-tabs d-none" id="mainAppTabs" role="tablist">
  <li class="nav-item">
    <button class="nav-link active" id="trigger-inicio" data-bs-toggle="tab" data-bs-target="#tab-inicio" type="button"></button>
  </li>
  <li class="nav-item">
    <button class="nav-link" id="trigger-pases" data-bs-toggle="tab" data-bs-target="#tab-pases" type="button"></button>
  </li>
  <li class="nav-item">
    <button class="nav-link" id="trigger-reservas" data-bs-toggle="tab" data-bs-target="#tab-reservas" type="button"></button>
  </li>
  <li class="nav-item">
    <button class="nav-link" id="trigger-mas" data-bs-toggle="tab" data-bs-target="#tab-mas" type="button"></button>
  </li>
<li class="nav-item">
    <button class="nav-link" id="trigger-mapa" data-bs-toggle="tab" data-bs-target="#tab-mapa" type="button"></button>
</li>
</ul>
    
    <div class="tab-content" id="nav-tabContent">
        
       <div class="tab-pane fade show active" id="tab-inicio" role="tabpanel">
    <div class="bg-campestre text-white px-4 pb-5 pt-3" style="border-radius: 0 0 40px 40px;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-0">Bienvenido</h2>
                <p class="small opacity-75"><?php echo date('l, d \d\e F'); ?></p>
            </div>
             
        </div>

        

         
    </div>

    <div style="margin-top: 80px;" class="px-4 pb-5" id="feed-container">
       <div class="feed-scope">
    
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <h4 class="fw-bold mb-0 text-dark">Muro Escolar</h4>
        <button class="btn btn-outline-success btn-sm rounded-pill px-3" id="btVerTodas">Filtrar <i class="bi bi-filter"></i></button>
    </div>

    <div class="feed-card animate__animated animate__fadeInUp">
        <div class="feed-header">
            <div class="poster-info">
                <img src="https://randomuser.me/api/portraits/women/44.jpg" class="poster-avatar" alt="Avatar">
                <div>
                    <h6 class="poster-name">Dir. Académica</h6>
                    <p class="post-date">Hace 2 horas</p>
                </div>
            </div>
            <i class="bi bi-pin-angle-fill text-muted"></i>
        </div>

        <div class="feed-body">
            <p class="text-highlight large mb-0">
                ⚠️ Aviso Importante: <br>
                Debido a las condiciones climáticas, la entrada de mañana se recorre a las 09:00 AM.
            </p>
        </div>

        <div class="feed-footer">
            <div class="rating-box">
                <i class="bi bi-star-fill"></i> 4.9
            </div>
            <button class="action-btn text-primary">
                <i class="bi bi-chat-dots"></i> 12 Comentarios
            </button>
        </div>
    </div>

    <div class="feed-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
        <div class="feed-header">
            <div class="poster-info">
                <img src="https://randomuser.me/api/portraits/men/32.jpg" class="poster-avatar" alt="Avatar">
                <div>
                    <h6 class="poster-name">Edgar Silerio</h6>
                    <p class="post-date">Ayer, 04:30 PM</p>
                </div>
            </div>
            <span class="badge bg-success bg-opacity-10 text-success rounded-pill">PAEC</span>
        </div>

        <div class="feed-body">
            <h5 class="post-title">Colecta de Víveres 2026</h5>
            <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=800" class="media-cover" alt="Colecta">
            <p class="text-secondary small mb-2">
                Gracias a todos los alumnos de 3er y 5to semestre por su increíble apoyo en la recolección de despensas. ¡Superamos la meta!
            </p>
        </div>

        <div class="feed-footer">
            <div class="rating-box">
                <i class="bi bi-star-fill"></i> 5.0
            </div>
            <button class="action-btn">
                <i class="bi bi-chat"></i> Comentar
            </button>
        </div>
    </div>

    <div class="feed-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
        <div class="feed-header">
            <div class="poster-info">
                <img src="https://ui-avatars.com/api/?name=Servicios+Escolares&background=0D8ABC&color=fff" class="poster-avatar" alt="Avatar">
                <div>
                    <h6 class="poster-name">Serv. Escolares</h6>
                    <p class="post-date">01 Ene 2026</p>
                </div>
            </div>
            <i class="bi bi-paperclip text-muted"></i>
        </div>

        <div class="feed-body">
            <p class="mb-2 text-dark fw-bold">Calendario de Exámenes Parciales</p>
            <p class="small text-muted mb-2">Descarga el calendario oficial para el ciclo 2026-A.</p>
            
            <div class="doc-embed-card" onclick="alert('Abriendo PDF...')">
                <i class="bi bi-file-earmark-pdf-fill doc-icon"></i>
                <div class="doc-info">
                    <h6>Calendario_Oficial_2026.pdf</h6>
                    <span>PDF • 1.2 MB • Toca para ver</span>
                </div>
                <div class="ms-auto">
                    <i class="bi bi-download text-success fs-5"></i>
                </div>
            </div>
        </div>

        <div class="feed-footer">
            <div class="rating-box text-muted" style="font-weight: 400;">
                <i class="bi bi-eye"></i> 1.2k Vistas
            </div>
            <button class="action-btn">
                <i class="bi bi-chat"></i> 0 Comentarios
            </button>
        </div>
    </div>


    <div class="feed-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
        <div class="feed-header">
            <div class="poster-info">
                <img src="https://randomuser.me/api/portraits/men/85.jpg" class="poster-avatar" alt="Avatar">
                <div>
                    <h6 class="poster-name">Coach Deportes</h6>
                    <p class="post-date">Hace 3 horas</p>
                </div>
            </div>
            <i class="bi bi-collection text-muted"></i>
        </div>

        <div class="feed-body">
            <p class="mb-1 text-dark fw-bold">Entrenamiento Matutino de Fútbol ⚽</p>
            <span class="album-indicator"><i class="bi bi-images me-1"></i>Desliza para ver más</span>
            
            <div class="media-carousel">
                <div class="carousel-item-wrapper">
                    <img src="https://images.unsplash.com/photo-1529900748604-07564a03e7a6?q=80&w=600" alt="Entrenamiento">
                </div>
                <div class="carousel-item-wrapper">
                    <img src="https://images.unsplash.com/photo-1517649763962-0c623066013b?q=80&w=600" alt="Equipo">
                </div>
                <div class="carousel-item-wrapper">
                    <img src="https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?q=80&w=600" alt="Cancha">
                </div>
                <div class="carousel-item-wrapper">
                    <img src="https://images.unsplash.com/photo-1628891892236-f3a912d645a1?q=80&w=600" alt="Jugadores">
                </div>
            </div>
            
            <p class="small text-muted mb-2">
                Gran esfuerzo del equipo representativo hoy en la cancha principal. ¡Listos para el torneo estatal!
            </p>
        </div>

        <div class="feed-footer">
            <div class="rating-box">
                <i class="bi bi-star-fill"></i> 4.8
            </div>
            <button class="action-btn text-primary">
                <i class="bi bi-chat-dots"></i> 8 Comentarios
            </button>
        </div>
    </div>

    <div class="feed-card animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
        <div class="feed-header">
            <div class="poster-info">
                <img src="https://randomuser.me/api/portraits/women/65.jpg" class="poster-avatar" alt="Avatar">
                <div>
                    <h6 class="poster-name">Comité de Graduación</h6>
                    <p class="post-date">Ayer</p>
                </div>
            </div>
            <i class="bi bi-stars text-warning"></i>
        </div>

        <div class="feed-body">
            <h5 class="post-title">📸 Galería: Ceremonia de Graduación Generación 2023-2026</h5>
            <p class="text-secondary small mb-2">
                Momentos inolvidables de nuestra última ceremonia. ¡Felicidades a todos los graduados!
            </p>

            <div class="mosaic-grid">
                <div class="mosaic-item">
                    <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=400" alt="Graduación">
                </div>
                <div class="mosaic-item">
                    <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=400" alt="Togas">
                </div>
                <div class="mosaic-item">
                    <img src="https://images.unsplash.com/photo-1627556592933-ffe99c1cd9eb?q=80&w=400" alt="Diploma">
                </div>
                
                <div class="mosaic-item" onclick="alert('Abrir galería completa con las 15 fotos restantes')">
                    <img src="https://images.unsplash.com/photo-1525921429624-479b6a26d84d?q=80&w=400" alt="Grupo">
                    <div class="mosaic-overlay">+15</div>
                </div>
            </div>
        </div>

        <div class="feed-footer">
            <div class="rating-box">
                <i class="bi bi-star-fill"></i> 5.0
            </div>
            <button class="action-btn">
                <i class="bi bi-chat"></i> 45 Comentarios
            </button>
        </div>
    </div>

</div>  

 
        
       

    </div>
</div>

        <div class="tab-pane fade px-4 pt-4" id="tab-pases" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Mis Invitaciones</h4>
                <button class="btn bg-campestre text-white btn-sm rounded-pill px-3" id="btPase">+ Nuevo Pase</button>
            </div>
            
            <div class="card card-custom mb-3 border-0 shadow-sm overflow-hidden">
                <div class="d-flex">
                    <div class="bg-success" style="width: 6px;"></div>
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold mb-0">Roberto Gómez</h6>
                            <small class="text-muted">Vence: 30 Dic</small>
                            <div class="mt-1"><span class="badge bg-success-subtle text-success status-badge">VIGENTE</span></div>
                        </div>
                        <button class="btn btn-outline-success rounded-circle border-0 fs-4"><i class="bi bi-whatsapp"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade px-4 pt-4" id="tab-reservas" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Reservaciones</h4>
                <button class="btn btn-outline-dark btn-sm rounded-pill" data-bs-toggle="offcanvas" data-bs-target="#misReservas">
                    <i class="bi bi-list-check me-1"></i> Mis citas
                </button>
            </div>

            <div class="card card-custom mb-4 overflow-hidden border-0 shadow-sm">
                <img src="https://images.unsplash.com/photo-1587174486073-ae5e5cff23aa?q=80&w=600" class="card-img-top" style="height: 160px; object-fit: cover;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold mb-0">Tee de Salida Golf</h5>
                        <span class="badge bg-success status-badge">Hoy: 4</span>
                    </div>
                    <p class="text-muted small">Campo profesional de 18 hoyos. Disponible mañana: <b>12 slots</b></p>
                    <button class="btn bg-campestre text-white w-100 rounded-pill fw-bold btReserva">Reservar Ahora</button>
                </div>
            </div>

            <div class="card card-custom mb-4 overflow-hidden border-0 shadow-sm">
                <img src="assets/img/areas/padel.jpg" class="card-img-top" style="height: 160px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="fw-bold mb-1">Canchas de Pádel</h5>
                    <p class="text-muted small mb-3">4 canchas al aire libre. Disponible hoy: <b>2 slots</b></p>
                    <button class="btn bg-campestre text-white w-100 rounded-pill fw-bold btReserva">Reservar Ahora</button>
                </div>
            </div>
        </div>

        <div class="tab-pane fade px-4 pt-5 text-center" id="tab-mas" role="tabpanel">
            <i class="bi bi-tools text-muted display-1"></i>
            <h4 class="mt-4 fw-bold">Próximamente</h4>
            <p class="text-muted">La seccion de blog será liberada el dia 1 de Enero de 2026.</p>
        </div>


        <div class="tab-pane fade" id="tab-mapa" role="tabpanel">
    

    <div class="px-4" style="margin-top: 30px;">
        <div class="card border-0 shadow-lg overflow-hidden mb-4" style="border-radius: 20px;">
            <div id="mapContainer" style="height: 300px; width: 100%; background: #e9ecef;"></div>
            
            <div class="card-body p-3">
                <div class="form-floating mb-3">
                    <select class="form-select border-0 bg-light" id="comboAreas" onchange="centrarEnArea()">
                        <option value="0" selected>Selecciona un área...</option>
                        <option value="1">Hoyo 1 - Tee de Salida</option>
                        <option value="2">Alberca Techada</option>
                        <option value="3">Canchas de Pádel</option>
                        <option value="4">Casa Club / Restaurante</option>
                    </select>
                    <label for="comboAreas">¿A dónde quieres ir?</label>
                </div>

                <div id="infoArea" class="animate__animated animate__fadeIn">
                    <h6 class="fw-bold text-campestre mb-1" id="nombreAreaMapa">Bienvenido</h6>
                    <p class="small text-muted mb-0" id="descAreaMapa">Selecciona un punto en el mapa o en el menú para ver detalles.</p>
                </div>
            </div>
        </div>

        <h6 class="fw-bold mb-3"><i class="bi bi-images me-2 text-gold"></i>Galería del Área</h6>
        <div class="gallery-scroll mb-5">
            <img src="https://placehold.co/400x400/3aa76d/white?text=Foto+1" class="gallery-item shadow-sm">
            <img src="https://placehold.co/400x400/3aa76d/white?text=Foto+2" class="gallery-item shadow-sm">
            <img src="https://placehold.co/400x400/3aa76d/white?text=Foto+3" class="gallery-item shadow-sm">
            <img src="https://placehold.co/400x400/3aa76d/white?text=Foto+4" class="gallery-item shadow-sm">
        </div>
    </div>
</div>

    </div>
</main>

<div class="offcanvas offcanvas-bottom rounded-top-5" style="height: 70%;" tabindex="-1" id="misReservas">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold">Mis Reservas Activas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div class="alert alert-light border shadow-sm rounded-4">
            <div class="d-flex align-items-center">
                <div class="bg-campestre text-white p-3 rounded-3 me-3 text-center">
                    <small class="d-block">DIC</small><h5 class="mb-0 fw-bold">28</h5>
                </div>
                <div>
                    <h6 class="fw-bold mb-0">Tee de Salida Golf</h6>
                    <p class="small text-muted mb-0">Hora: 08:30 AM</p>
                </div>
            </div>
        </div>
    </div>
</div>
    <footer class="app-footer">
        <div class="row g-0 text-center">
            <div class="col">
                <a href="#" class="nav-link py-2 active" data-section="inicio">
                    <i class="bi bi-house-door"></i> Inicio
                </a>
            </div>
            <div class="col">
                <a href="#" class="nav-link py-2" data-section="pases">
                    <i class="bi bi-people-fill"></i> Alumnos
                </a>
            </div>
            <div class="col">
                <a href="#" class="nav-link py-2" data-section="reservas">
                    <i class="bi bi-book-fill"></i> PAEC
                </a>
            </div>
            <div class="col">
                <a href="#" class="nav-link py-2" data-section="mas">
                    <i class="bi bi-three-dots"></i> Más
                </a>
            </div>
        </div>
    </footer>

    <!-- modal de invitaciones -->
     <div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="drawerPases" aria-labelledby="drawerPasesLabel" style="width: 95%; max-width: 450px; border-radius: 30px 0 0 30px;">
    
    <div class="offcanvas-header bg-campestre text-white px-4 py-3">
        <h5 class="offcanvas-title fw-bold" id="drawerPasesLabel">
            <i class="bi bi-ticket-perforated-fill me-2"></i>Gestión de Invitados
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0 bg-light">
        <ul class="nav nav-pills d-none" id="pasesTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-registro-pase-btn" data-bs-toggle="pill" data-bs-target="#content-registro-pase" type="button" role="tab">Pase</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-nuevo-invitado-btn" data-bs-toggle="pill" data-bs-target="#content-nuevo-invitado" type="button" role="tab">Nuevo</button>
            </li>
        </ul>

        <div class="tab-content" id="pasesTabContent">
            
            <div class="tab-pane fade show active p-4" id="content-registro-pase" role="tabpanel">
                <h6 class="fw-bold mb-4 text-campestre">Información del Pase</h6>
                
                <div class="input-group mb-4">
                    <div class="form-floating flex-grow-1">
                        <input type="tel" class="form-control" id="paseTelInvitado" placeholder="Teléfono">
                        <label for="paseTelInvitado">Teléfono Invitado</label>
                    </div>
                    <button class="btn btn-outline-success px-3 border-start-0" type="button" onclick="cambiarATabInvitado()" title="Registrar nuevo invitado">
                        <i class="bi bi-person-plus-fill fs-5"></i>
                    </button>
                </div>

                <div class="form-floating mb-3">
                    <input type="date" class="form-control" id="paseFecha" value="<?php echo date('Y-m-d'); ?>">
                    <label for="paseFecha">Fecha de Visita</label>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="time" class="form-control" id="paseHoraDesde" value="08:00">
                            <label for="paseHoraDesde">Desde</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="time" class="form-control" id="paseHoraHasta" value="14:00">
                            <label for="paseHoraHasta">Hasta</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mb-4">
                    <textarea class="form-control" placeholder="Observaciones" id="paseObs" style="height: 100px"></textarea>
                    <label for="paseObs">Observaciones</label>
                </div>

                <div class="d-grid">
                    <button type="button" class="btn btn-campestre btn-lg rounded-pill py-3 fw-bold" onclick="guardarPaseFinal()">Generar Pase</button>
                </div>
            </div>

            <div class="tab-pane fade p-4" id="content-nuevo-invitado" role="tabpanel">
                <div class="d-flex align-items-center mb-4">
                    <button class="btn btn-sm btn-light rounded-circle me-3" onclick="regresarAPase()"><i class="bi bi-chevron-left"></i></button>
                    <h6 class="fw-bold mb-0 text-campestre">Datos del Nuevo Invitado</h6>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="nuevoNombre" placeholder="Nombre">
                    <label for="nuevoNombre">Nombre Completo</label>
                </div>

                <div class="form-floating mb-3">
                    <textarea class="form-control" placeholder="Domicilio" id="nuevoDomicilio" style="height: 80px"></textarea>
                    <label for="nuevoDomicilio">Domicilio</label>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="photo-card text-center p-3 border rounded-3 bg-white" onclick="capturarFoto('perfil')">
                            <i class="bi bi-person-badge text-campestre fs-2"></i>
                            <p class="small mb-0 fw-bold">Foto Perfil</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="photo-card text-center p-3 border rounded-3 bg-white" onclick="capturarFoto('ine')">
                            <i class="bi bi-card-heading text-campestre fs-2"></i>
                            <p class="small mb-0 fw-bold">Foto INE</p>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-campestre btn-lg rounded-pill py-3 fw-bold" onclick="registrarInvitadoLocal()">Registrar y Continuar</button>
                    <button type="button" class="btn btn-link text-muted" onclick="regresarAPase()">Cancelar</button>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="drawerReservas" style="width: 95%; max-width: 450px; border-radius: 30px 0 0 30px;">
    
    <div class="offcanvas-header bg-primary text-white px-4 py-3">
        <h5 class="offcanvas-title fw-bold">
            <i class="bi bi-calendar-check-fill me-2"></i>Nueva Reserva
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-0 bg-light">
        <ul class="nav nav-pills d-none" id="reservaTab" role="tablist">
            <li class="nav-item"><button class="nav-link active" id="tab-reserva-form-btn" data-bs-toggle="pill" data-bs-target="#content-reserva-form"></button></li>
            <li class="nav-item"><button class="nav-link" id="tab-reserva-confirm-btn" data-bs-toggle="pill" data-bs-target="#content-reserva-confirm"></button></li>
        </ul>

        <div class="tab-content" id="reservaTabContent">
            
            <div class="tab-pane fade show active p-4" id="content-reserva-form" role="tabpanel">
                <h6 class="fw-bold mb-3 text-primary">1. Selecciona el Área</h6>
                <div class="form-floating mb-4">
                    <select class="form-select" id="areaReserva">
                        <option value="1">Salón de Fiestas "Los Pinos"</option>
                        <option value="2">Sala de Juntas Ejecutiva</option>
                        <option value="3">Área Infantil (Kids Zone)</option>
                        <option value="4">Cancha de Tenis 1</option>
                    </select>
                    <label for="areaReserva">Espacio a reservar</label>
                </div>

                <h6 class="fw-bold mb-3 text-primary">2. Fecha y Disponibilidad</h6>
                <div class="calendar-mini mb-4 p-2 bg-white rounded-4 shadow-sm border">
                    <input type="date" class="form-control border-0 fw-bold fs-5" id="fechaReserva" value="<?php echo date('Y-m-d'); ?>" onchange="cargarSlots()">
                </div>

                <h6 class="fw-bold mb-3 text-primary">3. Horarios Disponibles</h6>
                <div id="slotsContainer" class="row g-2 mb-4">
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="slot" id="slot1" autocomplete="off">
                        <label class="btn btn-outline-primary w-100 rounded-3 py-2" for="slot1">09:00</label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="slot" id="slot2" autocomplete="off">
                        <label class="btn btn-outline-primary w-100 rounded-3 py-2" for="slot2">11:00</label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="slot" id="slot3" autocomplete="off">
                        <label class="btn btn-outline-primary w-100 rounded-3 py-2" for="slot3">13:00</label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="slot" id="slot4" autocomplete="off" disabled>
                        <label class="btn btn-outline-secondary w-100 rounded-3 py-2 opacity-50" for="slot4">Ocupado</label>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="button" class="btn btn-primary btn-lg rounded-pill py-3 fw-bold shadow" onclick="irAConfirmarReserva()">Continuar</button>
                </div>
            </div>

            <div class="tab-pane fade p-4" id="content-reserva-confirm" role="tabpanel">
                <div class="d-flex align-items-center mb-4">
                    <button class="btn btn-sm btn-light rounded-circle me-3" onclick="regresarAReserva()"><i class="bi bi-chevron-left"></i></button>
                    <h6 class="fw-bold mb-0 text-primary">Confirmar Detalles</h6>
                </div>

                <div class="card border-0 bg-white shadow-sm rounded-4 mb-4">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Área seleccionada:</p>
                        <h6 class="fw-bold" id="resumenArea">Salón de Fiestas</h6>
                        <hr>
                        <p class="small text-muted mb-1">Fecha y Hora:</p>
                        <h6 class="fw-bold" id="resumenTiempo">24 Oct - 09:00 AM</h6>
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="numPersonas" placeholder="Pax">
                    <label for="numPersonas">Número de personas</label>
                </div>

                <div class="form-floating mb-4">
                    <textarea class="form-control" id="notasReserva" style="height: 100px" placeholder="Notas"></textarea>
                    <label for="notasReserva">Notas o requerimientos</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary btn-lg rounded-pill py-3 fw-bold" onclick="finalizarReserva()">Confirmar Reserva</button>
                    <button type="button" class="btn btn-link text-muted" onclick="regresarAReserva()">Cambiar horario</button>
                </div>
            </div>

        </div>
    </div>
</div>

    <script src="../assets/js/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/leaflet.js" ></script>
    <script src="../api/apiCaller.js"></script>
    <script src="this.js?x=1.08"></script>
</body>
</html>