<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    include_once "rbh/conexion1.php";
    include_once "rbh.php";
    $res=[];
    $res["ok"] = false;
    $res["qrsrc"] = "assets/img/sadqr.png";
    $res["fecha"] = "----";


    if(!isset($_REQUEST["tkn"]))    
        $res["mensaje"] = "Pase mal formado";
    else{
        $tkn = $_REQUEST["tkn"];
        $sql = "SELECT a.fecha, b.nombre guest, concat(c.nombre, ' ', c.paterno) socio, usado, 
                DATEDIFF(NOW(), a.fecha) > 0 pastdue, c.socio_id , b.invitado_id
                ,b.nombre, a.token tkn
                FROM pases a, invitados b, socios c 
                WHERE a.socio_id=c.socio_id 
                AND a.token='" . $tkn . "' 
                AND a.invitado_id=b.invitado_id 
                AND a.usado=0";
        
        $row = $db->query($sql)->fetch_assoc();
        if(!$row) {
            $res["mensaje"] = "Pase no encontrado";
        }
        else if($row["usado"]) {
            $res["fecha"] = date("d/m/Y", strtotime($row["fecha"]));
            $res["mensaje"] = "Pase ya ha sido usado";
        }
        else if($row["pastdue"]) {
            $res["fecha"] = date("d/m/Y", strtotime($row["fecha"]));
            $res["mensaje"] = " este pase ya no es valido";
        } else {
            $res["fecha"] = date("d/m/Y", strtotime($row["fecha"]));
            $res["mensaje"] = $row["socio"];
            $res["guest"] = $row["guest"];
            $res["qrsrc"] = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=https://campestredurango.com/?pase=" . $tkn;
            $res["pase"] = $row;
            $res["pase"]["fecha"] = date("d/m/Y", strtotime($row["fecha"]));
            $res["pase"]["nombre"] = $row["nombre"];
            $res["pase"]["invitado_id"] = $row["invitado_id"];
            $res["pase"]["socio_id"] = $row["socio_id"];
    }
  }

    
    if(isset($_REQUEST["passQr"])){
        $qr=$_REQUEST["passQr"];
        if(strpos($qr, "https://campestredurango.com/?acceso=") === 0){
            $token = explode("https://campestredurango.com/?acceso=", $qr)[1];
            $sql="select * from qrcodes where token='" . $db->real_escape_string($token) . "' and activo=1";
            if($row=$db->query($sql)->fetch_assoc()){
                $token= $_REQUEST["tkn"];
                $sql = "SELECT a.fecha, b.nombre guest, c.nombre socio, usado, 
                        DATEDIFF(CURDATE(), a.fecha) <> 0 pastdue, c.socio_id , b.invitado_id
                        ,b.nombre, a.token tkn
                        FROM pases a, invitados b, socios c 
                        WHERE a.socio_id=c.socio_id 
                        AND a.token='" . $token . "' 
                        AND a.invitado_id=b.invitado_id 
                        AND a.usado=0";
                $row = $db->query($sql)->fetch_assoc();
                $r["sql"] = $sql;
                if(!$row) {
                    $r["success"] = false;
                    $r["message"] = "Pase no encontrado";
                }
                else if($row["usado"]) {
                    $r["success"] = false;
                    $r["message"] = "Pase ya ha sido usado";
                }
                else if($row["pastdue"]) {
                    $r["success"] = false;
                    $r["message"] = " este pase no es valido el dia de hoy";
                } else {
                    $r["success"] = true;
                    $r["message"] = "Pase válido para acceso";
                    $r["pase"] = $row;
                    $r["pase"]["fecha"] = date("d/m/Y", strtotime($row["fecha"]));
                    $r["pase"]["nombre"] = $row["nombre"];
                    $r["pase"]["invitado_id"] = $row["invitado_id"];
                    $r["pase"]["socio_id"] = $row["socio_id"];
                    $r["pase"]["usado"] = $row["usado"];
                    $r["pase"]["tkn"] = $row["tkn"];
                    $r["pase"]["fecha"] = date("d/m/Y", strtotime($row["fecha"]));
                    // Mark as used
                    $sql = "UPDATE pases set usado=1, fecha_uso=NOW() WHERE token='" . $db->real_escape_string($token) . "'";
                    dump($sql);
                    $db->query($sql);
                }
            } 
            else {
                $r["qr_valid"] = false;
                $r["qr_message"] = "QR inválido.";
        }
        } else {
            $r["qr_valid"] = false;
            $r["qr_message"] = "QR inválido..";
        }
        header('Content-Type: application/json');
        echo json_encode($r);
        die();
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pase Club Campestre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="pase.css?v=1.03">
</head>
<body>
    <input type="hidden" id="hTkn" value="<?php echo $_REQUEST["tkn"] ?? ""; ?>">
    <div class="pass-container">
        <div class="pass-header">
            <div class="logo-container">
                <!-- Replace with your actual logo -->
                <img src="assets/img/ccd2025.png" alt="Golf Club Logo" class="logo">
                 
            </div>
           
            <div class="pass-id">
                <div class="pass-id-label"><?php echo $res["fecha"] ?? "Valido : Pase valido"; ?></div>
                <div class="pass-id-value"><?php echo $res["mensaje"] ; ?></div>
            </div>
        </div>
        
        <div class="pass-content">
            <div class="qr-section">
                <div class="qr-code">
                    <!-- Replace with your actual QR code image -->
                    <img src="<?php echo $res["qrsrc"]; ?>" alt="QR Code" class="qr-image">
                </div>
               
            </div>
            
            <div class="photo-section">
                <div class="photo-placeholder">
                    <!-- Replace with actual photo -->
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-1.2.1&auto=format&fit=crop&w=634&q=80" alt="Pass Holder Photo">
                </div>
                <div class="photo-label"><?php echo $res["guest"] ; ?></div>
                
                 
            </div>
        </div>
        


        
        <div class="pass-footer">
            <div class="row">
                <div class="col-6 d-grid">
                    <button type="button" id="btCancelPass" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#cancelPassModal">
                        <i class="bi bi-x-circle" style="font-size:1.2rem;margin-right:8px;"></i> Cancelar Pase
                    </button>
                </div>
                <div class="col-6 d-grid">
                    <button type="button" id="btScanQR" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
                        <i class="bi bi-qr-code-scan" style="font-size:1.2rem;margin-right:8px;"></i> Escanear QR
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border: none; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);">
            <div class="modal-header border-0" style="background: rgba(0, 0, 0, 0.2);">
                <h5 class="modal-title text-white" id="qrScannerModalLabel" style="font-weight: 600;">
                    <i class="bi bi-qr-code-scan me-2"></i> Escanear Código QR
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <div class="scanner-container position-relative" style="border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);">
                    <video id="qrScanner" style="width: 100%; display: block; max-height: 40vh;"></video>
                    <div class="scanner-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;">
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 70%; height: 70%; border: 3px solid rgba(0, 255, 200, 0.7); border-radius: 12px; box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.5);"></div>
                        <div class="scanner-animation" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 65%; height: 4px; background: linear-gradient(90deg, transparent, rgba(0, 255, 200, 0.8), transparent); animation: scan 2s infinite linear;"></div>
                    </div>
                </div>
                <div id="qr-result" class="mt-4 text-center text-white-50" style="font-size: 1.1rem;"></div>
            </div>
            <div class="modal-footer border-0" style="background: rgba(0, 0, 0, 0.2);">
                <button type="button" class="btn btn-outline-light me-2" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Cerrar
                </button>
                <button type="button" class="btn btn-primary" id="toggle-flash" style="background: linear-gradient(135deg, #00b4db, #0083b0); border: none;">
                    <i class="bi bi-lightbulb me-1"></i> Flash
                </button>
            </div>
        </div>
    </div>
</div>

  
<!-- Disclaimer Modal -->
<div class="modal fade" id="disclaimerModal" tabindex="-1" aria-labelledby="disclaimerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disclaimerModalLabel">
                    <i class="bi bi-info-circle me-2"></i> Aviso Importante
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>
                    Este pase es personal e intransferible. El uso indebido puede resultar en la cancelación del acceso. Por favor, respete las normas del club y presente este pase al ingresar.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entiendo</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var disclaimerModal = new bootstrap.Modal(document.getElementById('disclaimerModal'));
        disclaimerModal.show();
    });
</script>
    <script src="https://www.gstatic.com/firebasejs/10.0.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.0.0/firebase-auth-compat.js"></script>  
    <script src="https://www.gstatic.com/firebasejs/10.0.0/firebase-database-compat.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qr-scanner@1.4.1/qr-scanner.umd.min.js"></script>
    <script src="pase.js?version=1.05"></script>
    <script src="app/admin/assets/js/xe3_java.js"></script>

</body>
</html>