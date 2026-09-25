<?php
// Carga la configuración del plantel (config.local.php) y expone cfg('a.b').
if (!function_exists('cfg')) {
    function cfg($key, $default = null) {
        static $conf = null;
        if ($conf === null) {
            $file = __DIR__ . '/config.local.php';
            $conf = is_file($file) ? require $file : [];
        }
        $v = $conf;
        foreach (explode('.', $key) as $k) {
            if (!is_array($v) || !array_key_exists($k, $v)) return $default;
            $v = $v[$k];
        }
        return $v;
    }
    function plantel_nombre() {
        return htmlspecialchars(cfg('plantel.nombre', 'Cobaed'), ENT_QUOTES, 'UTF-8');
    }
}

// Ubicación de las llaves de Firebase (fuera de la carpeta web). El código de push
// ya lee estas variables de entorno antes de buscar en sus rutas antiguas.
// Por defecto se buscan en ~/secure/ del usuario del plantel.
$__secure = dirname(__DIR__, 3) . '/secure';
$__sa  = (string)cfg('firebase.service_account', $__secure . '/firebase-service-account.json');
$__key = (string)cfg('firebase.consumer_key_file', $__secure . '/consume_cred_messages.key');
if (!getenv('COBAED_FIREBASE_SERVICE_ACCOUNT') && is_file($__sa)) {
    putenv('COBAED_FIREBASE_SERVICE_ACCOUNT=' . $__sa);
}
if (!getenv('COBAED_FCM_CONSUMER_KEY') && is_file($__key)) {
    putenv('COBAED_FCM_CONSUMER_KEY=' . trim((string)file_get_contents($__key)));
}
unset($__secure, $__sa, $__key);

// Ruta para logs de depuración, fuera de la carpeta web.
if (!function_exists('cobaed_log')) {
    function cobaed_log($nombre) {
        $dir = cfg('logs_dir') ?: dirname(__DIR__, 3) . '/logs/cobaed';
        if (!is_dir($dir)) @mkdir($dir, 0770, true);
        return rtrim($dir, '/') . '/' . basename($nombre);
    }
}
