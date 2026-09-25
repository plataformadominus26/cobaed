<?php
// Aplica a la base de ESTE plantel las migraciones pendientes de deploy/migraciones/*.sql
// (en orden alfabético; cada una se aplica una sola vez y queda anotada en _migraciones).
//
//   php deploy/migrar.php                 aplica las pendientes
//   php deploy/migrar.php --marcar-todas  las marca como aplicadas sin ejecutarlas (instalación nueva)
//   php deploy/migrar.php --instalar      base vacía: crea las tablas (esquema.sql) y marca todas
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

require __DIR__ . '/../config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli(cfg('db.host'), cfg('db.user'), cfg('db.pwd'), cfg('db.name'));
$db->set_charset('utf8mb4');

function ejecutarSql(mysqli $db, string $sql): void {
    $db->multi_query($sql);
    do {
        if ($r = $db->store_result()) $r->free();
    } while ($db->more_results() && $db->next_result());
}

$modo = $argv[1] ?? '';
if ($modo === '--instalar') {
    $n = $db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()")->fetch_row()[0];
    if ($n > 0) { fwrite(STDERR, "La base ya tiene $n tablas; no se instala nada.\n"); exit(1); }
    ejecutarSql($db, file_get_contents(__DIR__ . '/esquema.sql'));
    echo "Tablas creadas.\n";
    $modo = '--marcar-todas';
}

$db->query("CREATE TABLE IF NOT EXISTS _migraciones (nombre VARCHAR(190) PRIMARY KEY, aplicada DATETIME NOT NULL)");
$hechas = array_column($db->query("SELECT nombre FROM _migraciones")->fetch_all(), 0);
$archivos = glob(__DIR__ . '/migraciones/*.sql');
sort($archivos);

foreach ($archivos as $f) {
    $nombre = basename($f);
    if (in_array($nombre, $hechas, true)) continue;
    try {
        if ($modo !== '--marcar-todas') ejecutarSql($db, file_get_contents($f));
        $st = $db->prepare("INSERT INTO _migraciones (nombre, aplicada) VALUES (?, NOW())");
        $st->bind_param('s', $nombre);
        $st->execute();
        echo date('Y-m-d H:i:s') . ($modo === '--marcar-todas' ? " Marcada: " : " Migración aplicada: ") . "$nombre\n";
    } catch (Throwable $e) {
        echo date('Y-m-d H:i:s') . " ERROR en $nombre: " . $e->getMessage() . "\n";
        exit(1);
    }
}
