<?php
// ============================================================================
// Conexión y arranque común de la app móvil (Control de Asistencia COBAED).
//
// Zona horaria: el plantel opera en UTC-6. Este servidor corre en UTC, por lo
// que TODA fecha/hora se calcula en PHP con America/Mexico_City (UTC-6 fijo,
// México eliminó el horario de verano en 2022) y NUNCA con NOW() de MySQL,
// que devolvería la hora UTC y correría los registros 6 horas.
// ============================================================================

date_default_timezone_set('America/Mexico_City');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    require_once __DIR__ . '/../config.php';
    $db = new mysqli(cfg('db.host'), cfg('db.user'), cfg('db.pwd'), cfg('db.name'));
    $db->set_charset("utf8mb4");
} catch (Throwable $e) {
    error_log("COBAED movil - fallo de conexión: " . $e->getMessage());
    header('Content-Type: application/json; charset=utf-8', true, 503);
    echo json_encode(["ok" => false, "msg" => "Servicio no disponible"]);
    exit;
}

/** Hora local del plantel (UTC-6) en formato HH:MM:SS. */
function horaLocal() { return date('H:i:s'); }

/** Fecha local del plantel (UTC-6) en formato Y-m-d. */
function fechaLocal() { return date('Y-m-d'); }

/** Día ISO local: 1=lunes ... 7=domingo (coincide con horarios.dia). */
function diaLocal() { return (int)date('N'); }

/** Marca de tiempo local completa para columnas DATETIME. */
function ahoraLocal() { return date('Y-m-d H:i:s'); }
