<?php

   if(!isset($_REQUEST["action"])) {
       echo "error";
       exit();
   }
   include_once("../rbh/conexion1.php");
    $RTDB_URL = 'https://cobaedlomas-default-rtdb.firebaseio.com';
    $SERVICE_ACCOUNT_PATH = resolveServiceAccountPath();
    switch($_REQUEST["action"]) {
       case "getConfig":
            $retVal=getConfig();
        break;
        case "login":
            $username=$_REQUEST["username"];
            $password=$_REQUEST["password"];
            $sql="SELECT * FROM alumnos WHERE (email='".$_REQUEST["username"]."'";
            $sql.=" or telefono='".$_REQUEST["username"]."'";
            $sql.=" or matricula='".$_REQUEST["username"]."')";
            $sql.=" AND pwd='".$_REQUEST["password"]."'";
            $row=$db->query($sql)->fetch_assoc();
            if($row) {
                $row["ok"]="true";
                $retVal=json_encode($row);

            } else  
                $retVal=json_encode(array("ok"=>"error","message"=>"Clave o usuario incorrecto"));
        break;
        case "registerPushToken":
            $retVal = registerPushToken($db, $RTDB_URL, $SERVICE_ACCOUNT_PATH);
        break;
        case "unregisterPushToken":
            $retVal = unregisterPushToken($db, $RTDB_URL, $SERVICE_ACCOUNT_PATH);
        break;
        case "pushDiag":
            $retVal = pushDiag();
        break;
             
    }
    header('Content-Type: application/json');
    echo $retVal;

    function getConfig() {
         $config = array();
         $kobaiDir="../assets/img/kobai";
         $randomPng=scandir($kobaiDir);
        $randomPng = array_filter(scandir($kobaiDir), function($file) {
            return preg_match('/\.(png|jpg|jpeg|gif)$/i', $file);
        });
         $randomIndex=array_rand($randomPng);
         $config["mascotImg"]="../assets/img/kobai/".$randomPng[$randomIndex];
        //
         return json_encode($config);
    }

    function registerPushToken($db, $rtdbUrl, $serviceAccountPath) {
        $alumnoToken = trim((string)($_REQUEST["alumno_token"] ?? ""));
        $pushToken = trim((string)($_REQUEST["push_token"] ?? ""));
        $deviceId = preg_replace('/[^a-zA-Z0-9_\-]/', '_', (string)($_REQUEST["device_id"] ?? ""));
        $platform = trim((string)($_REQUEST["platform"] ?? ""));

        if ($alumnoToken === "" || $pushToken === "") {
            return json_encode(array("ok" => false, "message" => "alumno_token y push_token son requeridos"));
        }
        if ($deviceId === "") {
            $deviceId = 'dev_' . bin2hex(random_bytes(4));
        }

        $safeToken = $db->real_escape_string($alumnoToken);
        $row = $db->query("SELECT token, book_id FROM alumnos WHERE token='".$safeToken."' LIMIT 1")->fetch_assoc();
        if (!$row) {
            return json_encode(array("ok" => false, "message" => "alumno no válido"));
        }

        $payload = array(
            "alumno_token" => (string)$row["token"],
            "book_id" => (string)$row["book_id"],
            "token" => $pushToken,
            "platform" => $platform,
            "device_id" => $deviceId,
            "active" => true,
            "updated_at" => date('c')
        );

        $path = '/push_devices/'
            . rawurlencode((string)$row["book_id"]) . '/'
            . rawurlencode((string)$row["token"]) . '/'
            . rawurlencode((string)$deviceId) . '.json';

        $ok = rtdbPutWithServiceAccount($rtdbUrl, $serviceAccountPath, $path, $payload);
        @file_put_contents(__DIR__ . '/push_register_log.txt', date('c') . " registerPushToken alumno=" . $row["token"] . " device=" . $deviceId . " ok=" . ($ok ? '1' : '0') . PHP_EOL, FILE_APPEND);
        return json_encode(array("ok" => (bool)$ok, "device_id" => $deviceId));
    }

    function unregisterPushToken($db, $rtdbUrl, $serviceAccountPath) {
        $alumnoToken = trim((string)($_REQUEST["alumno_token"] ?? ""));
        $deviceId = preg_replace('/[^a-zA-Z0-9_\-]/', '_', (string)($_REQUEST["device_id"] ?? ""));
        if ($alumnoToken === "" || $deviceId === "") {
            return json_encode(array("ok" => false, "message" => "alumno_token y device_id son requeridos"));
        }

        $safeToken = $db->real_escape_string($alumnoToken);
        $row = $db->query("SELECT token, book_id FROM alumnos WHERE token='".$safeToken."' LIMIT 1")->fetch_assoc();
        if (!$row) {
            return json_encode(array("ok" => false, "message" => "alumno no válido"));
        }

        $path = '/push_devices/'
            . rawurlencode((string)$row["book_id"]) . '/'
            . rawurlencode((string)$row["token"]) . '/'
            . rawurlencode((string)$deviceId) . '.json';

        $ok = rtdbDeleteWithServiceAccount($rtdbUrl, $serviceAccountPath, $path);
        return json_encode(array("ok" => (bool)$ok));
    }

    function pushDiag() {
        $stage = trim((string)($_REQUEST["stage"] ?? ""));
        $platform = trim((string)($_REQUEST["platform"] ?? ""));
        $extra = trim((string)($_REQUEST["extra"] ?? ""));
        $alumnoToken = trim((string)($_REQUEST["alumno_token"] ?? ""));
        $line = date('c')
            . " pushDiag stage=" . $stage
            . " platform=" . $platform
            . " alumno=" . $alumnoToken
            . " extra=" . $extra
            . PHP_EOL;
        @file_put_contents(__DIR__ . '/push_register_log.txt', $line, FILE_APPEND);
        return json_encode(array("ok" => true));
    }

    function rtdbPutWithServiceAccount($rtdbUrl, $serviceAccountPath, $path, $payload)
    {
        return rtdbRequestWithServiceAccount('PUT', $rtdbUrl, $serviceAccountPath, $path, $payload);
    }

    function rtdbDeleteWithServiceAccount($rtdbUrl, $serviceAccountPath, $path)
    {
        return rtdbRequestWithServiceAccount('DELETE', $rtdbUrl, $serviceAccountPath, $path, null);
    }

    function rtdbRequestWithServiceAccount($method, $rtdbUrl, $serviceAccountPath, $path, $payload)
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
        $opts = array(
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $tokenRes['access_token'],
                'Content-Type: application/json'
            ),
            CURLOPT_TIMEOUT => 20
        );
        if ($payload !== null) {
            $opts[CURLOPT_POSTFIELDS] = json_encode($payload);
        }
        curl_setopt_array($ch, $opts);
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
            __DIR__ . '/../../alumni/cobaedlomas-service-account.json',
            __DIR__ . '/../../../cam/cobaedlomas-firebase-adminsdk-fbsvc-69bc07f243.json',
            __DIR__ . '/../../../cam/cobaedlomas-firebase-adminsdk-fbsvc-896c424477.json',
            __DIR__ . '/../../../cobaedlomas-firebase-adminsdk-fbsvc-896c424477.json',
            __DIR__ . '/../../../../cobaedlomas-firebase-adminsdk-fbsvc-896c424477.json'
        );
        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        $patterns = array(
            __DIR__ . '/../../alumni/cobaedlomas-service-account*.json',
            __DIR__ . '/../../../cam/cobaedlomas-firebase-adminsdk-*.json',
            __DIR__ . '/../../../cobaedlomas-firebase-adminsdk-*.json',
            __DIR__ . '/../../../../cobaedlomas-firebase-adminsdk-*.json'
        );
        foreach ($patterns as $pattern) {
            $found = glob($pattern);
            if ($found && !empty($found[0])) {
                return $found[0];
            }
        }

        return __DIR__ . '/../../../cam/cobaedlomas-firebase-adminsdk-fbsvc-69bc07f243.json';
    }
?>