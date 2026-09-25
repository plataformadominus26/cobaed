<?php
// Mostrar errores de PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

   include_once("rbh/conexion1.php");
   $enrollAlumno = 0;
   $enrollMaestro = 0;
   if(isset($_REQUEST["opt"])){
        $sql="select * from alumnos where token='".$_REQUEST["enroll"]."' and imei<>''";
        if($db->query($sql)->fetch_assoc())
            die("Este alumno ya está registrado, favor de visitar la direccion para solicitar cambio de equipo celular");
        
        $sql="select * from alumnos where imei='".$_REQUEST["opt"]."'";
        if($alumni=$db->query($sql)->fetch_assoc())
            die("El equipo celular ya está registrado a nombre de ".$alumni["nombre"].", favor de visitar la direccion para solicitar cambio de alumno");
        
        $sql="select * from usuarios where imei='".$_REQUEST["opt"]."'";
        if($alumni=$db->query($sql)->fetch_assoc())
            die("El equipo celular ya está registrado a nombre de ".$alumni["nombre"].", favor de visitar la direccion para solicitar cambio de alumno");
        $sql="update alumnos set imei='".$_REQUEST["opt"]."' where token='".$_REQUEST["enroll"]."'";
        print($sql);
        if($db->query($sql))
            print("Registro de alumno actualizado correctamente");
        else
            print("Error al actualizar el registro del alumno, intente nuevamente");
        die();
   }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COBAED Durango - Tablón de Mensajes</title>
     <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="this.css">
</head>
<body>
    <?php
        // Generar un valor hexadecimal aleatorio de 15 caracteres
        $randHex = bin2hex(random_bytes(8));
        $randHex = substr($randHex, 0, 15);
    ?>
    <input type="hidden" id="hRand15" value="<?php echo $randHex; ?>">
    
    
    <div class="container">
        <header>
            <div class="logo-container">
                <div class="logo">CD</div>
                <div>
                    <h1>COBAED Durango</h1>
                    <div class="subtitle">Colegio de Bachilleres del Estado de Durango</div>
                </div>
            </div>
            <div class="date-display" id="current-date">Cargando fecha...</div>
        </header>
        

        <section class="announcement-section" >
       
           
        </section>
        <section class="announcement-section">
            <div class="section-title">
                <i>📢</i>
                <h2>Avisos Importantes</h2>
            </div>
            
            <div class="news-item urgent">
                <div class="news-title">Regreso a Clases Presenciales</div>
                <div class="news-date">15 de agosto, 2023</div>
                <div class="news-content">
                    A partir del 21 de agosto, todas las actividades académicas regresarán a la modalidad presencial. Se mantendrán los protocolos de salud establecidos.
                </div>
            </div>
            
            <div class="news-item">
                <div class="news-title">Inscripciones Ciclo 2023-2024</div>
                <div class="news-date">1 de agosto, 2023</div>
                <div class="news-content">
                    El periodo de inscripciones para el ciclo escolar 2023-2024 está abierto. Consulta los requisitos en la página oficial.
                </div>
            </div>
        </section>
        
        <section class="announcement-section">
            <div class="section-title">
                <i>📰</i>
                <h2>Noticias Recientes</h2>
            </div>
            
            <div class="news-item">
                <div class="news-title">COBAED Destaca en Olimpiada del Conocimiento</div>
                <div class="news-date">25 de julio, 2023</div>
                <div class="news-content">
                    Estudiantes de COBAED obtuvieron primeros lugares en la Olimpiada Estatal del Conocimiento 2023 en las áreas de Matemáticas y Física.
                </div>
            </div>
            
            <div class="news-item">
                <div class="news-title">Implementación de Nuevo Programa Académico</div>
                <div class="news-date">10 de julio, 2023</div>
                <div class="news-content">
                    COBAED incorporará el programa "Habilidades Digitales" para todos los estudiantes a partir del próximo ciclo escolar.
                </div>
            </div>
            
            <div class="news-item">
                <div class="news-title">Ceremonia de Graduación 2023</div>
                <div class="news-date">30 de junio, 2023</div>
                <div class="news-content">
                    Más de 2,500 estudiantes de COBAED recibieron su certificado de bachillerato en ceremonias realizadas en los 33 planteles.
                </div>
            </div>
        </section>
        
        <section class="announcement-section">
            <div class="section-title">
                <i>🎓</i>
                <h2>Eventos Académicos</h2>
            </div>
            
            <div class="news-item">
                <div class="news-title">Feria de Ciencias 2023</div>
                <div class="news-date">15 de septiembre, 2023</div>
                <div class="news-content">
                    Se llevará a cabo la Feria de Ciencias anual en el auditorio principal. Participarán estudiantes de todos los planteles.
                </div>
            </div>
            
            <div class="news-item">
                <div class="news-title">Concurso de Oratoria</div>
                <div class="news-date">5 de octubre, 2023</div>
                <div class="news-content">
                    Inscripciones abiertas para el XXV Concurso Interno de Oratoria "Palabra Viva".
                </div>
            </div>
        </section>
        
        <div class="quick-links">
            <div class="link-card">
                <div class="link-icon">📚</div>
                <div class="link-title">Plataforma Educativa</div>
            </div>
            <div class="link-card">
                <div class="link-icon">📅</div>
                <div class="link-title">Calendario Escolar</div>
            </div>
            <div class="link-card">
                <div class="link-icon">📋</div>
                <div class="link-title">Boletas de Calificaciones</div>
            </div>
            <div class="link-card">
                <div class="link-icon">📞</div>
                <div class="link-title">Contacto</div>
            </div>
        </div>
        
        <section class="announcement-section highlight">
            <div class="section-title">
                <i>⭐</i>
                <h2>Logros Destacados</h2>
            </div>
            <div class="news-content">
                COBAED Durango ha sido reconocido como una de las instituciones de educación media superior con mayor índice de aceptación en universidades públicas de la región.
            </div>
        </section>
        
        <footer>
            COBAED Durango - Formando líderes para el futuro<br>
            © 2025 Todos los derechos reservados
        </footer>
    </div>
    <?php
        if($enrollAlumno==0)
            echo '<input type="hidden" id="hEnrollAlumno" value="none">';
        if($enrollMaestro==0)
            echo '<input type="hidden" id="hEnrollMaestro" value="none">';
            
    ?>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
   
    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src='this.js?v=1.03'></script>


 
</body>
</html>