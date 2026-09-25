<?php
    date_default_timezone_set('America/Monterrey');
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
            include_once("../rbh/conexion1.php");
           include_once("../rbh.php");


    if(isset($_REQUEST["cargaAsistencia"])){
        $sql="select * from usuarios a, attendance b 
            where a.usuario_id = b.usuario_id  and a.token = '".$_REQUEST["cargaAsistencia"]."' 
            order by fecha desc, checkin desc";
        $result = $db->query($sql);
        $response = "";
        $clase="hoy";
        $clase1="";
        $clase2="";
        while($row = $result->fetch_assoc()){
            if($clase)
                if($row["checkout"]== null) 
                     $clase1= "ahora" ;
                else 
                    $clase2= "ahora";
            $response .= "<tr class='$clase xTr'>";
             $response .= "<td>".$row['fecha']."</td>";
            $response .= "<td class='$clase1 xTd'>".$row['checkin']."</td>";
            $response .= "<td class='$clase2 xTd'>".($row['checkout'] ? $row['checkout'] : 'Pendiente')."</td>";
            $response .= "</tr>";
            $clase="";
            $clase1="";
            $clase2="";
        }
        print($response);
        die();
    }


    if(isset($_REQUEST["checar"])){
        $qr = explode(".",$_REQUEST["qr"]);
        $estacion = $qr[1];
        $qr = $qr[0];
        $checar = $_REQUEST["checar"];
        $sql = "SELECT * FROM usuarios WHERE token='$checar'";
     
        if($row = $db->query($sql)->fetch_assoc()){
            $nombre= $row['nombre'];
            $uid = $row['usuario_id'];
            $sql="select * from qrs where (qr='$qr') and (empresa_id = '".$row['empresa_id']."') ";
             if($row=$db->query($sql)->fetch_assoc()){
                $empresa_id = $row['empresa_id'];
                $sql= "select * from attendance where usuario_id = '".$uid."' and fecha = curdate() and checkout is null ";
                if($row=$db->query($sql)->fetch_assoc())
                    $sql="update attendance set checkout = curtime() where attendance_id=".$row['attendance_id'];
                else{
                    $sql = "INSERT INTO attendance  set ";
                    $sql.=" usuario_id='".$uid."', fecha=CURDATE(), checkin=CURTIME()";
                }
                header('Content-Type: application/json');
               try {   
                    $result = @$db->query($sql); // @ suppresses error output
                    if ($result) {
                        $response = [
                            'status' => 'success', 
                            'message' => 'Registro exitoso', 
                            'nombre' => $nombre ,
                            'estacion' => $estacion,
                        ];
                    } 
                    else {
                        $error = $db->error ? $db->error : 'Error desconocido en la base de datos';
                        throw new Exception('Error al registrar asistencia: ' . $error);
                    }
                } 
                catch (Exception $e) {
                    $response = [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'debug' =>  ['sql' => $sql]     ];
                }        
            }
        }
        else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Token inválido']);
        }
            echo json_encode($response);

        die();
    }

    if(isset($_REQUEST['fetch_new'])) {
        $sql="select * from usuarios where token = '".$_REQUEST["fetch_new"]."'";
        dump($sql,"qr.txt");

        $row= $db->query($sql)->fetch_assoc();
        $token= bin2hex(random_bytes(8));

        $qr_content = "https://sinpendientes.com/cliente/index.php?token=" .$token.".".$_REQUEST["estacion"];;
        $sql="select * from empresas where empresa_id= '".$row["empresa_id"]."'";
                dump($sql,"qr.txt","a");

        $row= $db->query($sql)->fetch_assoc();
        $sql="delete from qrs where estacion='".$_REQUEST["estacion"]."'";
       $db->query($sql);

        $sql= "INSERT INTO qrs(qr, empresa_id,fecha,estacion) VALUES ('$token','".$row["empresa_id"]."',now(),'".$_REQUEST["estacion"]."')";
        dump($sql,"qr.txt","a");
        $db->query($sql);
        echo   $qr_content ;
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de asistencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="checador.css?v=1.04" rel="stylesheet">
</head>
<body>
    <input type="hidden" id="hModulo" value="checador">
    <input type="hidden" id="hQr">
    <input type="hidden" id="hEstacion" value="<?php echo bin2hex(random_bytes(8)); ?>">
    <!-- Header -->
    <header >
           Merendero       <i class="bi bi-list fs-1" id="sidebarToggle" ></i>
    </header>
<!-- Sidebar Menu -->
<nav class="sidebar bg-dark text-white position-fixed vh-100" id="sidebar" style="width: 240px; left: -240px; top: 0; z-index: 1040; transition: left 0.3s;">
    <div class="p-3">
        <h5 class="text-center">Menú</h5>
        <hr>
        <ul class="nav flex-column">
            <!--
            <li class="nav-item">
                <a class="nav-link text-white active" href="#" data-section="dashboard"><i class="bi bi-house-door me-2"></i> Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#" data-section="tasks"><i class="bi bi-list-task me-2"></i> Mis Tareas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#" data-section="attendance"><i class="bi bi-clock-history me-2"></i> Asistencia</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#" data-section="chat"><i class="bi bi-chat-dots me-2"></i> Chat</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#" data-section="training"><i class="bi bi-mortarboard me-2"></i> Capacitación</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#" data-section="board"><i class="bi bi-megaphone me-2"></i> Tablero</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="#" data-section="performance"><i class="bi bi-graph-up-arrow me-2"></i> Mi Desempeño</a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link text-white" href="#"><i class="bi bi-gear me-2"></i> Ajustes</a>
            </li>-->
            <li class="nav-item">
                <a class="nav-link text-white" href="login.php?esChecador=1"><i class="bi bi-box-arrow-right me-2"></i> Salir</a>
            </li>
        </ul>
    </div>
</nav>
 
 
    <!-- Main Content -->
    
    <div class="qr-container text-center">
        <div class="lcd-clock mb-3 p-2 bg-dark  rounded"
            style="font-family: 'LCD', monospace; font-size: 1.5rem; letter-spacing: 2px;">
            <div id="current-time">
                <span class="lcd-digit">-</span><span class="lcd-digit">-</span>
                <span class="lcd-separator">:</span>
                <span class="lcd-digit">-</span><span class="lcd-digit">-</span>
                <span class="lcd-separator">:</span>
                <span class="lcd-digit">-</span><span class="lcd-digit">-</span>
            </div>
        </div>
          <div id="dNombre" class="alert alert-info h5" role="alert">Escanea este código QR</div> 
        <div class="qr-wrapper">
            <div id="qrCodeDisplay" class="qr-code">
                <!-- QR will be inserted here -->
                <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-center my-3" id="soundSliderContainer">
                <button id="soundToggleBtn" class="btn btn-link p-0 me-2" style="font-size: 1.5rem;">
                    <i class="bi bi-volume-up-fill" id="soundIcon"></i>
                </button>
                <input type="range" min="0" max="1" step="1" value="0" id="soundSlider" style="width: 100px;">
            </div>
             
            <div class="refresh-btn" id="refreshQR" title="Actualizar código QR">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z" />
                    <path fill-rule="evenodd"
                        d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z" />
                </svg>
            </div>
        </div>

        <div class="countdown mb-3">
             
            <div class="recent-checkins mb-4">

                <div class="card border-0 shadow-sm">
                    <ul class="list-group list-group-flush" id="recentCheckinsList">
                        <li class="list-group-item d-flex align-items-center py-2">
                            <div class="fw-medium"></div>
                        </li>
                        <li class="list-group-item d-flex align-items-center py-2">
                            <div class="fw-medium"></div>
                        </li>
                        <li class="list-group-item d-flex align-items-center py-2">
                            <div class="fw-medium"></div>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
        <div class="manual-checkin">
            <button class="btn btn-outline-primary" id="manualCheckinBtn">
                <i class="bi bi-keyboard-fill"></i> Registro Manual
            </button>
            <div id="manualCheckinForm" class="mt-3" style="display: none;">
                <div class="input-group mb-2">
                    <input type="text" class="form-control" placeholder="Ingresa el código de verificación">
                    <button class="btn btn-primary">Enviar</button>
                </div>
                <small class="text-muted">Solicita el código a tu supervisor</small>
            </div>
        </div>
    </div>
    

    <!-- Footer -->
    <footer class="bg-light py-3 mt-5">
        <div class="container text-center">
            <p class="mb-0 text-muted">
                © 2025 el merendero • 
                <span id="last-checkin">Último registro: --:--</span>
            </p>
        </div>
    </footer>
    <!-- Media player for ding sound -->
    <audio id="dingSound" src="../assets/audio/bell2.mp3" preload="auto"></audio>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.0.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.0.0/firebase-auth-compat.js"></script>  
    <script src="https://www.gstatic.com/firebasejs/10.0.0/firebase-database-compat.js"></script>
    <script src="../assets/js/xe3_java.js?v=1.07"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="this.js?v=1.17"></script>
</body>
</html>