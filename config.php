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
