<?php require_once __DIR__ . '/config.php'; ?>
    <div class="side-nav">
        <div class="nav-logo text-white">
            <!-- <i class="bi bi-egg-fried-fill me-2"></i>
            <span>sinPendientes</span>-->
            <img src="../assets/img/cobaed2010.png" alt="Logo" class="logo-img">
        </div>
        
         <div class="nav-menu">
                    <div class="nav-item" id="menuMaestros">
                        <a href="../empleados" class="nav-link text-white">
                            <i class="bi bi-people-fill"></i> Empleados
                        </a>
                    </div>   
                    <div class="nav-item" id="menuAreas">
                        <a href="../areas" class="nav-link text-white">
                            <i class="bi bi-building"></i> Areas
                        </a>
                    </div>   
                    <div class="nav-item" id="menuHorarios">
                        <a href="../horarios" class="nav-link text-white">
                            <i class="bi bi-calendar-week"></i> Horarios
                        </a>
                    </div>      
                     <div class="nav-item" id="menuAsistencia">
                        <a href="../asistencia" class="nav-link text-white">
                            <i class="bi bi-check2-square"></i> Asistencia
                        </a>
                    </div>       

                    <div class="nav-item" id="menuAlumnos">
                        <a href="../alumni" class="nav-link text-white">
                            <i class="bi bi-mortarboard-fill"></i> Alumnos
                        </a>
                    </div>   
                    <div class="nav-item" id="menuConocimientos">
                        <a href="#subMenuConocimientos" class="nav-link text-white" data-bs-toggle="collapse">
                            <i class="bi bi-book-fill"></i> Base de Conocimientos
                            <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse" id="subMenuConocimientos">
                            <div class="nav-item ms-3">
                                <a href="../academias" class="nav-link text-white">
                                    <i class="bi bi-mortarboard"></i> Academias
                                </a>
                            </div>
                            <div class="nav-item ms-3">
                                <a href="../materias" class="nav-link text-white">
                                    <i class="bi bi-file-text"></i> Materias
                                </a>
                            </div>
                            <div class="nav-item ms-3" id="menuTemas">
                                <a href="../temas" class="nav-link text-white">
                                    <i class="bi bi-bookmark-fill"></i> Temas
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="nav-item" id="menuCamera">
                        <a href="../camera" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#modalCamara">
                            <i class="bi bi-camera-fill"></i> Cámara
                        </a>
                    </div>
                    <div class="nav-item" id="menuConfiguracion">
                        <a href="#" class="nav-link text-white">
                            <i class="bi bi-gear-fill"></i> Configuracion
                        </a>
                    </div>
                    <div class="position-absolute bottom-0 w-100 mb-4">
                        <div class="nav-item">
                            <a href="#" class="nav-link text-white">
                                <i class="bi bi-box-arrow-left"></i>
                                Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>    
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="top-header glass-panel">
            <button class="btn btn-sm btn-outline-secondary nav-toggle d-none">
                <i class="bi bi-list"></i>
            </button>
            
            <div class="locale-selector">
                <button class="btn btn-sm btn-outline-secondary  " type="button" id="localeDropdown" data-bs-toggle="dropdown">
                    <i class="bi bi-globe2"></i>
                     <?= plantel_nombre() ?>
                </button>
                 
            </div>
            
            <div class="header-icons d-flex align-items-center">
                <div class="icon-badge">
                    <i class="bi bi-bell-fill"></i>
                    <span class="badge bg-danger">3</span>
                </div>
                <div class="icon-badge">
                    <i class="bi bi-envelope-fill"></i>
                    <span class="badge bg-primary">5</span>
                </div>
                <div class="icon-badge">
                    <i class="bi bi-list-task-fill"></i>
                    <span class="badge bg-warning">2</span>
                </div>
                
                <div class="user-menu dropdown ms-4">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User">
                        <span class="d-none d-md-inline">Admin</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Administrador</h6></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person-fill me-2"></i> Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear-fill me-2"></i> Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-left"></i> Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </header>
        <!-- QR Modal -->
        <div class="modal fade" id="modalCamara" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Habilitar Cámara Móvil</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <img  alt="QR Code" class="img-fluid" id="qrCodeImage">
                                <p class="mt-3 text-muted">Escanea el código QR para acceder</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>    
    <script>
        if(!localStorage["cobaed_camara"]){
             const randomToken = Array.from({length: 16}, () => Math.floor(Math.random() * 16).toString(16).toUpperCase()).join('');
            localStorage["cobaed_camara"] = randomToken;
        }
            document.addEventListener('DOMContentLoaded', function() {
            qrService="../../--id/assets/api/qr.php"
            params="?logo=camara&data=" + location.origin + "/cam/?estacion=" + localStorage["cobaed_camara"] ;
            finalUrl=qrService + params;
            qrImage = document.getElementById('qrCodeImage');
            qrImage.src = finalUrl;
        });

        function setMenu(menu, subMenu) {
             $('[data-bs-toggle="collapse"]').click(function (e) {
                const target = $(this).attr('href') || $(this).data('bs-target');
                $('.collapse.show').each(function () {
                    if ('#' + this.id !== target) {
                        $(this).collapse('hide');
                    }
                });
            });

            
            $('.collapse')
                .on('show.bs.collapse', function () {
                    const trigger = $('[href="#' + this.id + '"], [data-bs-target="#' + this.id + '"]');
                    trigger.find('.bi-chevron-down').addClass('rotate-180');
                })
                .on('hide.bs.collapse', function () {
                    const trigger = $('[href="#' + this.id + '"], [data-bs-target="#' + this.id + '"]');
                    trigger.find('.bi-chevron-down').removeClass('rotate-180');
                });

            // Open Tareas submenu and highlight menuAreas
            const menuLink = $('#'+menu);
            const tareasSubmenu = $('#'+subMenu);
            if (tareasSubmenu.length && !tareasSubmenu.hasClass('show')) {
                new bootstrap.Collapse(tareasSubmenu[0], { toggle: false }).show();
            }
            menuLink
                .attr('aria-expanded', 'true')
                .find('.bi-chevron-down').addClass('rotate-180');
            menuLink.addClass('active active-menu-parent');
        }
    </script>
