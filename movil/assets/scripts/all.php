<?php
$urlsToCache = [
    '/cobaed/movil/assets/img/icons/favico.png',
    '/cobaed/movil/index.html',
    '/cobaed/movil/movil.css',
    '/cobaed/movil/manifest-2.json',
    '/cobaed/movil/this.js',
    '/cobaed/movil/qr.css',
    '/cobaed/movil/assets/css/bootstrap.min.css',
    '/cobaed/movil/assets/css/bootstrap-icons.min.css',
    '/cobaed/movil/assets/css/fonts/bootstrap-icons.woff',
    '/cobaed/movil/assets/css/fonts/bootstrap-icons.woff2',
    '/cobaed/movil/assets/css/bootstrap.min.css',
    '/cobaed/movil/assets/css/bootstrap-icons.min.css',
    '/cobaed/movil/assets/js/bootstrap.bundle.min.js',
    '/cobaed/movil/assets/js/bootstrap.bundle.min.js',
    '/cobaed/movil/assets/js/jquery-3.6.0.min.js',
    '/cobaed/movil/assets/js/qr-scanner.umd.min.js',
    '/cobaed/movil/assets/js/easyDb.js',
    '/cobaed/movil/assets/img/icons/icon-48x48.png',
    '/cobaed/movil/assets/img/icons/icon-72x72.png',
    '/cobaed/movil/assets/img/icons/icon-96x96.png',
    '/cobaed/movil/assets/img/icons/icon-144x144.png',
    '/cobaed/movil/assets/img/icons/icon-192x192.png',
    '/cobaed/movil/assets/img/icons/icon-512x512.png',
    '/cobaed/movil/assets/img/cobaed2010.png'
];

$baseDir = $_SERVER['DOCUMENT_ROOT'];

echo "<table border='1'><tr><th>File</th><th>Exists</th></tr>";
foreach ($urlsToCache as $url) {
    $filePath = $baseDir . $url;
    $exists = file_exists($filePath) ? 'Yes' : 'No';
    echo "<tr><td>{$url}</td><td>{$exists}</td></tr>";
}
echo "</table>";
?>