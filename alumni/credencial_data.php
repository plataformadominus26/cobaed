<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../rbh/conexion1.php';

if (!isset($_GET['atkn']) || trim($_GET['atkn']) === '') {
    http_response_code(400);
    echo json_encode(array('ok' => false, 'message' => 'Falta atkn'));
    exit;
}

$token = trim($_GET['atkn']);
if (!preg_match('/^[A-Za-z0-9_-]{4,64}$/', $token)) {
    http_response_code(400);
    echo json_encode(array('ok' => false, 'message' => 'Token invalido'));
    exit;
}

$sql = "SELECT
            a.*,
            p.nombre AS plantel_nombre,
            p.cct AS plantel_cct,
            t.nombre AS turno_nombre,
            s.nombre AS sangre_nombre,
            pa1.nombre AS parentesco1_nombre,
            pa2.nombre AS parentesco2_nombre
        FROM alumnos a
        LEFT JOIN planteles p ON p.plantel_id = a.plantel_id
        LEFT JOIN turnos t ON t.turno_id = a.turno_id
        LEFT JOIN tipos_sangre s ON s.sangre_id = a.sangre_id
        LEFT JOIN cat_parentescos pa1 ON pa1.parentesco_id = a.parentesco1
        LEFT JOIN cat_parentescos pa2 ON pa2.parentesco_id = a.parentesco2
        WHERE a.token = ?
        LIMIT 1";

$stmt = $db->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(array('ok' => false, 'message' => 'No se pudo preparar consulta', 'error' => $db->error));
    exit;
}

$stmt->bind_param('s', $token);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$row) {
    http_response_code(404);
    echo json_encode(array('ok' => false, 'message' => 'Alumno no encontrado'));
    exit;
}

$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'cobaedlomas.com';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

$fotoId = isset($row['foto_id']) ? trim((string)$row['foto_id']) : '';
$fotoUrl = $fotoId !== '' ? ($scheme . '://' . $host . '/cobaed/assets/uploads/' . rawurlencode($fotoId)) : '';

$fotoMtime = '';
if ($fotoId !== '') {
    $fotoPath = __DIR__ . '/../assets/uploads/' . $fotoId;
    if (file_exists($fotoPath)) {
        $fotoMtime = (string)filemtime($fotoPath);
    }
}

$revision = md5(json_encode($row, JSON_UNESCAPED_UNICODE) . '|' . $fotoMtime);

$payloadUrl = $scheme . '://' . $host . '/cobaed/enrollalumni.php?atkn=' . rawurlencode($token);
$qrUrl = $scheme . '://' . $host . '/--id/assets/api/qr.php?data=' . rawurlencode($payloadUrl);

if (isset($_GET['meta']) && $_GET['meta'] === '1') {
    echo json_encode(array(
        'ok' => true,
        'token' => $token,
        'revision' => $revision
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

$nombre = trim((string)($row['nombre'] ?? ''));
$paterno = trim((string)($row['paterno'] ?? ''));
$materno = trim((string)($row['materno'] ?? ''));
$apellidos = trim($paterno . ' ' . $materno);

$response = array(
    'ok' => true,
    'token' => $token,
    'revision' => $revision,
    'nombre' => $nombre,
    'apellidos' => $apellidos,
    'plantel' => trim((string)($row['plantel_nombre'] ?? '')),
    'cct' => trim((string)($row['plantel_cct'] ?? '')),
    'matricula' => trim((string)($row['matricula'] ?? '')),
    'domicilio' => trim((string)($row['direccion'] ?? '')),
    'lugar' => 'DURANGO, DGO.',
    'semestre' => trim((string)($row['semestre'] ?? '')),
    'grupo' => trim((string)($row['grupo'] ?? '')),
    'turno' => trim((string)($row['turno_nombre'] ?? '')),
    'sangre' => trim((string)($row['sangre_nombre'] ?? '')),
    'seguro' => trim((string)($row['seguro'] ?? '')),
    'alergias' => trim((string)($row['alergias'] ?? '')),
    'padecimientos' => trim((string)($row['padecimientos'] ?? '')),
    'contacto1' => trim((string)($row['contacto1'] ?? '')),
    'parentesco1' => trim((string)($row['parentesco1_nombre'] ?? '')),
    'tel1' => trim((string)($row['tel1'] ?? '')),
    'contacto2' => trim((string)($row['contacto2'] ?? '')),
    'parentesco2' => trim((string)($row['parentesco2_nombre'] ?? '')),
    'tel2' => trim((string)($row['tel2'] ?? '')),
    'foto' => $fotoUrl,
    'qr' => $qrUrl
);

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>