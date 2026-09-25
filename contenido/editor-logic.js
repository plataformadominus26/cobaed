    /* =============================================
    LOGICA DEL EDITOR COBAED (v2.0)
    Maneja templates, uploads y preview en vivo
    ============================================= */

    let currentTemplate = 'text';

    $(document).ready(function() {
        // 1. Inicialización
        selectTemplate('text'); 
        $(".template-option").on('click', function() {
            selectTemplate($(this).data('tipo'));
        });
        // 2. Listeners Globales (Reactividad)
        // Cada vez que escriban o cambien algo, repintamos la preview
        $('#inputTitle, #inputBody, #inputBgStyle, #inputAudience, #inputYoutube').on('input change', function() {
            renderPreview();
        });

        // 3. Listeners de Archivos
        $('#inputImageSingle, #inputGallery, #inputFile').change(function() {
            renderPreview();
        });

        // 4. Lógica del Botón Publicar
        $('#btPublish').click(function() {
            // Aquí recogerías los datos para enviarlos al servidor
            const status = $('#inputStatus').val();
            alert("Simulación: Contenido enviado correctamente con estado: " + status.toUpperCase());
        });
    });

    /* --- FUNCIONES PRINCIPALES --- */

     function selectTemplate(type) {
        currentTemplate = type;
        targetType =$(".template-option[data-tipo='" + type + "']");
        
        // UI: Marcar tarjeta activa visualmente
        $('.template-option').removeClass('active border-success bg-success-subtle');
        // Nota: event.currentTarget funciona al hacer click
        
            targetType.addClass('active border-success bg-success-subtle');
        

        // UI: Ocultar todos los inputs especiales
        $('#groupImageSingle, #groupGallery, #groupFile, #groupVideo').addClass('d-none');
        
        // UI: Mostrar solo el necesario
        if (type === 'standard') $('#groupImageSingle').removeClass('d-none');
        if (type === 'mosaic' || type === 'carousel') $('#groupGallery').removeClass('d-none');
        if (type === 'file') $('#groupFile').removeClass('d-none');
        if (type === 'video') $('#groupVideo').removeClass('d-none');

        renderPreview();
    }

    // El corazón del editor: Construye el HTML de la preview
    function renderPreview() {
        let title = $('#inputTitle').val() || 'Título de la Publicación';
        let body = $('#inputBody').val() || 'El contenido de tu publicación aparecerá aquí...';
        let bgClass = $('#inputBgStyle').val(); // Clase CSS del select
        let audienceVal = $('#inputAudience').val();

        // 1. Lógica de Audiencia (Badge)
        let audLabel = 'Público General';
        let audIcon = 'bi-globe';
        
        if(audienceVal === 'plantel') { audLabel = 'Solo Plantel'; audIcon = 'bi-building'; }
        if(audienceVal === 'friends') { audLabel = 'Amigos'; audIcon = 'bi-people'; }
        if(audienceVal === 'group')   { audLabel = 'Mi Grupo'; audIcon = 'bi-lock'; }

        // 2. Estilos de Fondo (Tapiz)
        let cardStyle = "";
        if(bgClass === 'bg-gold-subtle') cardStyle = 'background-color: #fff9e6; border: 1px solid #e6d3a3;';
        if(bgClass === 'bg-green-subtle') cardStyle = 'background-color: #e8f5e9; border: 1px solid #a5d6a7;';
        if(bgClass === 'bg-dark-subtle') cardStyle = 'background-color: #212529; color: white; border: 1px solid #333;';
        
        // Ajuste de texto para modo oscuro
        let textClass = bgClass === 'bg-dark-subtle' ? 'text-white' : 'text-dark';
        let mutedClass = bgClass === 'bg-dark-subtle' ? 'text-white-50' : 'text-muted';

        // 3. HTML del Header (Común)
        let headerHTML = `
            <div class="feed-header">
                <div class="poster-info">
                    <img src="https://ui-avatars.com/api/?name=Admin+Cobaed&background=005931&color=fff" class="poster-avatar">
                    <div>
                        <h6 class="poster-name ${textClass}">Administración</h6>
                        <p class="post-date ${mutedClass}">
                            Hace un momento • <i class="bi ${audIcon}"></i> ${audLabel}
                        </p>
                    </div>
                </div>
                <i class="bi bi-three-dots ${mutedClass}"></i>
            </div>
        `;

        // 4. HTML del Contenido (Variable)
        let contentHTML = '';

        // --- CASO A: TEXTO SIMPLE ---
        if (currentTemplate === 'text') {
            contentHTML = `<p class="text-highlight large mb-0 ${textClass}">${nl2br(body)}</p>`;
        }

       // --- CASO B: IMAGEN STANDARD (Una foto) ---
    else if (currentTemplate === 'standard') {
        // 1. Placeholder por defecto (si no hay archivo)
        let imgDisplay = '<div class="bg-light text-center py-5 rounded mb-2 border text-muted"><i class="bi bi-image fs-1"></i><br>Sin Imagen</div>';
        
        // 2. Si hay archivo seleccionado, renderizamos la vista previa real
        const fileInput = $('#inputImageSingle')[0];
        
        if (fileInput && fileInput.files && fileInput.files[0]) {
            // Creamos una URL temporal local para el archivo
            const imgURL = URL.createObjectURL(fileInput.files[0]);
            
            // Reemplazamos el placeholder con la imagen (estilos inline para asegurar el look "cover")
            imgDisplay = `
                <div class="ratio ratio-16x9 mb-3">
                    <img src="${imgURL}" class="rounded-3" style="object-fit: cover; width: 100%; height: 100%;">
                </div>
            `;
        }
        
        contentHTML = `
            <h5 class="post-title ${textClass}">${title}</h5>
            ${imgDisplay}
            <p class="small mb-2 ${mutedClass}">${nl2br(body)}</p>
        `;
    }
        // --- CASO C: VIDEO (YouTube Embed) ---
        else if (currentTemplate === 'video') {
            let url = $('#inputYoutube').val();
            let videoId = getYouTubeID(url);
            let embedBlock = '';

            if(videoId) {
                embedBlock = `
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden mb-3 shadow-sm">
                        <iframe src="https://www.youtube.com/embed/${videoId}" allowfullscreen></iframe>
                    </div>`;
            } else {
                embedBlock = `<div class="alert alert-secondary border text-center small py-4">Pega un link válido de YouTube...</div>`;
            }

            contentHTML = `
                <h5 class="post-title ${textClass}">${title}</h5>
                ${embedBlock}
                <p class="small mb-2 ${mutedClass}">${nl2br(body)}</p>
            `;
        }

        // --- CASO D: ARCHIVOS (PDF / Word / Excel) ---
        else if (currentTemplate === 'file') {
            let fileName = "Documento.pdf";
            let fileType = "pdf";
            let iconClass = "bi-file-earmark-pdf-fill"; 
            let iconColor = "text-danger";

            // Detectar extensión real del input
            if($('#inputFile')[0].files[0]) {
                fileName = $('#inputFile')[0].files[0].name;
                let ext = fileName.split('.').pop().toLowerCase();
                
                if(ext.includes('doc')) { 
                    fileType = "word"; iconClass = "bi-file-earmark-word-fill"; iconColor = "text-primary"; 
                }
                if(ext.includes('xls')) { 
                    fileType = "excel"; iconClass = "bi-file-earmark-excel-fill"; iconColor = "text-success"; 
                }
                if(ext.includes('ppt')) {
                    fileType = "ppt"; iconClass = "bi-file-earmark-slides-fill"; iconColor = "text-warning";
                }
            }

            contentHTML = `
                <p class="mb-2 fw-bold ${textClass}">${title}</p>
                <p class="small mb-2 ${mutedClass}">${nl2br(body)}</p>
                
                <div class="doc-embed-card bg-white border">
                    <i class="bi ${iconClass} ${iconColor} fs-1 me-3"></i>
                    <div class="doc-info">
                        <h6 class="mb-0 text-dark">${fileName}</h6>
                        <span class="small text-muted text-uppercase">${fileType} • Descargar</span>
                    </div>
                    <div class="ms-auto"><i class="bi bi-download text-muted"></i></div>
                </div>
            `;
        }

       // --- CASO E: CARRUSEL / GALERÍA ---
    else if (currentTemplate === 'carousel' || currentTemplate === 'mosaic') {
        
        // 1. Preparar Arreglo de Fuentes (URLs)
        const fileInput = $('#inputGallery')[0];
        let imgSources = [];

        if (fileInput && fileInput.files && fileInput.files.length > 0) {
            // Opción A: El usuario subió archivos -> Crear URLs temporales
            Array.from(fileInput.files).forEach(file => {
                imgSources.push(URL.createObjectURL(file));
            });
        } else {
            // Opción B: No hay archivos -> Usar Demos
            imgSources = [
                '../assets/img/posts/1.jpg',
                '../assets/img/posts/2.jpg',
                '../assets/img/posts/3.jpg'
            ];
        }

        // 2. Construir HTML Visual (Carrusel o Mosaico)
        let visualHTML = '';

        if (currentTemplate === 'carousel') {
            // --> Construcción CARRUSEL
            let slides = imgSources.map(src => 
                `<div class="carousel-item-wrapper"><img src="${src}" style="width:100%; height:100%; object-fit:cover;"></div>`
            ).join('');
            
            visualHTML = `<div class="media-carousel">${slides}</div>`;

        } else {
            // --> Construcción MOSAICO (Grid)
            // Lógica: Mostrar máximo 4. Si hay más, la 4ta tiene overlay.
            let gridItems = '';
            const maxShow = 4;
            const total = imgSources.length;
            const extra = total - maxShow;

            // Tomamos solo las primeras 4 para el grid
            imgSources.slice(0, maxShow).forEach((src, index) => {
                let overlay = '';
                // Si estamos en la 4ta foto (índice 3) y sobran fotos...
                if (index === 3 && extra > 0) {
                    overlay = `<div class="mosaic-overlay">+${extra}</div>`;
                }
                gridItems += `<div class="mosaic-item"><img src="${src}">${overlay}</div>`;
            });

            visualHTML = `<div class="mosaic-grid">${gridItems}</div>`;
        }

        // 3. Ensamblar Contenido Final
        contentHTML = `
            <p class="mb-1 fw-bold ${textClass}">${title}</p>
            <span class="album-indicator"><i class="bi bi-images me-1"></i>Galería Multimedia</span>
            ${visualHTML}
            <p class="small mb-2 ${mutedClass}">${nl2br(body)}</p>
        `;
    }
        // 5. Ensamblaje Final
        let finalHTML = `
            <div class="feed-card animate__animated animate__fadeIn" style="${cardStyle}">
                ${headerHTML}
                <div class="feed-body">
                    ${contentHTML}
                </div>
                <div class="feed-footer" style="border-top:1px solid rgba(0,0,0,0.05)">
                    <div class="rating-box small text-warning"><i class="bi bi-star-fill"></i> Nuevo</div>
                    <button class="action-btn ${mutedClass}">
                        <i class="bi bi-chat"></i>
                    </button>
                </div>
            </div>
        `;

        $('#livePreviewSlot').html(finalHTML);
    }

    /* --- HELPERS --- */

    // Convertir saltos de línea a <br>
    function nl2br (str) {
        return str ? str.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2') : '';
    }

    // Extraer ID de YouTube de cualquier URL (largo, corto, embed)
    function getYouTubeID(url) {
        if (!url) return null;
        let regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        let match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }