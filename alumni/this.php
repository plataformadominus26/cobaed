<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include_once '../rbh/conexion1.php';
    include_once __DIR__ . '/../assets/services/push_service.php';
    $tabla = "alumnos";
    $RTDB_URL = 'https://cobaedlomas-default-rtdb.firebaseio.com';
    $SERVICE_ACCOUNT_PATH = resolveServiceAccountPath();
    if (isset($_FILES)) {
        file_put_contents('files.txt', print_r($_FILES, true));
    }
    if (isset($_REQUEST["eliminar"])) {
        $alumnoToken = $db->real_escape_string($_REQUEST["eliminar"]);
        $rowAl = $db->query("select token, book_id from $tabla where token='".$alumnoToken."'")->fetch_assoc();
        $sql = "update $tabla set activo=3 where token='".$_REQUEST["eliminar"]."'";
        $db->query($sql);
        if ($rowAl) {
            notifyCredencialChangedRtdb($RTDB_URL, $SERVICE_ACCOUNT_PATH, $rowAl["book_id"], $rowAl["token"], "delete");
        }
    }

    if (isset($_REQUEST["previewBatchPush"])) {
        header('Content-Type: application/json');
        $usrToken = $db->real_escape_string((string)$_REQUEST["previewBatchPush"]);
        $usr = $db->query("select * from usuarios where token='".$usrToken."'")->fetch_assoc();
        if (!$usr) {
            echo json_encode(array("ok" => false, "message" => "Usuario no válido"));
            die();
        }

        $scope = " u.book_id='".$db->real_escape_string($usr["book_id"])."' ";
        $scope .= buildAlumniScopeFilters($db, $_REQUEST);

        $sqlCount = "select count(*) as n from alumnos u where $scope";
        $rowCount = $db->query($sqlCount)->fetch_assoc();
        $sqlSample = "select token, nombre, semestre, grupo, turno_id, estado from alumnos u where $scope order by nombre limit 20";
        $resSample = $db->query($sqlSample);
        $sample = array();
        while ($r = $resSample->fetch_assoc()) {
            $sample[] = $r;
        }

        echo json_encode(array(
            "ok" => true,
            "scope" => $scope,
            "total" => (int)$rowCount["n"],
            "sample" => $sample
        ));
        die();
    }

    if (isset($_REQUEST["sendBatchPush"])) {
        header('Content-Type: application/json');
        $usrToken = $db->real_escape_string((string)$_REQUEST["sendBatchPush"]);
        $usr = $db->query("select * from usuarios where token='".$usrToken."'")->fetch_assoc();
        if (!$usr) {
            echo json_encode(array("ok" => false, "message" => "Usuario no válido"));
            die();
        }

        $titulo = trim((string)($_REQUEST["titulo"] ?? ""));
        $mensaje = trim((string)($_REQUEST["mensaje"] ?? ""));
        if ($titulo === "" || $mensaje === "") {
            echo json_encode(array("ok" => false, "message" => "Título y mensaje son requeridos"));
            die();
        }

        $scope = " u.book_id='".$db->real_escape_string($usr["book_id"])."' ";
        $scope .= buildAlumniScopeFilters($db, $_REQUEST);
        $sql = "select token, book_id from alumnos u where $scope";
        $res = $db->query($sql);
        $sent = 0;
        $msgId = cobaedPushCreateMessageId();

        while ($r = $res->fetch_assoc()) {
            $ok = cobaedPushEnqueueMessageRtdb(
                $RTDB_URL,
                $SERVICE_ACCOUNT_PATH,
                $r["book_id"],
                $r["token"],
                $msgId,
                $titulo,
                $mensaje,
                $usrToken
            );
            if ($ok) {
                $sent++;
            }
        }

        // Auto-disparo del consumer para no depender de flush manual.
        $consumerTriggered = false;
        $consumerInfo = null;
        $autoConsume = ((string)($_REQUEST["auto_consume"] ?? "1") === "1");
        if ($autoConsume && $sent > 0) {
            $consumerKey = cobaedPushReadConsumerKey(__DIR__);
            $consumerUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'cobaedlomas.com') . '/cobaed/alumni/consume_cred_messages.php';
            $trigger = cobaedPushTriggerConsumerAsync($consumerUrl, $consumerKey, min(100, max(1, $sent)));
            $consumerTriggered = !empty($trigger['triggered']);
            $consumerInfo = $trigger;
        }

        echo json_encode(array(
            "ok" => true,
            "queued" => $sent,
            "sent" => $sent,
            "message_id" => $msgId,
            "consumer_triggered" => $consumerTriggered,
            "consumer" => $consumerInfo
        ));
        die();
    }

    if (isset($_REQUEST["fetchListas"])) {
        $sql = "select * from $tabla where token='".$_REQUEST["fetchListas"]."'";
        $row = $db->query($sql)->fetch_assoc();
        $miBook = $row["book_id"];

        $controles = [];
        if ($_REQUEST["usr"] != "x") {
            $sql = "select * from $tabla where token='".$_REQUEST["usr"]."'";
            $row = $db->query($sql)->fetch_assoc();
            if ($row) {
                // Get the list of fields from a comma-separated string in $controles
                $fields = array_map('trim', explode(',', $_REQUEST["controles"]));
                foreach ($fields as $field) {
                    if (isset($row[$field])) {
                        $controles[$field] = $row[$field];
                    }
                }
            }
        }
        $archivo =   __DIR__ . "/../assets/uploads/perfil_".$_REQUEST["usr"]."_256.jpg";    
            if (!file_exists($archivo)) {
                $archivo = "";
            }
            else
                $archivo = "perfil_".$_REQUEST["usr"]."_256.jpg";
        

        $response = array(
            "img" => $archivo,
            "status" => "success",

        "_controles_" => $controles,
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        die();
    }


    if (isset($_REQUEST["registrar"])) {
    // 1. REGLA DE ORO: Las cabeceras siempre antes de cualquier echo o warning
    header('Content-Type: application/json');

    file_put_contents('request_debug.txt', print_r($_REQUEST, true));
    
    $row = $db->query("select * from usuarios where token='".$db->real_escape_string($_REQUEST["registrar"])."'")->fetch_assoc();
    
    $coma = "";
    $sql_fields = "";
    
    // CORRECCIÓN CRÍTICA: Decodificar el JSON que enviamos desde JS
    $data = isset($_POST["dta"]) ? json_decode($_POST["dta"], true) : [];

    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $escaped_value = $db->real_escape_string($value);
            $sql_fields .= $coma . $key . "='" . $escaped_value . "'";
            $coma = ", ";
        }
    }

    $tkn = bin2hex(random_bytes(4));
    
    // Construcción de la consulta
    if ($_REQUEST["nuevo"] === "true" || $_REQUEST["nuevo"] === true) {
        $query_final = "insert into $tabla set token='$tkn', book_id='".$row["book_id"]."', ".$sql_fields;
    } else {
        $query_final = "update $tabla set ".$sql_fields." where token='".$db->real_escape_string($_REQUEST["token"])."'";
    }

    file_put_contents('sql_debug.txt', $query_final . PHP_EOL, FILE_APPEND);

    if (!empty($sql_fields) && $db->query($query_final)) {
        $response = array(
            "ok" => true,
            "message" => "Registro exitoso.",
            "sql" => $query_final
        );
        
        // Manejo de la foto si el registro fue exitoso
        $token_final = ($_REQUEST["nuevo"] === "true") ? $tkn : $_REQUEST["token"];
        if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
            subeFoto($_FILES["foto"], $token_final);
        } elseif (!empty($_REQUEST["rtdb_image_url"])) {
            subeFotoDesdeUrl($_REQUEST["rtdb_image_url"], $token_final);
        }
        notifyCredencialChangedRtdb($RTDB_URL, $SERVICE_ACCOUNT_PATH, $row["book_id"], $token_final, "upsert");
    } else {
        $response = array(
            "ok" => false,
            "message" => "No se pudo ejecutar la consulta SQL.",
            "error" => $db->error,
            "sql" => $query_final
        );
    }

    echo json_encode($response);
    die();
}


    function subeFoto($file, $tkn)
    {
        global $db, $tabla;

        $targetDir = __DIR__ . "/../assets/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $token = ($_REQUEST["nuevo"] === "true" ? $tkn : $_REQUEST["token"]);
        $tokenSafe = preg_replace('/[^a-zA-Z0-9_\-]/', '', $token);

        $tmp = $_FILES["foto"]["tmp_name"];
        $info = @getimagesize($tmp);
        if (!$info) {
            die("No es imagen válida.");
        }

        // Abrir
        switch ($info["mime"]) {
            case "image/jpeg": $src = imagecreatefromjpeg($tmp);
                $src = fixExifOrientationIfJpeg($tmp, $src);
                break;
            case "image/png":  $src = imagecreatefrompng($tmp);
                break;
            case "image/gif":  $src = imagecreatefromgif($tmp);
                break;
            case "image/webp": $src = function_exists("imagecreatefromwebp") ? imagecreatefromwebp($tmp) : null;
                break;
            default: die("Formato no permitido.");
        }
        if (!$src) {
            die("No se pudo abrir la imagen.");
        }

        // 1) Master (1024 lado mayor, <=350KB)
        $master = resizeFit($src, 1024, 1024);
        $masterName = "perfil_{$tokenSafe}.jpg";
        $masterPath = $targetDir . $masterName;
        saveJpegUnderKB($master, $masterPath, 350);

        // 2) Avatar (256x256, <=60KB)
        $thumb = cropCenterSquare($src, 256);
        $thumbName = "perfil_{$tokenSafe}_256.jpg";
        $thumbPath = $targetDir . $thumbName;
        saveJpegUnderKB($thumb, $thumbPath, 60, 80, 45);

        // 3) Credencial (700x875 aprox, <=180KB)
        $id = resizeFit($src, 700, 875);
        $idName = "perfil_{$tokenSafe}_id.jpg";
        $idPath = $targetDir . $idName;
        saveJpegUnderKB($id, $idPath, 180);

        imagedestroy($src);
        imagedestroy($master);
        imagedestroy($thumb);
        imagedestroy($id);

        // Guardar en BD (ajusta nombres de columnas según tu tabla)
        $tDb = $db->real_escape_string($token);
        $mDb = $db->real_escape_string($masterName);
        $thDb = $db->real_escape_string($thumbName);
        $idDb = $db->real_escape_string($idName);

        $sql = "UPDATE $tabla SET foto='$mDb', foto_thumb='$thDb', foto_id='$idDb' WHERE token='$tDb'";
        file_put_contents("upload_debug.txt", "Foto subida y procesada para token: $sql\n", FILE_APPEND);
        $db->query($sql);

    }

    function subeFotoDesdeUrl($url, $tkn)
    {
        global $db, $tabla;

        $url = trim((string)$url);
        if ($url === "") {
            return;
        }

        $parsed = parse_url($url);
        if (!$parsed || empty($parsed["scheme"]) || empty($parsed["host"])) {
            return;
        }
        if (!in_array(strtolower($parsed["scheme"]), array("http", "https"), true)) {
            return;
        }

        $allowedHosts = array(
            isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "",
            "cobaedlomas.com",
            "www.cobaedlomas.com"
        );
        if (!in_array(strtolower($parsed["host"]), array_map('strtolower', $allowedHosts), true)) {
            return;
        }

        $binary = @file_get_contents($url);
        if ($binary === false || strlen($binary) < 32) {
            return;
        }

        $info = @getimagesizefromstring($binary);
        if (!$info) {
            return;
        }

        $src = @imagecreatefromstring($binary);
        if (!$src) {
            return;
        }

        $targetDir = __DIR__ . "/../assets/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $token = $tkn;
        $tokenSafe = preg_replace('/[^a-zA-Z0-9_\-]/', '', $token);

        $master = resizeFit($src, 1024, 1024);
        $masterName = "perfil_{$tokenSafe}.jpg";
        $masterPath = $targetDir . $masterName;
        saveJpegUnderKB($master, $masterPath, 350);

        $thumb = cropCenterSquare($src, 256);
        $thumbName = "perfil_{$tokenSafe}_256.jpg";
        $thumbPath = $targetDir . $thumbName;
        saveJpegUnderKB($thumb, $thumbPath, 60, 80, 45);

        $id = resizeFit($src, 700, 875);
        $idName = "perfil_{$tokenSafe}_id.jpg";
        $idPath = $targetDir . $idName;
        saveJpegUnderKB($id, $idPath, 180);

        imagedestroy($src);
        imagedestroy($master);
        imagedestroy($thumb);
        imagedestroy($id);

        $tDb = $db->real_escape_string($token);
        $mDb = $db->real_escape_string($masterName);
        $thDb = $db->real_escape_string($thumbName);
        $idDb = $db->real_escape_string($idName);

        $sql = "UPDATE $tabla SET foto='$mDb', foto_thumb='$thDb', foto_id='$idDb' WHERE token='$tDb'";
        file_put_contents("upload_debug.txt", "Foto RTDB procesada para token: $sql\n", FILE_APPEND);
        $db->query($sql);
    }


    function fixExifOrientationIfJpeg($path, $img)
    {
        if (!function_exists('exif_read_data')) {
            return $img;
        }
        $exif = @exif_read_data($path);
        if (!$exif || empty($exif['Orientation'])) {
            return $img;
        }

        switch ((int)$exif['Orientation']) {
            case 3:  $img = imagerotate($img, 180, 0);
                break;
            case 6:  $img = imagerotate($img, -90, 0);
                break;
            case 8:  $img = imagerotate($img, 90, 0);
                break;
        }
        return $img;
    }

    function saveJpegUnderKB($gd, $path, $maxKB, $qStart = 85, $qMin = 45)
    {
        $q = $qStart;
        do {
            imagejpeg($gd, $path, $q);
            clearstatcache(true, $path);
            $kb = filesize($path) / 1024;
            $q -= 5;
        } while ($kb > $maxKB && $q >= $qMin);
    }

    function resizeFit($src, $maxW, $maxH)
    {
        $w = imagesx($src);
        $h = imagesy($src);
        $scale = min($maxW / $w, $maxH / $h, 1);
        $nw = (int)round($w * $scale);
        $nh = (int)round($h * $scale);

        $dst = imagecreatetruecolor($nw, $nh);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        return $dst;
    }

    function cropCenterSquare($src, $size)
    {
        $w = imagesx($src);
        $h = imagesy($src);
        $side = min($w, $h);
        $sx = (int)(($w - $side) / 2);
        $sy = (int)(($h - $side) / 2);

        $tmp = imagecreatetruecolor($side, $side);
        $white = imagecolorallocate($tmp, 255, 255, 255);
        imagefill($tmp, 0, 0, $white);

        imagecopyresampled($tmp, $src, 0, 0, $sx, $sy, $side, $side, $side, $side);

        $dst = imagecreatetruecolor($size, $size);
        imagefill($dst, 0, 0, $white);
        imagecopyresampled($dst, $tmp, 0, 0, 0, 0, $size, $size, $side, $side);
        imagedestroy($tmp);
        return $dst;
    }

    function buildAlumniScopeFilters($db, $req)
    {
        $scope = "";
        if (isset($req["fltNombre"]) && trim($req["fltNombre"]) !== "") {
            $v = $db->real_escape_string(trim($req["fltNombre"]));
            $scope .= " and u.nombre like '%$v%'";
        }
        if (isset($req["fltSemestre"]) && trim($req["fltSemestre"]) !== "" && $req["fltSemestre"] !== "-1") {
            $v = (int)$req["fltSemestre"];
            $scope .= " and u.semestre=$v";
        }
        if (isset($req["fltGrupo"]) && trim($req["fltGrupo"]) !== "" && $req["fltGrupo"] !== "-1") {
            $v = $db->real_escape_string(trim($req["fltGrupo"]));
            $scope .= " and u.grupo='$v'";
        }
        if (isset($req["fltTurno"]) && trim($req["fltTurno"]) !== "" && $req["fltTurno"] !== "-1") {
            $v = (int)$req["fltTurno"];
            $scope .= " and u.turno_id=$v";
        }
        if (isset($req["fltEstado"]) && trim($req["fltEstado"]) !== "" && $req["fltEstado"] !== "-1") {
            $v = (int)$req["fltEstado"];
            $scope .= " and u.estado=$v";
        }
        return $scope;
    }

    function notifyCredencialChangedRtdb($rtdbUrl, $serviceAccountPath, $bookId, $alumnoToken, $eventType)
    {
        $payload = array(
            "book_id" => (string)$bookId,
            "alumno_token" => (string)$alumnoToken,
            "event" => (string)$eventType,
            "updated_at" => date('c'),
            "revision" => hash('sha1', $alumnoToken . '|' . microtime(true))
        );

        $path = '/cred_updates_by_token/' . rawurlencode($alumnoToken) . '/latest.json';
        rtdbPutWithServiceAccount($rtdbUrl, $serviceAccountPath, $path, $payload);
    }

    function sendBatchCredentialMessageRtdb($rtdbUrl, $serviceAccountPath, $bookId, $alumnoToken, $msgId, $titulo, $mensaje, $fromToken)
    {
        $payload = array(
            "message_id" => (string)$msgId,
            "book_id" => (string)$bookId,
            "alumno_token" => (string)$alumnoToken,
            "titulo" => (string)$titulo,
            "mensaje" => (string)$mensaje,
            "from" => (string)$fromToken,
            "created_at" => date('c')
        );

        $path = '/cred_messages/' . rawurlencode((string)$bookId) . '/' . rawurlencode((string)$alumnoToken) . '/' . rawurlencode((string)$msgId) . '.json';
        return rtdbPutWithServiceAccount($rtdbUrl, $serviceAccountPath, $path, $payload);
    }

    function rtdbPutWithServiceAccount($rtdbUrl, $serviceAccountPath, $path, $payload)
    {
        if (!function_exists('curl_init')) {
            return false;
        }

        $scope = 'https://www.googleapis.com/auth/firebase.database https://www.googleapis.com/auth/userinfo.email';
        $tokenRes = getGoogleAccessTokenLocal($serviceAccountPath, $scope);
        if (empty($tokenRes['ok'])) {
            return false;
        }

        $url = rtrim($rtdbUrl, '/') . $path;
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $tokenRes['access_token'],
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 20
        ));

        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $resp !== false && $http >= 200 && $http < 300;
    }

    function getGoogleAccessTokenLocal($serviceAccountPath, $scope)
    {
        if (!function_exists('openssl_sign') || !function_exists('curl_init')) {
            return array('ok' => false, 'error' => 'openssl/curl no disponible');
        }
        if (!file_exists($serviceAccountPath)) {
            return array('ok' => false, 'error' => 'service account no encontrado');
        }

        $sa = json_decode(file_get_contents($serviceAccountPath), true);
        if (!$sa || empty($sa['client_email']) || empty($sa['private_key']) || empty($sa['token_uri'])) {
            return array('ok' => false, 'error' => 'service account inválido');
        }

        $now = time();
        $jwtHeader = array('alg' => 'RS256', 'typ' => 'JWT');
        $jwtClaim = array(
            'iss' => $sa['client_email'],
            'scope' => $scope,
            'aud' => $sa['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600
        );

        $base = b64urlLocal(json_encode($jwtHeader)) . '.' . b64urlLocal(json_encode($jwtClaim));
        $signature = '';
        $okSign = openssl_sign($base, $signature, $sa['private_key'], OPENSSL_ALGO_SHA256);
        if (!$okSign) {
            return array('ok' => false, 'error' => 'no se pudo firmar JWT');
        }

        $jwt = $base . '.' . b64urlLocal($signature);
        $post = http_build_query(array(
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ));

        $ch = curl_init($sa['token_uri']);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_TIMEOUT => 20
        ));
        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($resp === false || $http < 200 || $http >= 300) {
            return array('ok' => false, 'error' => 'token oauth inválido');
        }
        $json = json_decode($resp, true);
        if (empty($json['access_token'])) {
            return array('ok' => false, 'error' => 'access_token ausente');
        }

        return array('ok' => true, 'access_token' => $json['access_token']);
    }

    function b64urlLocal($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    function resolveServiceAccountPath()
    {
        $env = getenv('COBAED_FIREBASE_SERVICE_ACCOUNT');
        if ($env && file_exists($env)) {
            return $env;
        }

        $candidates = array(
            __DIR__ . '/../../cam/cobaedlomas-firebase-adminsdk-fbsvc-69bc07f243.json',
            __DIR__ . '/../../cam/cobaedlomas-firebase-adminsdk-fbsvc-896c424477.json',
            __DIR__ . '/../../cobaedlomas-firebase-adminsdk-fbsvc-896c424477.json',
            __DIR__ . '/../../../cobaedlomas-firebase-adminsdk-fbsvc-896c424477.json'
        );
        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        $patterns = array(
            __DIR__ . '/../../cam/cobaedlomas-firebase-adminsdk-*.json',
            __DIR__ . '/../../cobaedlomas-firebase-adminsdk-*.json',
            __DIR__ . '/../../../cobaedlomas-firebase-adminsdk-*.json'
        );
        foreach ($patterns as $pattern) {
            $found = glob($pattern);
            if ($found && !empty($found[0])) {
                return $found[0];
            }
        }

        return __DIR__ . '/../../cam/cobaedlomas-firebase-adminsdk-fbsvc-69bc07f243.json';
    }
