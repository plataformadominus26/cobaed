<?php
  include_once("rbh/conexion1.php");
  include_once("rbh.php");

  if(isset($_REQUEST["imei"])){

    $imei = $_REQUEST["imei"];
    $ttkn = $_REQUEST["ttkn"];

    $sql="update usuarios set imei='$imei' where token='$ttkn' ";
    if($db->query($sql)){
        echo "IMEI registrado correctamente.". $sql;
    }else{
        echo "Error al registrar IMEI.";
    }
    exit;
  }

    $token = $_REQUEST['ttkn']?:'error';
    $dbToken ="error";
    $dbNombre ="error";
    $dbPassword ="error";
    $dbImei ="error";


    $sql="select * from usuarios where token='$token' ";
    if($row = $db->query($sql)->fetch_array()) {
        $dbToken = $row['token'];
        $dbNombre = $row['nombre'];
        $dbPassword = $row['pwd'];
        $dbImei = $row['imei']?:'No registrado';
    }  
 ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Maestro - COBAED</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --cobaed-primary: #0d6efd;
            --cobaed-secondary: #1a4f8c;
            --cobaed-accent: #198754;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .success-container {
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
        
        .welcome-message {
            color: var(--cobaed-secondary);
            margin-bottom: 1.5rem;
            font-weight: 500;
            border-left: 4px solid var(--cobaed-accent);
            padding-left: 15px;
        }
        
        .user-name {
            font-weight: 700;
            color: var(--cobaed-primary);
        }
        
        .password-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
            border: 1px solid #e9ecef;
            position: relative;
        }
        
        .password-label {
            font-weight: 600;
            margin-bottom: 10px;
            color: #495057;
        }
        
        .password-display {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: white;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ced4da;
        }
        
        .password-text {
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            letter-spacing: 2px;
            margin: 0;
            padding: 0;
        }
        
        .password-toggle-btn {
            background-color: var(--cobaed-primary);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }
        
        .password-toggle-btn:hover {
            background-color: var(--cobaed-secondary);
        }
        
        .password-toggle-btn:active {
            transform: scale(0.98);
        }
        
        .password-toggle-btn i {
            margin-right: 5px;
        }
        
        .btn-panel {
            background-color: var(--cobaed-accent);
            color: white;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn-panel:hover {
            background-color: #157347;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.2);
        }
        
        .success-icon {
            color: var(--cobaed-accent);
            font-size: 4rem;
            margin-bottom: 1.5rem;
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
        }
    </style>
</head>
<body>
    <input type="hidden" id="dbToken" value="<?php echo $dbToken; ?>">
    <input type="hidden" id="dbNombre" value="<?php echo $dbNombre; ?>">
    <input type="hidden" id="dbPassword" value="<?php echo $dbPassword; ?>">
    <input type="hidden" id="dbImei" value="<?php echo $dbImei; ?>">   
    <input type="hidden" id="imei" value="<?php echo bin2hex(random_bytes(8)); ?>">


    <div class="container">
        <div class="success-container">
            <!-- Header with logo -->
            <div class="header-section">
                <div class="logo-container">
                    <div class="logo">
                        <i class="bi bi-mortarboard-fill logo-icon"></i>
                    </div>
                    <div class="cobaed-name">COBAED</div>
                </div>
                <h1>Módulo de Registro</h1>
                <p class="mb-0">Colegio de Bachilleres del Estado de Durango</p>
            </div>
            
            <!-- Main content -->
            <div class="content-section text-center d-none" id="successContent">
                <!-- Success icon -->
                <div class="success-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                
                <!-- Welcome message -->
                <h2 class="welcome-message text-start">
                    ¡Bienvenido(a) al sistema, <span class="user-name" id="userName">María González Rodríguez</span>!
                </h2>
                
                <p class="lead mb-4">
                    Tu inscripción ha sido completada exitosamente. Ahora tienes acceso a todas las funcionalidades del sistema educativo.
                </p>
                
                <!-- Password section -->
                <div class="password-container">
                    <div class="password-label">
                        <i class="bi bi-key-fill me-2"></i>Esta es tu contraseña de acceso
                    </div>
                    
                    <div class="password-display">
                        <p class="password-text" id="passwordText">••••••••</p>
                        <button class="password-toggle-btn" id="passwordToggle">
                            <i class="bi bi-eye-fill"></i> <span id="toggleText">Mostrar</span>
                        </button>
                    </div>
                    
                    <div class="mt-3 text-muted small">
                        <i class="bi bi-info-circle me-1"></i> Mantén presionado el botón para ver tu contraseña
                    </div>
                </div>
                
                <!-- Instructions -->
                <div class="alert alert-info text-start">
                    <h5><i class="bi bi-lightbulb me-2"></i>Instrucciones importantes:</h5>
                    <ul class="mb-0">
                        <li>Guarda tu contraseña en un lugar seguro.</li>
                        <li>Podrás cambiar tu contraseña una vez que ingreses al panel.</li>
                        <li>Si tienes problemas para acceder, contacta al soporte técnico.</li>
                    </ul>
                </div>
                
                <!-- Go to panel button -->
                <button class="btn btn-panel" id="goToPanel" >
                    <i class="bi bi-door-open-fill me-2"></i> Ir al Panel de Control
                </button>
            </div>


            <div class="content-section text-center d-none " id="errorContent">
    <!-- Error icon -->
    <div class="error-icon">
        <i class="bi bi-exclamation-triangle-fill"></i>
    </div>
    
    <!-- Error message -->
    <h2 class="error-message text-center">
        Hubo un problema con tu registro
    </h2>
    
    <p class="lead mb-4">
        Lo sentimos, pero hemos encontrado un problema al procesar tu solicitud de registro.
    </p>
    
    <!-- Error details -->
    <div class="error-details alert alert-danger text-start">
        <h5><i class="bi bi-x-circle-fill me-2"></i>Detalles del error:</h5>

        <ul class="mb-0" id="errorList" class="d-none">
            <li>Error en la validación de datos </li>
            <li>QR incompleta o ilegible</li>
            <li>No hay conexion con el servidor</li>
        </ul>
        <div id="error2" class="d-none">
            <strong>Qr registrado por :</strong> <span class="badge bg-dark" id="dOtro">FOLIO-20240627-XYZ123</span>
        </div>
            
        <div class="mt-3">
            <strong>Código de error:</strong> <span class="badge bg-dark">ERR-CONAED-045</span>
        </div>
    </div>
    
    <!-- Instructions for resolution -->
    <div class="alert alert-warning text-start">
        <h5><i class="bi bi-exclamation-octagon me-2"></i>¿Qué puedes hacer ahora?</h5>
        <ul class="mb-0">
            <li>Comunícate con el departamento de coordinación al (618) 123-4567</li>
            <li>Envía un correo a <strong>coordinacion@<?= htmlspecialchars(preg_replace("/^www\./", "", $_SERVER["HTTP_HOST"] ?? "cobaedlomas.com")) ?></strong> con tu folio</li>
            <li>Acude a la dirección de servicios escolares para corregir la situación</li>
            <li>Reintenta tu inscripción mas tarde</li>
        </ul>
    </div>
    
    <!-- Action buttons for error scenario -->
    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <button class="btn btn-warning w-100" id="retryButton">
                <i class="bi bi-arrow-clockwise me-2"></i> Reintentar Inscripción
            </button>
        </div>
        <div class="col-md-6 mb-3">
            <button class="btn btn-secondary w-100" id="contactButton">
                <i class="bi bi-headset me-2"></i> Contactar Soporte
            </button>
        </div>
    </div>
    
    
</div>

<style>
    /* Additional styles for error version */
    .error-icon {
        color: #dc3545;
        font-size: 4rem;
        margin-bottom: 1.5rem;
    }
    
    .error-message {
        color: #dc3545;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }
    
    .error-details {
        background-color: #f8d7da;
        border-color: #f5c2c7;
        border-radius: 10px;
        padding: 1.5rem;
        margin: 2rem 0;
    }
    
    .btn-warning {
        background-color: #ffc107;
        color: #212529;
        padding: 12px 20px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .btn-warning:hover {
        background-color: #ffca2c;
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
        padding: 12px 20px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .btn-secondary:hover {
        background-color: #5c636a;
        transform: translateY(-2px);
    }
</style>

            
            <!-- Footer -->
            <div class="footer-section">
                <p class="mb-0">
                    © 2023 COBAED - Colegio de Bachilleres del Estado de Durango. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(async function() {

            if(!localStorage["cobaed_imei"]) 
                localStorage["cobaed_imei"] = $("#imei").val();
            
            imei=localStorage["cobaed_imei"];
            dbImei=$("#dbImei").val();

            yo=localStorage["cobaed_tTkn"]||"";
            ttkn=localStorage["cobaed_tTkn"]||""
            qr=$("#dbToken").val();

            if(yo==""&&dbImei=="No registrado"){
                localStorage["cobaed_tTkn"]=$("#dbToken").val();
                localStorage["cobaed_tNombre"]=$("#dbNombre").val();
                $.post("enrolltchr.php",{imei:localStorage["cobaed_imei"],ttkn:qr},function(data){
                    console.log(data);
                });
                caso="nuevo";
                $("#successContent").removeClass("d-none");
            }

            if(yo==$("#dbToken").val()){
                caso="reingreso";
                $("#successContent").removeClass("d-none");
            }

            if(yo!=""&&$("#dbToken").val()!=yo&&$("#dbToken").val()!="error"){
                caso="otro";
                $("#error2, #errorContent").removeClass("d-none");
                $("#dOtro").text($("#dbNombre").val());
                caso="otro"

            }
            
            if($("#dbToken").val()=="error") {
                $("#errorContent").removeClass("d-none");return;       
               $("#errorList").removeClass("d-none");
               caso="no qr"; 
            }
            
             
             
            // Elements
            var $passwordToggle = $('#passwordToggle');
            var $passwordText = $('#passwordText');
            var $toggleText = $('#toggleText');
            var $goToPanelBtn = $('#goToPanel');


            // Actual password (server should provide this)
            var actualPassword = $("#dbPassword").val() ; // Placeholder password

            // Show on press, hide on release/leave
            $passwordToggle.on('mousedown touchstart', function(e) {
                e.preventDefault && e.preventDefault();
                $passwordText.text(actualPassword);
                $toggleText.text('Mostrando');
                $(this).addClass('active');
            });

            $passwordToggle.on('mouseup mouseleave touchend', function(e) {
                e.preventDefault && e.preventDefault();
                $passwordText.text('••••••••');
                $toggleText.text('Mostrar');
                $(this).removeClass('active');
            });

            // Go to panel button
            $goToPanelBtn.on('click', function() {
                var $btn = $(this);
                var originalHtml = $btn.html();
                $btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Redirigiendo...');
                $btn.prop('disabled', true);

                setTimeout(function() {
                    location.href = "newsboard.php";
                }, 2000);
            });

            // Set displayed user name from hidden input
            $('#userName').text($('#dbNombre').val() || '');
        });
      
    </script>
</body>
</html>