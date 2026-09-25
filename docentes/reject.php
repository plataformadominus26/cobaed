<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Restringido - COBAED</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --cobaed-primary: #0d6efd;
            --cobaed-secondary: #1a4f8c;
            --cobaed-warning: #ffc107;
            --cobaed-danger: #dc3545;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .restricted-container {
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            overflow: hidden;
            background-color: white;
        }
        
        .header-section {
            background: linear-gradient(135deg, var(--cobaed-primary), var(--cobaed-secondary));
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .logo {
            height: 80px;
            width: 80px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .logo-icon {
            font-size: 2.5rem;
            color: var(--cobaed-primary);
        }
        
        .cobaed-name {
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .content-section {
            padding: 2.5rem;
        }
        
        .warning-icon {
            color: var(--cobaed-warning);
            font-size: 5rem;
            margin-bottom: 1.5rem;
        }
        
        .warning-message {
            color: var(--cobaed-danger);
            margin-bottom: 1.5rem;
            font-weight: 700;
            text-align: center;
        }
        
        .alert-section {
            background-color: #fff3cd;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
            border: 1px solid #ffc107;
            position: relative;
        }
        
        .alert-icon {
            color: #856404;
            font-size: 1.5rem;
            margin-right: 10px;
        }
        
        .alert-title {
            color: #856404;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .instruction-box {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-left: 4px solid var(--cobaed-primary);
        }
        
        .instruction-title {
            color: var(--cobaed-primary);
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .contact-info {
            background-color: #e9ecef;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .contact-title {
            color: var(--cobaed-secondary);
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .contact-icon {
            color: var(--cobaed-primary);
            font-size: 1.2rem;
            margin-right: 10px;
            width: 25px;
        }
        
        .btn-options {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        
        .btn-return {
            background-color: var(--cobaed-primary);
            color: white;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            border: none;
            display: flex;
            align-items: center;
        }
        
        .btn-return:hover {
            background-color: var(--cobaed-secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2);
        }
        
        .btn-contact {
            background-color: var(--cobaed-warning);
            color: #212529;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            border: none;
            display: flex;
            align-items: center;
        }
        
        .btn-contact:hover {
            background-color: #e0a800;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 193, 7, 0.2);
        }
        
        .footer-section {
            background-color: #f1f3f5;
            padding: 1.5rem;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
            border-top: 1px solid #dee2e6;
        }
        
        @media (max-width: 768px) {
            .cobaed-name {
                font-size: 1.8rem;
            }
            
            .content-section {
                padding: 1.5rem;
            }
            
            .btn-options {
                flex-direction: column;
            }
            
            .btn-return, .btn-contact {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="restricted-container">
            <!-- Header with logo -->
            <div class="header-section">
                <div class="logo-container">
                    <div class="logo">
                        <i class="bi bi-mortarboard-fill logo-icon"></i>
                    </div>
                    <div class="cobaed-name">COBAED</div>
                </div>
                <h1>Acceso Restringido</h1>
                <p class="mb-0">Colegio de Bachilleres del Estado de Durango</p>
            </div>
            
            <!-- Main content -->
            <div class="content-section text-center">
                <!-- Warning icon -->
                <div class="warning-icon">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
                
                <!-- Warning message -->
                <h2 class="warning-message">
                    Sitio exclusivo para docentes
                </h2>
                
                <p class="lead mb-4">
                    Esta área del sistema está reservada exclusivamente para personal docente del COBAED.
                </p>
                
                <!-- Alert section -->
                <div class="alert-section">
                    <div class="alert-title">
                        <i class="bi bi-shield-exclamation alert-icon"></i>
                        Acceso no autorizado detectado
                    </div>
                    <p>
                        Su perfil de usuario no tiene los privilegios necesarios para acceder a esta sección. 
                        Si usted es un empleado académico (docente), por favor diríjase a Servicios Escolares 
                        para solicitar la activación de su cuenta de docente o regularizar su situación.
                    </p>
                    <p class="mb-0">
                        Si usted es estudiante, padre de familia o personal administrativo, utilice los 
                        portales correspondientes a su perfil.
                    </p>
                </div>
                
                <!-- Instructions -->
                <div class="instruction-box text-start">
                    <div class="instruction-title">
                        <i class="bi bi-info-circle me-2"></i>Procedimiento para docentes:
                    </div>
                    <ol class="mb-0">
                        <li>Acuda a la Dirección de Servicios Escolares de su plantel.</li>
                        <li>Presente su identificación oficial y comprobante de nombramiento.</li>
                        <li>Solicite la activación de su cuenta de docente en el sistema.</li>
                        <li>Reciba sus credenciales de acceso por correo institucional.</li>
                        <li>Ingrese nuevamente con sus nuevas credenciales.</li>
                    </ol>
                </div>
                
                <!-- Contact information -->
                <div class="contact-info">
                    <div class="contact-title">
                        <i class="bi bi-telephone me-2"></i>Contacto y asistencia:
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <strong>Dirección de Servicios Escolares:</strong><br>
                            Av. Universidad 123, Zona Centro, Durango, Dgo.
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div>
                            <strong>Teléfono:</strong> (618) 123-4567 ext. 102
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <strong>Correo electrónico:</strong> servicios.escolares@cobaed.edu.mx
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <div>
                            <strong>Horario de atención:</strong> Lunes a Viernes de 8:00 a 16:00 hrs.
                        </div>
                    </div>
                </div>
                
                <!-- Action buttons -->
                <div class="btn-options">
                    <button class="btn-return" id="returnButton">
                        <i class="bi bi-arrow-left-circle-fill me-2"></i> Volver al Portal Principal
                    </button>
                    
                    <button class="btn-contact" id="helpButton">
                        <i class="bi bi-question-circle-fill me-2"></i> Solicitar Ayuda
                    </button>
                </div>
                
                <!-- Additional information -->
                <div class="mt-4 small text-muted">
                    <p class="mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Si continúa intentando acceder sin autorización, su cuenta podría ser suspendida.
                    </p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer-section">
                <p class="mb-0">
                    © 2023 COBAED - Colegio de Bachilleres del Estado de Durango. Acceso restringido a personal autorizado.
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Button functionality
        const returnButton = document.getElementById('returnButton');
        const helpButton = document.getElementById('helpButton');
        
        // Return to main portal button
        returnButton.addEventListener('click', function() {
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Redirigiendo...';
            this.disabled = true;
            
            // Simulate a delay for redirection
            setTimeout(() => {
                alert("En una implementación real, esto redirigiría al portal principal del COBAED.");
                // Reset button
                this.innerHTML = originalText;
                this.disabled = false;
            }, 1500);
        });
        
        // Help button
        helpButton.addEventListener('click', function() {
            // Show help options
            const helpOptions = `
            <div class="alert alert-info text-start">
                <h5><i class="bi bi-info-circle-fill me-2"></i>Opciones de ayuda:</h5>
                <ul>
                    <li><strong>Llamar a Servicios Escolares:</strong> (618) 123-4567 ext. 102</li>
                    <li><strong>Enviar correo:</strong> servicios.escolares@cobaed.edu.mx</li>
                    <li><strong>Chat en línea:</strong> Disponible de 9:00 a 15:00 hrs</li>
                    <li><strong>Visita presencial:</strong> Dirección de Servicios Escolares</li>
                </ul>
                <p class="mb-0">Por favor, mencione el código de error: <strong>ACC-DEN-023</strong></p>
            </div>
            `;
            
            // In a real implementation, this would show a modal
            alert("Opciones de ayuda:\n\n1. Llamar a Servicios Escolares: (618) 123-4567 ext. 102\n2. Enviar correo: servicios.escolares@cobaed.edu.mx\n3. Chat en línea: Disponible de 9:00 a 15:00 hrs\n4. Visita presencial: Dirección de Servicios Escolares\n\nCódigo de error: ACC-DEN-023");
        });
        
        // Show warning message after a few seconds
        setTimeout(() => {
            const warningDiv = document.createElement('div');
            warningDiv.className = 'alert alert-warning alert-dismissible fade show mt-3';
            warningDiv.innerHTML = `
                <i class="bi bi-clock-history me-2"></i>
                <strong>Nota:</strong> Esta página se cerrará automáticamente en <span id="countdown">30</span> segundos.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            document.querySelector('.content-section').appendChild(warningDiv);
            
            // Countdown timer
            let countdown = 30;
            const countdownElement = document.getElementById('countdown');
            const countdownInterval = setInterval(() => {
                countdown--;
                countdownElement.textContent = countdown;
                
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    alert("Sesión finalizada. Redirigiendo al portal principal...");
                    // In a real implementation, this would redirect to the main portal
                }
            }, 1000);
        }, 3000);
    </script>
</body>
</html>