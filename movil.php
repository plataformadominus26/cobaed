<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require_once "rbh/conexion1.php";
    require_once "rbh.php";
    date_default_timezone_set('America/Mexico_City');

    if (isset($_REQUEST["u"])) {
        $sql = "SELECT * FROM usuarios WHERE email='" . $_REQUEST["u"] . "' AND pwd='" . $_REQUEST["p"] . "'";
        $result = $db->query($sql)->fetch_assoc();
        $r = [];
        if ($result) {
            $r["ok"] = true;
            $r["token"] = $result["token"];
            $r["nombre"] = $result["nombre"];
        } else {
            $r["ok"] = false;
        }
        header('Content-Type: application/json');
        echo json_encode($r);
        die();
    }

    if(isset($_REQUEST["passQr"])) {
        $qr = trim($_REQUEST["passQr"]);
        
        $prefix="https://cobaed.robertoblancarte.com/newsboard.php?area=";
                  
        $r = [];
        $r["ok"] = false;
        $r["area"] = "";
        $r["dia"] = 0;
        $r["maestro"] = "";
        $r["sql"] = "";
        $r["motivo"] =1;
        $r["msg"] ="QR no válido";
        $r["prefix"] = $prefix;
        $r["strpos"] = strpos($qr,$prefix);
        if (strpos($qr,$prefix)  >-1) {
            $r["motivo"] = 2;
            $r["msg"]="QR no es de esta institución";
            $qr = explode("area=",$qr)[1];
            $sql="SELECT a.* FROM areas a, usuarios b";
            $sql.=" WHERE a.token='" . $db->real_escape_string($qr) . "' AND a.book_id=b.book_id";
            $sql.=" and b.token='" . $db->real_escape_string($_REQUEST["tkn"]) . "'";
            $r["sql1"] = $sql;
            $r["sql"] = $sql;
            if($row = $db->query($sql)->fetch_assoc()) {
               $r["motivo"] = 3;
               $r["msg"] = "No hay clase reservada para esta area en esta hora.";

               $r["area"] = $row["nombre"];
               $r["area_id"] = $row["area_id"];
               $area_id = $row["area_id"];
               $dia = date('N'); // 1 (Monday) through 7 (Sunday)
            /*   if ($dia == 7) $dia = 1; // Set Sunday to 1
               else $dia++; // Shift other days up by 1 (Monday=2, ..., Saturday=7)*/
               $r["dia"] = $dia;
               $hora = date('H:i:s');
               $book_id = $row["book_id"];
               $sql = "SELECT a.*, b.nombre AS mtro, b.usuario_id as maestro_id FROM horarios a, usuarios b ";
               $sql.=" WHERE a.area_id=$area_id AND a.dia=$dia ";
                $sql.=" AND '$hora' BETWEEN a.hora AND a.hora1 and a.usuario_id=b.usuario_id";
                dump($sql);
                $r["sql"] = $sql;
               if($row = $db->query($sql)->fetch_assoc()) {
                   $r["motivo"] = 4;
                   $r["maestro"] = $row["mtro"];
                   $r["maestro_id"] = $row["maestro_id"];
                   $r["checkin"] = $row["hora"];
                   $r["checkout"] = $row["hora1"];
                   $sql="select * from attendance where  maestro_id=".$row["usuario_id"]." and date(fecha)='" . date('Y-m-d') . "'";
                   $sql.="and '$hora' between checkin and checkout";
                   dump($sql,"dump.txt","a");
                   $r["sql2"] = $sql;
                   if($row = $db->query($sql)->fetch_assoc()) {
                       $r["msg"] = "Asistencia ya registrada";
                   } else {
                        $r["ok"] = true;
                        $r["msg"] = "Introduzca el estado de asistencia";
                        $r["motivo"] = 0;
                   }
               }
                 
            } 
        } 
        header('Content-Type: application/json');
        echo json_encode($r);
        die();
    }

    if(isset($_REQUEST["action"])){
        $token = bin2hex(random_bytes(16));
        $sql="select * from usuarios where token='" . $db->real_escape_string($_REQUEST["tkn"]) . "'";
        $user=$db->query($sql)->fetch_array();
        $user_id=$user["usuario_id"];
        $sql="insert into attendance set token='".$token."', maestro_id='".$_REQUEST["maestro"]."',fecha=now(),";
        $sql.=" area_id='".$_REQUEST["area_id"]."',usuario_id='".$user_id."',";
        $sql.=" checkin='".$_REQUEST["inicio"]."', checkout='".$_REQUEST["fin"]."',";
        $sql.=" estado='".$_REQUEST["action"]."', rems='".$db->real_escape_string($_REQUEST["comments"])."'";
        print($sql);
        dump($sql,"dato.txt","a");
        $db->query($sql);
        die();
    }
    
    if(isset($_REQUEST["historial"])){
        $sql="select a.*, b.nombre as mtro, c.nombre as area, d.nombre as usuario from attendance a, usuarios b, areas c, usuarios d 
            where a.maestro_id=b.usuario_id and a.area_id=c.area_id 
            and a.usuario_id=d.usuario_id
            and d.token='" . $db->real_escape_string($_REQUEST["historial"]) . "'
            order by a.fecha desc limit 100";
            
        $result = $db->query($sql);
        header('Content-Type: text/html; charset=utf-8');
        echo '<div class="row row-cols-1 row-cols-md-2 g-4">';
        while ($row = $result->fetch_assoc()) {
            $estado = '';
            switch ($row['estado']) {
                case '1': $estado = '<span class="badge bg-danger">Falta</span>'; break;
                case '2': $estado = '<span class="badge bg-warning text-dark">Retraso</span>'; break;
                case '3': $estado = '<span class="badge bg-success">Asistencia</span>'; break;
                default: $estado = '<span class="badge bg-secondary">Desconocido</span>';
            }
            echo '<div class="col">';
            echo '<div class="card shadow-sm mb-0">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title mb-2"><i class="bi bi-person-badge me-2"></i>' . htmlspecialchars($row['mtro']) . '</h5>';
            echo '<p class="mb-1"><i class="bi bi-geo-alt me-2"></i><strong>Área:</strong> ' . htmlspecialchars($row['area']) . '</p>';
            echo '<p class="mb-1"><i class="bi bi-clock me-2"></i><strong>Horario:</strong> ' . htmlspecialchars($row['checkin']) . ' - ' . htmlspecialchars($row['checkout']) . '</p>';
            echo '<p class="mb-1"><i class="bi bi-info-circle me-2"></i><strong>Estado:</strong> ' . $estado . '</p>';
            echo '<p class="mb-1"><i class="bi bi-chat-left-text me-2"></i><strong>Comentarios:</strong> ' . htmlspecialchars($row['rems']) . '</p>';
            echo '<p class="mb-1"><i class="bi bi-calendar-event me-2"></i><strong>Fecha:</strong> ' . htmlspecialchars($row['fecha']) . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        die();
    }

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>COBAED</title>
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/bootstrap-icons.min.css" rel="stylesheet">
        <link href="qr.css?x=1.02" rel="stylesheet">
        <link href="movil.css?x=1.08" rel="stylesheet">
    </head>
    <body>
         <i id="wifiStatus" class="bi bi-wifi text-secondary fs-3 align-middle ms-2 bg-white"></i>
        <script>
            // Function to set wifi icon color based on online status
            function setWifiStatus(isOnline) {
                const wifiIcon = document.getElementById('wifiStatus');
                if (isOnline) {
                    wifiIcon.classList.remove('text-danger');
                    wifiIcon.classList.add('text-success');
                } else {
                    wifiIcon.classList.remove('text-success');
                    wifiIcon.classList.add('text-danger');
                }
            }

            // Initial status
            setWifiStatus(navigator.onLine);

            // Listen for online/offline events
            window.addEventListener('online', () => setWifiStatus(true));
            window.addEventListener('offline', () => setWifiStatus(false));
        </script>
        <input type="hidden" id="hNombre" value="">
        <input type="hidden" id="hQr" value="">
        <header class="pt-2 pb-3 px-3 red-ged text-white sticky-top">
            <div class="position-relative">
                <button class="btn btn-link text-decoration-none back-btn d-none" id="back-btn">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <h3 class="text-end m-100 pe-2 mb-2">Control de Asistencia </h3>
                <p class="mb-0">M.E. Everardo Cerecero Martínez</p>
                <img class="img img-fluid" src="assets/img/cobaed2010.png" alt="Logo COBAED,." id="iLogo">
            </div>
        </header>

        <div class="container position-relative" style="min-height: calc(100vh - 150px);">
            <ul class="nav nav-tabs justify-content-center mt-3 d-none" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="list-tab" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab" aria-controls="list" aria-selected="true">
                        <i class="bi bi-people me-2"></i>Beneficiarios
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="qr-tab" data-bs-toggle="tab" data-bs-target="#qr" type="button" role="tab" aria-controls="qr" aria-selected="false">
                        <i class="bi bi-qr-code me-2"></i>Lector QR
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="false">
                        <i class="bi bi-person me-2"></i>Login
                    </button>
                </li>
            </ul>

            <div class="tab-content w-100 px-3">
                <!-- Lista de Beneficiarios -->
                <div class="tab-pane fade show active" id="list" role="tabpanel" aria-labelledby="list-tab">
                    <div class="instructions">
                        <div class="input-group mb-3" style="max-width: 400px;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Buscar beneficiario...">
                        </div>
                    </div>
                    <div class="card-container px-5 w-100" id="historyContent"></div>
                </div>

                <!-- Lector QR -->
                <div class="tab-pane fade" id="qr" role="tabpanel" aria-labelledby="qr-tab">
                    <div class="scanner-container position-relative" style="border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);">
                        <video id="qrScanner" style="width: 100%; display: block; max-height: 40vh;"></video>
                        <div class="scanner-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;">
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 70%; height: 70%; border: 3px solid rgba(0, 255, 200, 0.7); border-radius: 12px; box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.5);"></div>
                            <div class="scanner-animation" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 65%; height: 4px; background: linear-gradient(90deg, transparent, rgba(0, 255, 200, 0.8), transparent); animation: scan 2s infinite linear;"></div>
                        </div>
                    </div>
                <div id="scanxResult" class="mt-2 text-center"> 
                    <div class="card mx-auto shadow" style="max-width: 400px;">
                        <div class="card-body">
                            <h5 class="card-title mb-2">
                                <i class="bi bi-person-badge me-2"></i>
                                <span id="teacherName">esperando Registro...</span>
                            </h5>
                            <p class="mb-1">
                                <i class="bi bi-geo-alt me-2"></i>
                                <span id="classArea">Area</span>
                            </p>
                            <p class="mb-1">
                                <i class="bi bi-clock me-2"></i>
                                <span id="startTime">Hora inicio</span> - <span id="endTime">Hora fin</span>
                            </p>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="userComments" placeholder="Escribe tus comentarios...">
                                <label for="userComments">Comentarios</label>
                            </div>
                            <div class="d-flex justify-content-between" id="actionButtons">
                                <button type="button" class="btn btn-danger flex-fill me-2" id="btnFalta" data-value="1">
                                    <i class="bi bi-x-circle me-1"></i> Falta
                                </button>
                                <button type="button" class="btn btn-warning flex-fill me-2" id="btnRetraso" data-value="2">
                                    <i class="bi bi-clock-history me-1"></i> Retraso
                                </button>
                                <button type="button" class="btn btn-success flex-fill" id="btnAsistencia" data-value="3">
                                    <i class="bi bi-check-circle me-1"></i> Asistencia
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <!-- Login Tab -->
                <div class="tab-pane fade" id="login" role="tabpanel" aria-labelledby="login-tab">
                    <div class="d-flex justify-content-center align-items-center" style="min-height: 300px;">
                        <div class="card shadow" style="width: 350px;">
                            <div class="card-body">
                                <h4 class="card-title text-center mb-4">Iniciar sesión</h4>
                                <form>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Usuario</label>
                                        <input type="text" class="form-control" id="username" placeholder="Ingrese su usuario">
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" id="password" placeholder="Ingrese su contraseña">
                                    </div>
                                    <button type="button" class="btn btn-primary w-100" id="btLogin">Entrar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-nav text-white">
            <div class="d-flex justify-content-around">
                <button class="btn btn-footer bg-white text-dark" data-tab="list">
                    <i class="bi bi-people"></i>
                </button>
                <button class="btn btn-footer bg-white text-dark" data-tab="qr">
                    <i class="bi bi-qr-code"></i>
                </button>
                <button class="btn btn-footer bg-white text-dark" id="exit-btn" data-tab="login">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </div>
        </div>

        

        <script src="assets/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/qr-scanner@1.4.1/qr-scanner.umd.min.js"></script>
        <script src="movil.js?v=1.27"></script>
    </body>
    </html>
