<?php

header('Content-Type: application/json; charset=utf-8');

$paths = array(
    __DIR__ . '/cobaedlomas-service-account.json',
    __DIR__ . '/../../cam/cobaedlomas-firebase-adminsdk-fbsvc-69bc07f243.json',
    __DIR__ . '/../../cam/cobaedlomas-firebase-adminsdk-fbsvc-896c424477.json',
    __DIR__ . '/../../cobaedlomas-firebase-adminsdk-fbsvc-896c424477.json',
    __DIR__ . '/../../../cobaedlomas-firebase-adminsdk-fbsvc-896c424477.json'
);

$report = array();
foreach ($paths as $p) {
    $exists = file_exists($p);
    $size = $exists ? @filesize($p) : 0;
    $jsonOk = false;
    $keysOk = false;

    if ($exists && $size > 0) {
        $raw = @file_get_contents($p);
        $json = @json_decode((string)$raw, true);
        $jsonOk = is_array($json);
        if ($jsonOk) {
            $keysOk = !empty($json['client_email']) && !empty($json['private_key']) && !empty($json['token_uri']);
        }
    }

    $report[] = array(
        'path' => $p,
        'exists' => $exists,
        'size' => (int)$size,
        'json_ok' => $jsonOk,
        'keys_ok' => $keysOk
    );
}

echo json_encode(array(
    'ok' => true,
    'checked_at' => date('c'),
    'php_version' => PHP_VERSION,
    'openssl' => function_exists('openssl_sign'),
    'curl' => function_exists('curl_init'),
    'candidates' => $report
), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
