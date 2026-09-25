<?php
// ============================================================================
// Hoja de códigos QR por salón, lista para imprimir y pegar en cada aula.
// El QR codifica la misma URL que espera la app: ...newsboard.php?area=<token>
// Uso: qr-salones.php              -> todos los salones activos
//      qr-salones.php?area=23      -> sólo uno (útil para probar)
//      qr-salones.php?area=11,12   -> varios (hoja para una jornada de pruebas)
// ============================================================================
require_once "conexion1.php";

// Acepta un id o una lista separada por comas; se saneia a enteros.
$ids = array_values(array_filter(array_map(
    'intval', explode(',', (string)($_GET["area"] ?? ''))
)));
$sql = "SELECT area_id, token, nombre FROM areas WHERE activa=1"
     . ($ids ? " AND area_id IN (" . implode(',', $ids) . ")" : "")
     . " ORDER BY nombre";
$areas = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
$prefix = "https://" . ($_SERVER["HTTP_HOST"] ?? "cobaedlomas.com") . "/cobaed/newsboard.php?area=";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>QR de salones - COBAED</title>
<style>
    body { font-family: system-ui, sans-serif; background:#f1f5f2; color:#1a221d; margin:0; padding:24px; }
    h1 { font-size:1.1rem; margin:0 0 4px; }
    p.sub { font-size:.85rem; color:#6b7a72; margin:0 0 22px; }
    .hoja { display:grid; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); gap:16px; }
    .qr { background:#fff; border-radius:14px; padding:18px; text-align:center;
          box-shadow:0 2px 12px rgba(36,26,32,.09); break-inside:avoid; }
    .qr canvas { width:100%; max-width:190px; height:auto; image-rendering:pixelated; }
    .qr h2 { font-size:1rem; margin:10px 0 2px; }
    .qr code { font-size:.7rem; color:#6b7a72; word-break:break-all; }
    @media print {
        body { background:#fff; padding:0; }
        .noprint { display:none; }
        .qr { box-shadow:none; border:1px solid #ddd; page-break-inside:avoid; }
    }
</style>
</head>
<body>
<h1>Códigos QR de salones</h1>
<p class="sub">Imprime y pega el código en la entrada de cada salón. <?= count($areas) ?> salón(es).</p>
<button class="noprint" onclick="print()" style="margin-bottom:18px;padding:10px 18px;border:0;border-radius:10px;background:#1b5e43;color:#fff;font-size:.9rem">Imprimir</button>

<div class="hoja">
<?php foreach ($areas as $a): ?>
    <div class="qr">
        <canvas data-texto="<?= htmlspecialchars($prefix . $a["token"], ENT_QUOTES) ?>"></canvas>
        <h2><?= htmlspecialchars($a["nombre"]) ?></h2>
        <code><?= htmlspecialchars($a["token"]) ?></code>
    </div>
<?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
document.querySelectorAll("canvas[data-texto]").forEach((c) => {
    QRCode.toCanvas(c, c.dataset.texto, { width: 380, margin: 1,
        color: { dark: "#241a20", light: "#ffffff" } });
});
</script>
</body>
</html>
