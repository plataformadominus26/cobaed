<?php
// Mostrar errores de PHP para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

 
include_once("rbh/conexion1.php");
require_once __DIR__ . "/config.php";

if(isset($_REQUEST["email"]) && isset($_REQUEST["password"])) {
     
    $email = $db->real_escape_string($_REQUEST["email"]); // Basic sanitization
    $password = $_REQUEST["password"];
    $r = [];

    // Prepared statement approach (better)
    $sql="SELECT * FROM usuarios a ";
    $sql.=" where email = '$email' and pwd='$password' and  activo = 1 LIMIT 1" ;

    
    if($row = $db->query($sql)->fetch_assoc()) {
            $r["ok"] = true;
            $r["tkn"] = $row["token"];
            $r["nombre"] = $row["nombre"];
            $r["mensaje"] = "Bienvenido " . $row["nombre"];
             
         
        } else {
            $r["ok"] = false;
            $r["mensaje"] = "Credencialesx incorrectas". $sql;
        }

    header('Content-Type: application/json');
    echo json_encode($r);
    die();
}
 

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= plantel_nombre() ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css?v=1.01">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo floating-logo">
                         <?= mb_strtoupper(plantel_nombre()) ?>
                    </div>
                    <p>Sistema de gestión <?= plantel_nombre() ?></p>
                </div>
                
                <div class="login-body">
                    
                        <div class="form-floating mb-3 position-relative">
                            <input type="email" class="form-control animated-input" id="email" placeholder="Correo electrónico">
                            <label for="email">Correo electrónico</label>
                            <i class="fas fa-envelope password-toggle"></i>
                        </div>
                        
                        <div class="form-floating mb-4 position-relative">
                            <input type="password" class="form-control animated-input" id="password" placeholder="Contraseña">
                            <label for="password">Contraseña</label>
                            <i class="fas fa-eye password-toggle toggle-password"></i>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label" for="remember">Recordarme</label>
                            </div>
                            <a href="#" class="text-decoration-none" style="color: var(--accent-color);">¿Olvidaste tu contraseña?</a>
                        </div>
                        
                        <button type="button" class="btn btn-login w-100 text-white mb-3" id="loginButton">
                            <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                        </button>
                        
                        <div class="divider">
                            <span class="divider-text">o continuar con</span>
                        </div>
                        
                        <a href="#" class="btn btn-google w-100 mb-3">
                            <img src="assets/img/login.png" alt="Google">
                            Google
                        </a>
                        
                        <div class="text-center mt-4">
                            <p class="mb-0">¿No tienes una cuenta? <a href="#" class="text-decoration-none" style="color: var(--accent-color);">Regístrate</a></p>
                        </div>
                    
                </div>
                
                <div class="login-footer">
                    <p class="mb-0 small text-muted">© <?= date("Y") ?> <?= plantel_nombre() ?>. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Google login handler (simulated)
        document.querySelector('.btn-google').addEventListener('click', function(e) {
            e.preventDefault();
            alert('Redirigiendo a Google para autenticación...');
            // Aquí iría la implementación real de Google Sign-In
        });
        
        // Login button handler
        document.getElementById('loginButton').addEventListener('click', function() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (email && password) { 
                const formData = new FormData();
                formData.append('email', email);
                formData.append('password', password);
                fetch('login.php', {
                    method: 'POST',
                    body: formData
                })
                
                .then(response => response.json())
                .then(data => {
                   if(data.ok) {
                        alert(data.mensaje);
                        localStorage.setItem('cobaed_token', data.tkn);
                        localStorage.setItem('cobaed_nombre', data.nombre);  
                        
                        location.href = 'empleados'; // Redirigir al dashboard o página principal
                        // Aquí podrías redirigir al usuario a otra página o guardar el token en localStorage
                    } else {
                        alert(data.mensaje);
                    }
                })
                .catch(error => {
                    alert('Error al iniciar sesión.');
                });
            } else {
                alert('Por favor, completa todos los campos.');
            }
        });
        
    </script>
</body>
</html>
