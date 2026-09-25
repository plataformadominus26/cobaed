<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Nuevo Post</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="feed-styles.css"> 
    <style>
        .feed-scope .feed-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
    margin-bottom: 25px;
    overflow: hidden;
    border: 1px solid rgba(168, 160, 0, 0.68);
    transition: transform 0.2s;
}

.feed-scope .text-highlight.large {
    font-size: 1.4rem;
    font-weight: 300;
    color: #005931;
}

.feed-scope .text-highlight {
    font-size: 1.1rem;
    line-height: 1.5;
    color: #2c3e50;
}

.mobile-preview-container {
     background-image: url('../assets/img/doodlecobaed.png');
     background-size: contain;
     background-position: center;
}

.feed-scope .poster-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--cobaed-gold, #C49A50);
}

.feed-scope .poster-info {
    display: flex;
    align-items: center;
    gap: 12px;
}
.feed-scope .feed-header {
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.feed-scope .post-date {
    font-size: 0.75rem;
    color: #999;
    margin: 0;
}

.feed-scope .rating-box {
    color: #ffc107;
    font-weight: 600;
    font-size: 0.9rem;
}

.feed-scope .action-btn {
    background: none;
    border: none;
    color: #666;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    border-radius: 50px;
    transition: all 0.2s;
}

.feed-scope .rating-box {
    color: #ffc107;
    font-weight: 600;
    font-size: 0.9rem;
}

.feed-scope .feed-footer {
    padding: 12px 20px;
    border-top: 1px solid #f1f1f1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #fff;
}

.feed-scope .feed-body {
    padding: 0 20px 15px 20px;
    color: #444;
}

.feed-scope .poster-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.feed-scope .feed-body {
    padding: 0 20px 15px 20px;
    color: #444;
}

.feed-scope .doc-icon {
    font-size: 2.5rem;
    color: #dc3545;
}
.feed-scope .doc-embed-card {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
    margin-top: 10px;
    transition: background 0.2s;
}

.feed-scope .carousel-item-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.feed-scope .album-indicator {
    font-size: 0.8rem;
    color: var(--cobaed-green);
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
}

.feed-scope .media-carousel {
    display: flex;
    overflow-x: auto;
    gap: 12px;
    padding-bottom: 10px;
    margin-top: 10px;
    margin-bottom: 15px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.feed-scope .mosaic-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.feed-scope .mosaic-item {
    position: relative;
    width: 100%;
    height: 100%;
}

.feed-scope .mosaic-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-template-rows: repeat(2, 140px);
    gap: 6px;
    border-radius: 12px;
    overflow: hidden;
    margin-top: 10px;
    margin-bottom: 15px;
}



    </style>
     
     
</head>
<body>

<div class="container-fluid py-4">
    <div class="row">
        
 <div class="col-lg-7">
    <form id="editorForm">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4 text-dark"><span class="badge bg-dark me-2">1</span>Elige el Formato</h5>
                
                <div class="row g-2 mb-4">
                    <div class="col-4 col-md-2">
                        <div class="template-option p-2 text-center h-100" data-tipo="text">
                            <i class="bi bi-fonts fs-3 text-secondary"></i>
                            <div class="small fw-bold mt-1">Texto</div>
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="template-option p-2 text-center h-100" data-tipo="standard">
                            <i class="bi bi-image fs-3 text-success"></i>
                            <div class="small fw-bold mt-1">Foto</div>
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="template-option p-2 text-center h-100" data-tipo="mosaic">
                            <i class="bi bi-images fs-3 text-warning"></i>
                            <div class="small fw-bold mt-1">Galería</div>
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="template-option p-2 text-center h-100" data-tipo="carousel">
                            <i class="bi bi-collection-play fs-3 text-info"></i>
                            <div class="small fw-bold mt-1">Carrusel</div>
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="template-option p-2 text-center h-100" data-tipo="file">
                            <i class="bi bi-paperclip fs-3 text-primary"></i>
                            <div class="small fw-bold mt-1">Archivo</div>
                        </div>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="template-option p-2 text-center h-100" data-tipo="video">
                            <i class="bi bi-youtube fs-3 text-danger"></i>
                            <div class="small fw-bold mt-1">Video</div>
                        </div>
                    </div>
                </div>

                <hr class="text-muted opacity-25">

                <h5 class="fw-bold mb-3 text-dark mt-4"><span class="badge bg-dark me-2">2</span>Contenido</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Título</label>
                    <input type="text" class="form-control bg-light" id="inputTitle" placeholder="Ej: Calendario de Exámenes">
                </div>

                <div class="mb-3 d-none bg-danger-subtle p-3 rounded border border-danger-subtle" id="groupVideo">
                    <label class="form-label fw-bold text-danger"><i class="bi bi-youtube me-2"></i>Link de YouTube</label>
                    <input type="url" class="form-control" id="inputYoutube" placeholder="https://www.youtube.com/watch?v=..." oninput="renderPreview()">
                    <div class="form-text text-danger-emphasis">Pega el link completo, nosotros hacemos la magia.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-muted">Descripción / Mensaje</label>
                    <textarea class="form-control bg-light" id="inputBody" rows="3"></textarea>
                </div>

                <div class="mb-3 d-none" id="groupImageSingle">
                    <label class="form-label">Subir Imagen de Portada</label>
                    <input type="file" class="form-control" accept="image/*" id="inputImageSingle">
                </div>

                <div class="mb-3 d-none" id="groupGallery">
                    <label class="form-label">Subir Múltiples Fotos</label>
                    <input type="file" class="form-control" accept="image/*" multiple id="inputGallery">
                </div>
                
                <div class="mb-3 d-none" id="groupFile">
                    <label class="form-label">Adjuntar Documento (PDF, Word, Excel)</label>
                    <input type="file" class="form-control" accept=".pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx" id="inputFile">
                    <div class="form-text">Formatos permitidos: PDF, Word, Excel.</div>
                </div>

                <div class="d-flex align-items-center gap-3 mt-3">
                    <label class="small fw-bold text-muted">Estilo de Fondo:</label>
                    <select class="form-select form-select-sm w-auto" id="inputBgStyle">
                        <option value="bg-white">Blanco (Limpieza)</option>
                        <option value="bg-gold-subtle">Dorado Institucional</option>
                        <option value="bg-green-subtle">Verde Institucional</option>
                        <option value="bg-dark-subtle">Modo Oscuro</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-5 bg-light">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3 text-dark"><span class="badge bg-dark me-2">3</span>Configuración de Publicación</h5>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-uppercase text-muted">¿Quién puede ver esto?</label>
                        <select class="form-select" id="inputAudience" onchange="renderPreview()">
                            <option value="public">🌍 Todo el Público</option>
                            <option value="plantel">🏫 Mi Plantel (Solo Alumnos)</option>
                            <option value="friends">👥 Mis Amigos / Seguidores</option>
                            <option value="group">🔒 Mi Grupo (502, 301, etc)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-uppercase text-muted">Estado Actual</label>
                        <select class="form-select" id="inputStatus" onchange="toggleDateInput()">
                            <option value="published">✅ Publicar Ahora</option>
                            <option value="draft">📁 Guardar como Borrador (On Hold)</option>
                            <option value="scheduled">📅 Programar Fecha...</option>
                        </select>
                    </div>

                    <div class="col-12 d-none" id="scheduledDateGroup">
                        <label class="form-label small fw-bold text-success">Fecha de Publicación Automática</label>
                        <input type="datetime-local" class="form-control border-success" id="inputDate">
                    </div>
                </div>

                <hr>

                <div class="d-grid">
                    <button type="button" class="btn btn-success btn-lg fw-bold shadow-sm" id="btPublish">
                        <i class="bi bi-rocket-takeoff-fill me-2"></i>CONFIRMAR PUBLICACIÓN
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
        <div class="card-body p-0">
            <h6 class="text-center text-muted mb-3 text-uppercase small ls-1">Vista Previa Móvil</h6>
            
            <div class="mobile-preview-container mx-auto" style="width:90% !important">
                <div class="mobile-header-notch"></div>
                
                <div class="feed-scope p-3">
                    
                    <div id="livePreviewSlot" >
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/jquery-3.7.1.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="editor-logic.js?x=1.05"></script>

</body>
</html>