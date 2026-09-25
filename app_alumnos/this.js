    $(document).ready(() => {
            if (localStorage["cobaed_tTkn"] === undefined || localStorage["cobaed_tTkn"] === null || localStorage["cobaed_tTkn"] === "" || localStorage["cobaed_tTkn"] === "undefined") {
            window.location.href = "../login_mobile.html";
        }

            cargarFeedBlog();

        $("#btSalir").on("click", function(){
            localStorage.removeItem("cobaed_tTkn");
            localStorage.removeItem("cobaed_nombre");
            localStorage.removeItem("cobaed_foto");
            window.location.href = "../login_mobile.html";
        })
     
        $("#btAreas").on("click", function(){
             // 1. Obtener la instancia del menú lateral
            const menuElement = document.getElementById('menuSocio');
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(menuElement);
            
            // 2. Si existe la instancia, cerrarla
            if (bsOffcanvas) {
                bsOffcanvas.hide();
            }
            
            // 3. Ejecutar tu función de cambio de tab
            irATab('tab-mapa');
        })
        $(".btReserva").on("click", abrirReservas);
        $("#btPase").on("click", abrirRegistroNuevoInvitado);
        payLoad={
                token: localStorage["cobaed_tTkn"]
            
        };

        apiCall("getProfile",payLoad)
        .then(function(response){
            if(response.ok){
                localStorage["cobaed_nombre"]=response.data.nombre;
                $("#h6Nombre").html(localStorage["cobaed_nombre"] || "Desconocido");
                $("#iStatus").html(response.data.puesto || " Cargo Desconocido");
                if( response.data.foto){
                   $("#imgPerfil").attr("src", response.data.foto);
                   localStorage["cobaed_foto"]=response.data.foto;
                }
                else{
                    localStorage["cobaed_foto"]="";
                    $("#imgPerfil").attr("src", "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Crect width='100' height='100' fill='%23cccccc'/%3E%3Ctext x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-family='Arial' font-size='40' fill='%23666666'%3E" + (response.data.nombre ? response.data.nombre.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : "?") + "%3C/text%3E%3C/svg%3E");
                }
            }else{
                alert("Error al obtener el perfil: "+response.message);
            }
        })
        .catch(function(error){
            console.log("Error:", error);
            alert("Error de red al obtener el perfil. Verifique su conexión.");
        })
            
            
        $("#h6Nombre").on("click", function () {
            location.reload();
        }).html(localStorage["ccd_nombre"] || "Invitado");


        // Escuchar clicks en el Footer
        $('.nav-link').click(function (e) {
            const section = $(this).data('section');
            if (section) {
                e.preventDefault();
                irATab('tab-' + section);
            }
        });
        // GPS con Capacitor    

        if (window.Capacitor) {
            const { StatusBar } = Capacitor.Plugins;

            // Hacer la barra de estado transparente y el texto claro/oscuro
            StatusBar.setOverlaysWebView({ overlay: true });
            StatusBar.setStyle({ style: 'DARK' }); // 'LIGHT' si el fondo es oscuro
        }
    });
    // Función de navegación global definitiva
    function irATab(tabId) {
        // 1. Extraer el nombre de la sección (ej. 'pases')
        const sectionName = tabId.replace('tab-', '');

        // 2. Encontrar el botón disparador oculto correspondiente
        const triggerEl = document.getElementById('trigger-' + sectionName);

        if (triggerEl) {
            // 3. Activar el tab usando el disparador (Standard Bootstrap 5)
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();

            // 4. Actualizar visualmente el footer
            $('footer .nav-link').removeClass('active text-campestre');
            $(`footer .nav-link[data-section="${sectionName}"]`).addClass('active text-campestre');

            // 5. Reset de scroll para sentirlo como página nueva
            window.scrollTo(0, 0);
        } else {
            console.warn("No se encontró el trigger para: " + sectionName);
        }

        if (tabId === 'tab-mapa') {
        setTimeout(() => {
            if (!map) {
                initMapa();
            } else {
                map.invalidateSize(); // Corrige errores de renderizado en tabs ocultos
            }
        }, 300);
    }
    }

    function abrirRegistroNuevoInvitado() {
        var myOffcanvas = document.getElementById('drawerPases');
        var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas);
        bsOffcanvas.show();
    }

     

function abrirReservas(){
        var myOffcanvas = document.getElementById('drawerReservas');
        var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas);
        bsOffcanvas.show();
}

 

 
function cargarFeedBlog() {
    return;
    apiCall("fetchPosts", { "scope": "all" })
    .then(result => {
        const $contenedor = $('#feed-container');
        $contenedor.empty();

        // SEGÚN TU SCREENSHOT: 'result' es el objeto y 'result.data' es el Array(1)
        const posts = result.data; 

        if (posts && Array.isArray(posts) && posts.length > 0) {
            posts.forEach(post => {
                // Pasamos el post directamente
                const postHTML = generarPostHTML(post);
                $contenedor.append(postHTML);
            });
        } else {
            $contenedor.html('<p class="text-center text-muted mt-4">No hay noticias por el momento.</p>');
        }
    })
    .catch(error => {
        console.error("Error cargando blog:", error);
    });
}
function generarPostHTML(post) {
    // contenido_json ya es un objeto/array de JS gracias a tu PHP
    const estructura = post.contenido_json; 
    
    let html = `<div class="blog-entry p-3 mb-4 shadow-sm border ${post.estilo === 'alerta' ? 'alerta' : ''}">`;

    if (Array.isArray(estructura)) {
        estructura.forEach(bloque => {
            const c = bloque.content;
            if (bloque.type === 'header') html += `<h6 class="fw-bold mb-3">${c.text}</h6>`;
            if (bloque.type === 'text') html += `<p class="small mb-3">${c.text}</p>`;
            
            if (bloque.type === 'gallery' && c.urls) {
                html += `<div class="gallery-scroll mb-3">`;
                c.urls.forEach(url => {
                    html += `<img src="../${url}" class="gallery-item shadow-sm">`;
                });
                html += `</div>`;
            }
            
            if (bloque.type === 'youtube') {
                html += `<div class="video-container mb-3 shadow-sm rounded-4">${c.embedHtml || ''}</div>`;
            }

            if (bloque.type === 'file') {
                html += `
                <a href="../${c.fileUrl}" target="_blank" class="file-link d-flex align-items-center mb-3 text-decoration-none text-dark">
                    <i class="bi bi-file-earmark-arrow-down-fill fs-3 me-2 text-warning"></i>
                    <span class="small fw-bold">${c.fileName}</span>
                </a>`;
            }
        });
    }

    html += `</div>`;
    return html;
}