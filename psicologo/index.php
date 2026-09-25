<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cobaed Care | Command Center</title>
    
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --cobaed-green: #00A859; 
            --bg-chat: #e5ddd5;
            --bubble-me: #dcf8c6; 
            --bubble-them: #ffffff;
            --header-height: 70px;
        }

        /* --- BASE LAYOUT (DESKTOP) --- */
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #f0f2f5; 
            height: 100vh; 
            overflow: hidden; /* Evita scroll en el body, todo scroll es interno */
            margin: 0;
        }
        
        .dashboard-container { 
            height: 100%; 
            display: flex; 
            width: 100%;
        }
        
        /* 1. COLUMNA IZQUIERDA (LISTA) */
        .sidebar-chats { 
            width: 350px; 
            min-width: 300px;
            background: white; 
            border-right: 1px solid #ddd; 
            display: flex; 
            flex-direction: column; 
            z-index: 20;
        }

        .chat-list-item {
            cursor: pointer;
            transition: 0.2s;
            border-bottom: 1px solid #f8f9fa;
        }
        .chat-list-item:hover { background-color: #f5f5f5; }
        .chat-list-item.active { background-color: #e8f5e9; border-left: 4px solid var(--cobaed-green); }
        
        /* 2. COLUMNA CENTRAL (CHAT) */
        .main-chat { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            background-color: #efe7dd; 
            background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
            opacity: 0.96;
            position: relative;
            min-width: 0; /* Previene desbordes en flex */
        }

        .chat-header { 
            background: white; 
            padding: 0 20px; 
            border-bottom: 1px solid #ddd; 
            height: var(--header-height); 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            flex-shrink: 0;
        }

        .chat-messages { 
            flex: 1; 
            overflow-y: auto; 
            padding: 20px; 
            display: flex;
            flex-direction: column;
        }

        .chat-input-area { 
            background: #f0f2f5; 
            padding: 15px; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            flex-shrink: 0;
        }
        
        /* Burbujas */
        .msg { 
            max-width: 75%; 
            margin-bottom: 10px; 
            padding: 10px 15px; 
            border-radius: 12px; 
            position: relative; 
            font-size: 0.95rem; 
            box-shadow: 0 1px 2px rgba(0,0,0,0.1); 
            word-wrap: break-word;
        }
        .msg-them { background: var(--bubble-them); align-self: flex-start; border-top-left-radius: 0; }
        .msg-me { background: var(--bubble-me); align-self: flex-end; margin-left: auto; border-top-right-radius: 0; }
        .msg-time { font-size: 0.7rem; color: #999; display: block; text-align: right; margin-top: 4px; }

        /* 3. COLUMNA DERECHA (INFO) */
        .sidebar-info { 
            width: 320px; 
            background: white; 
            border-left: 1px solid #ddd; 
            padding: 20px; 
            overflow-y: auto;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }
        
        /* Utilidades */
        .avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
        .badge-alert { background-color: #ff3b30; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; }
        
        /* Botón atrás móvil (oculto por defecto) */
        .btn-back-mobile { display: none; }


        /* --- RESPONSIVE MEDIA QUERIES --- */

        /* A. TABLETS Y PANTALLAS MEDIANAS (< 1200px) */
        /* Convertimos el panel derecho en un "Drawer" flotante */
        @media (max-width: 1200px) {
            .sidebar-info {
                position: fixed;
                right: 0;
                top: 0;
                height: 100%;
                z-index: 1050;
                box-shadow: -5px 0 20px rgba(0,0,0,0.15);
                transform: translateX(100%); /* Oculto a la derecha */
            }
            .sidebar-info.show {
                transform: translateX(0); /* Visible */
            }
        }

        /* B. MÓVILES (< 768px) */
        /* Intercambio de vistas: O ves Lista O ves Chat */
       /* --- B. MÓVILES (< 768px) --- */
@media (max-width: 768px) {
    
    /* 1. La Lista ocupa toda la pantalla por defecto */
    .sidebar-chats {
        width: 100%;
        position: absolute;
        height: 100%;
        border-right: none;
        z-index: 20; /* Nivel base */
        display: flex; /* Asegura que se vea */
    }

    /* 2. El Chat está oculto a la derecha */
    .main-chat {
        width: 100%;
        position: absolute;
        height: 100%;
        z-index: 30; /* CORRECCIÓN: Mayor que la lista */
        transform: translateX(100%); 
        transition: transform 0.3s ease-in-out;
        background-color: #efe7dd; /* Asegurar fondo opaco */
    }

    /* 3. ESTADO ACTIVO (Clase mágica) */
    
    /* Cuando el chat está activo, el chat entra */
    body.mobile-chat-active .main-chat {
        transform: translateX(0);
    }

    /* Y LA LISTA DESAPARECE (Para evitar conflictos de touch/scroll) */
    body.mobile-chat-active .sidebar-chats {
        display: none; 
    }

    /* Mostrar botón atrás */
    .btn-back-mobile { 
        display: block; 
        margin-right: 10px; 
    }
    
    /* Ajuste visual de burbujas */
    .msg { max-width: 85%; }
}
    </style>
</head>
<body>

<div class="dashboard-container">
    
    <div class="sidebar-chats">
        <div class="p-3 text-white d-flex justify-content-between align-items-center" style="background: var(--cobaed-green); height: var(--header-height);">
            <div class="d-flex align-items-center">
                <i class="bi bi-shield-plus fs-4 me-2"></i>
                <div>
                    <h6 class="mb-0 fw-bold">Cobaed Care</h6>
                    <small style="opacity: 0.8;">Psicología</small>
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-link text-white p-0" type="button"><i class="bi bi-three-dots-vertical"></i></button>
            </div>
        </div>
        
        <div class="p-2 border-bottom bg-light">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Buscar alumno...">
            </div>
        </div>

        <div class="flex-grow-1 overflow-auto" id="inboxList">
            <div class="chat-list-item p-3 d-flex align-items-center active" onclick="cargarChat('Juan Pérez', '4A')">
                <img src="https://ui-avatars.com/api/?name=Juan+Perez&background=random" class="avatar me-3">
                <div class="flex-grow-1 overflow-hidden">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0 text-truncate">Juan Pérez</h6>
                        <small class="text-muted">10:45</small>
                    </div>
                    <small class="text-muted text-truncate d-block">Gracias, eso haré entonces...</small>
                </div>
            </div>

            <div class="chat-list-item p-3 d-flex align-items-center" onclick="cargarChat('Ana López', '2B')">
                <div class="position-relative">
                    <img src="https://ui-avatars.com/api/?name=Ana+Lopez&background=ea4335&color=fff" class="avatar me-3">
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0 text-truncate fw-bold">Ana López</h6>
                        <span class="badge-alert">SOS</span>
                    </div>
                    <small class="text-dark fw-bold text-truncate d-block">Necesito ayuda urgente por favor</small>
                </div>
            </div>
        </div>
    </div>

    <div class="main-chat">
        <div class="chat-header shadow-sm bg-white">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-dark p-0 btn-back-mobile" onclick="cerrarChatMobile()">
                    <i class="bi bi-arrow-left fs-3"></i>
                </button>

                <img src="https://ui-avatars.com/api/?name=Juan+Perez&background=random" class="avatar me-3" id="headerAvatar">
                <div>
                    <h6 class="mb-0 fw-bold" id="headerName">Juan Pérez</h6>
                    <small class="text-success" id="headerStatus">
                        <i class="bi bi-circle-fill" style="font-size: 8px;"></i> En línea
                    </small>
                </div>
            </div>
            
            <div>
                <button class="btn btn-outline-secondary btn-sm rounded-pill me-1 d-none d-md-inline-block">
                    <i class="bi bi-telephone-fill"></i>
                </button>
                <button class="btn btn-outline-success btn-sm rounded-pill" onclick="toggleInfoPanel()">
                    <i class="bi bi-person-lines-fill"></i> <span class="d-none d-md-inline">Expediente</span>
                </button>
            </div>
        </div>

        <div class="chat-messages" id="messagesArea">
            <div class="text-center my-3"><small class="text-muted bg-white px-3 py-1 rounded-pill shadow-sm border">Hoy</small></div>
            
            <div class="msg msg-them">Hola consejero, ¿tiene un momento? <span class="msg-time">10:42 AM</span></div>
            <div class="msg msg-me">Claro Juan, estoy aquí. ¿Qué sucede? <span class="msg-time">10:43 AM</span></div>
            <div class="msg msg-them">Es sobre los exámenes parciales... me siento muy presionado. <span class="msg-time">10:45 AM</span></div>
        </div>

        <div class="chat-input-area border-top">
            <button class="btn btn-light rounded-circle text-muted d-none d-md-block"><i class="bi bi-emoji-smile"></i></button>
            <button class="btn btn-light rounded-circle text-muted"><i class="bi bi-paperclip"></i></button>
            <input type="text" class="form-control rounded-pill border-0 bg-white shadow-sm py-2" placeholder="Escribe un mensaje..." id="txtInput">
            <button class="btn btn-success rounded-circle shadow-sm text-white" style="width: 45px; height: 45px; background: var(--cobaed-green);" onclick="enviarMensaje()">
                <i class="bi bi-send-fill ps-1"></i>
            </button>
        </div>
    </div>

    <div class="sidebar-info" id="panelInfo">
        <div class="d-flex justify-content-between align-items-center mb-4 d-xl-none">
            <h6 class="fw-bold m-0 text-success">Expediente Alumno</h6>
            <button class="btn btn-sm btn-light rounded-circle" onclick="toggleInfoPanel()"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="text-center mb-4">
            <img src="https://ui-avatars.com/api/?name=Juan+Perez&background=random" class="avatar mb-3" style="width: 90px; height: 90px;" id="infoAvatar">
            <h5 class="mb-0 fw-bold" id="infoName">Juan Pérez</h5>
            <p class="text-muted small">ID: 283921 • Semestre 4</p>
            <span class="badge bg-warning text-dark border border-warning">Riesgo Académico: Bajo</span>
        </div>

        <hr>

        <h6 class="fw-bold text-muted text-uppercase small mb-3"><i class="bi bi-journal-text me-1"></i> Notas Privadas</h6>
        <div class="form-floating mb-4">
            <textarea class="form-control bg-light border-0" placeholder="Notas" style="height: 120px; font-size: 0.9rem;"></textarea>
            <label>Observaciones de sesión...</label>
        </div>

        <h6 class="fw-bold text-muted text-uppercase small mb-3"><i class="bi bi-lightning-charge me-1"></i> Acciones</h6>
        <div class="d-grid gap-2">
            <button class="btn btn-outline-primary btn-sm text-start py-2"><i class="bi bi-calendar-event me-2"></i> Agendar Cita</button>
            <button class="btn btn-outline-danger btn-sm text-start py-2"><i class="bi bi-exclamation-triangle-fill me-2"></i> Reportar Riesgo (SOS)</button>
        </div>
        
        <div class="mt-4 p-3 bg-light rounded small text-muted">
            <i class="bi bi-info-circle me-1"></i> Última conexión: Ayer
        </div>
    </div>

</div>

<script src="../assets/js/jquery-3.7.1.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script>
    // --- LÓGICA DE UI Y RESPONSIVIDAD ---

    // 1. Cargar Chat (Simulado)
    function cargarChat(nombre, grupo) {
        console.log("Abriendo chat: " + nombre);
        
        // Actualizar UI Header y Panel Info
        $("#headerName").text(nombre);
        $("#infoName").text(nombre);
        $("#headerAvatar").attr("src", `https://ui-avatars.com/api/?name=${nombre}&background=random`);
        $("#infoAvatar").attr("src", `https://ui-avatars.com/api/?name=${nombre}&background=random`);

        // Marcar activo en la lista
        $(".chat-list-item").removeClass("active");
        // (En producción usarías $(this) o un ID único)
        
        // MOSTRAR CHAT EN MÓVIL (Clase mágica)
        $("body").addClass("mobile-chat-active");
        
        // Aquí llamarías a tu wrapper: myFirebase.listen(id_alumno)...
        scrollToBottom();
    }

    // 2. Cerrar Chat en Móvil (Volver a lista)
    function cerrarChatMobile() {
        $("body").removeClass("mobile-chat-active");
    }

    // 3. Toggle Panel Info (Tablet/Móvil)
    function toggleInfoPanel() {
        $("#panelInfo").toggleClass("show");
    }

    // 4. Enviar Mensaje
    function enviarMensaje() {
        let txt = $("#txtInput").val().trim();
        if(!txt) return;

        // Render Optimista
        $("#messagesArea").append(`
            <div class="msg msg-me animate__animated animate__fadeIn">
                ${txt}
                <span class="msg-time">Ahora</span>
            </div>
        `);
        
        $("#txtInput").val("");
        scrollToBottom();
        // Aquí: myFirebase.send(...)
    }

    // Utilidad Scroll
    function scrollToBottom(){
        let d = document.getElementById("messagesArea");
        d.scrollTop = d.scrollHeight;
    }

    // Evento Enter en Input
    $("#txtInput").on('keypress',function(e) {
        if(e.which == 13) { enviarMensaje(); }
    });

    // Resetear vistas al cambiar tamaño de pantalla (evitar bugs visuales)
    $(window).resize(function() {
        if (window.innerWidth > 768) {
            $("body").removeClass("mobile-chat-active");
        }
    }); 

</script>

</body>
</html>