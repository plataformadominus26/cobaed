<?php

declare(strict_types=1);

/**
 * Consumer de cola RTDB -> FCM v1 para mensajes de credencial.
 *
 * Uso CLI:
 *   php consume_cred_messages.php --limit=50 --max-attempts=3 --dry-run=1
 *
 * Uso HTTP (recomendado con key):
 *   /alumni/consume_cred_messages.php?key=TU_KEY&limit=50&dry_run=1
 */

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$PROJECT_ID = getenv('COBAED_FIREBASE_PROJECT_ID') ?: 'cobaedlomas';
$RTDB_URL = getenv('COBAED_FIREBASE_RTDB_URL') ?: 'https://cobaedlomas-default-rtdb.firebaseio.com';
$SERVICE_ACCOUNT_PATH = resolveServiceAccountPath();
$LOCK_FILE = __DIR__ . '/consume_cred_messages.lock';

// Si no se define, HTTP queda bloqueado por seguridad.
$HTTP_KEY = getenv('COBAED_FCM_CONSUMER_KEY') ?: '';
if ($HTTP_KEY === '') {
    $keyFiles = [
        __DIR__ . '/.consume_cred_messages.key',
        __DIR__ . '/consume_cred_messages.key',
        __DIR__ . '/consume_cred_messages.secret',
        __DIR__ . '/.consume_cred_messages.secret'
    ];
    foreach ($keyFiles as $keyFile) {
        if (file_exists($keyFile)) {
            $HTTP_KEY = trim((string)@file_get_contents($keyFile));
            if ($HTTP_KEY !== '') {
                break;
            }
        }
    }
}

$opts = readOptions($argv ?? []);
if (!$opts['is_cli']) {
    header('Content-Type: application/json; charset=utf-8');
}

if (!$opts['is_cli']) {
    if ($HTTP_KEY === '') {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'consumer key no configurada'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (!hash_equals($HTTP_KEY, (string)$opts['key'])) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'error' => 'key inválida'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$lockHandle = fopen($LOCK_FILE, 'c+');
if (!$lockHandle) {
    respond($opts['is_cli'], ['ok' => false, 'error' => 'no se pudo abrir lock file']);
    exit(1);
}

if (!flock($lockHandle, LOCK_EX | LOCK_NB)) {
    respond($opts['is_cli'], ['ok' => false, 'error' => 'otro consumer está en ejecución']);
    fclose($lockHandle);
    exit(1);
}

$summary = [
    'ok' => true,
    'dry_run' => $opts['dry_run'],
    'limit' => $opts['limit'],
    'max_attempts' => $opts['max_attempts'],
    'processed' => 0,
    'sent' => 0,
    'failed' => 0,
    'dead_letter' => 0,
    'remaining_limit' => $opts['limit'],
    'started_at' => gmdate('c'),
    'errors' => []
];

try {
    $dbToken = getGoogleAccessToken($SERVICE_ACCOUNT_PATH, 'https://www.googleapis.com/auth/firebase.database https://www.googleapis.com/auth/userinfo.email');
    if (!$dbToken['ok']) {
        throw new RuntimeException('token RTDB inválido: ' . $dbToken['error']);
    }

    $fcmToken = getGoogleAccessToken($SERVICE_ACCOUNT_PATH, 'https://www.googleapis.com/auth/firebase.messaging');
    if (!$fcmToken['ok']) {
        throw new RuntimeException('token FCM inválido: ' . $fcmToken['error']);
    }

    $bookIdsRes = rtdbGet($RTDB_URL, '/cred_messages.json?shallow=true', $dbToken['access_token']);
    if (!$bookIdsRes['ok']) {
        throw new RuntimeException('no se pudo leer cred_messages root');
    }

    $bookIds = is_array($bookIdsRes['data']) ? array_keys($bookIdsRes['data']) : [];

    foreach ($bookIds as $bookId) {
        if ($summary['remaining_limit'] <= 0) {
            break;
        }

        $alumnosRes = rtdbGet($RTDB_URL, '/cred_messages/' . rawurlencode((string)$bookId) . '.json?shallow=true', $dbToken['access_token']);
        if (!$alumnosRes['ok']) {
            $summary['errors'][] = 'no se pudo leer alumnos para book_id=' . $bookId;
            continue;
        }

        $alumnoTokens = is_array($alumnosRes['data']) ? array_keys($alumnosRes['data']) : [];

        foreach ($alumnoTokens as $alumnoToken) {
            if ($summary['remaining_limit'] <= 0) {
                break;
            }

            $messagesPath = '/cred_messages/' . rawurlencode((string)$bookId) . '/' . rawurlencode((string)$alumnoToken) . '.json';
            $msgsRes = rtdbGet($RTDB_URL, $messagesPath, $dbToken['access_token']);
            if (!$msgsRes['ok'] || !is_array($msgsRes['data'])) {
                continue;
            }

            $msgIds = array_keys($msgsRes['data']);
            sort($msgIds);

            foreach ($msgIds as $msgId) {
                if ($summary['remaining_limit'] <= 0) {
                    break;
                }

                $msg = $msgsRes['data'][$msgId];
                if (!is_array($msg)) {
                    continue;
                }

                $attempts = (int)($msg['attempts'] ?? 0);
                if ($attempts >= $opts['max_attempts']) {
                    $deadPayload = [
                        'book_id' => (string)$bookId,
                        'alumno_token' => (string)$alumnoToken,
                        'message_id' => (string)$msgId,
                        'reason' => 'max_attempts_reached',
                        'attempts' => $attempts,
                        'last_error' => (string)($msg['last_error'] ?? ''),
                        'moved_at' => gmdate('c'),
                        'payload' => $msg
                    ];
                    if (!$opts['dry_run']) {
                        rtdbPut($RTDB_URL, '/cred_messages_dead/' . rawurlencode((string)$bookId) . '/' . rawurlencode((string)$alumnoToken) . '/' . rawurlencode((string)$msgId) . '.json', $deadPayload, $dbToken['access_token']);
                        rtdbDelete($RTDB_URL, '/cred_messages/' . rawurlencode((string)$bookId) . '/' . rawurlencode((string)$alumnoToken) . '/' . rawurlencode((string)$msgId) . '.json', $dbToken['access_token']);
                    }
                    $summary['dead_letter']++;
                    $summary['processed']++;
                    $summary['remaining_limit']--;
                    continue;
                }

                $summary['processed']++;
                $summary['remaining_limit']--;

                if ($opts['dry_run']) {
                    continue;
                }

                $dataPayload = [
                    'book_id' => (string)$bookId,
                    'alumno_token' => (string)$alumnoToken,
                    'message_id' => (string)$msgId,
                    'titulo' => (string)($msg['titulo'] ?? ''),
                    'mensaje' => (string)($msg['mensaje'] ?? ''),
                    'type' => 'credencial_batch'
                ];
                $notificationPayload = [
                    'title' => (string)($msg['titulo'] ?? 'Notificación'),
                    'body' => (string)($msg['mensaje'] ?? '')
                ];

                $send = sendFcmToRegisteredDevices(
                    $RTDB_URL,
                    $dbToken['access_token'],
                    $PROJECT_ID,
                    $fcmToken['access_token'],
                    (string)$bookId,
                    (string)$alumnoToken,
                    $dataPayload,
                    $notificationPayload
                );

                if ($send['ok']) {
                    $donePayload = [
                        'book_id' => (string)$bookId,
                        'alumno_token' => (string)$alumnoToken,
                        'message_id' => (string)$msgId,
                        'sent_at' => gmdate('c'),
                        'fcm_name' => (string)($send['name'] ?? ''),
                        'delivery' => [
                            'mode' => (string)($send['mode'] ?? ''),
                            'sent_tokens' => (int)($send['sent_tokens'] ?? 0),
                            'failed_tokens' => (int)($send['failed_tokens'] ?? 0)
                        ],
                        'attempts' => $attempts + 1
                    ];

                    rtdbPut($RTDB_URL, '/cred_messages_done/' . rawurlencode((string)$bookId) . '/' . rawurlencode((string)$alumnoToken) . '/' . rawurlencode((string)$msgId) . '.json', $donePayload, $dbToken['access_token']);
                    rtdbDelete($RTDB_URL, '/cred_messages/' . rawurlencode((string)$bookId) . '/' . rawurlencode((string)$alumnoToken) . '/' . rawurlencode((string)$msgId) . '.json', $dbToken['access_token']);
                    $summary['sent']++;
                } else {
                    $failReason = (string)($send['error'] ?? 'error_desconocido');
                    $failPatch = [
                        'attempts' => $attempts + 1,
                        'last_error' => $failReason,
                        'last_attempt_at' => gmdate('c')
                    ];
                    rtdbPatch($RTDB_URL, '/cred_messages/' . rawurlencode((string)$bookId) . '/' . rawurlencode((string)$alumnoToken) . '/' . rawurlencode((string)$msgId) . '.json', $failPatch, $dbToken['access_token']);
                    $summary['failed']++;
                    if (count($summary['errors']) < 10) {
                        $summary['errors'][] = 'book_id=' . $bookId . ' alumno=' . $alumnoToken . ' msg=' . $msgId . ' reason=' . $failReason;
                    }
                }
            }
        }
    }
} catch (Throwable $e) {
    $summary['ok'] = false;
    $summary['errors'][] = $e->getMessage();
}

$summary['ended_at'] = gmdate('c');
respond($opts['is_cli'], $summary);

flock($lockHandle, LOCK_UN);
fclose($lockHandle);

function readOptions(array $argv): array
{
    $isCli = (PHP_SAPI === 'cli');
    $limit = 50;
    $maxAttempts = 3;
    $dryRun = false;
    $key = '';

    if ($isCli) {
        foreach ($argv as $arg) {
            if (strpos($arg, '--limit=') === 0) {
                $limit = (int)substr($arg, 8);
            } elseif (strpos($arg, '--max-attempts=') === 0) {
                $maxAttempts = (int)substr($arg, 15);
            } elseif (strpos($arg, '--dry-run=') === 0) {
                $dryRun = ((string)substr($arg, 10) === '1');
            }
        }
    } else {
        $limit = (int)($_GET['limit'] ?? 50);
        $maxAttempts = (int)($_GET['max_attempts'] ?? 3);
        $dryRun = ((string)($_GET['dry_run'] ?? '0') === '1');
        $key = (string)($_GET['key'] ?? '');
    }

    return [
        'is_cli' => $isCli,
        'limit' => max(1, min(300, $limit)),
        'max_attempts' => max(1, min(10, $maxAttempts)),
        'dry_run' => $dryRun,
        'key' => $key
    ];
}

function buildAlumniTopic(string $bookId, string $alumnoToken): string
{
    $cleanBook = preg_replace('/[^a-zA-Z0-9_-]/', '_', $bookId);
    $cleanAl = preg_replace('/[^a-zA-Z0-9_-]/', '_', $alumnoToken);
    return 'alumno_' . $cleanBook . '_' . $cleanAl;
}

function sendFcmToRegisteredDevices(
    string $rtdbUrl,
    string $dbAccessToken,
    string $projectId,
    string $fcmAccessToken,
    string $bookId,
    string $alumnoToken,
    array $dataPayload,
    array $notification
): array {
    $path = '/push_devices/' . rawurlencode($bookId) . '/' . rawurlencode($alumnoToken) . '.json';
    $devicesRes = rtdbGet($rtdbUrl, $path, $dbAccessToken);
    $devices = ($devicesRes['ok'] && is_array($devicesRes['data'])) ? $devicesRes['data'] : [];

    $sent = 0;
    $failed = 0;
    $firstName = '';
    foreach ($devices as $dev) {
        if (!is_array($dev) || empty($dev['token']) || (isset($dev['active']) && !$dev['active'])) {
            continue;
        }
        $res = sendFcmV1ToToken($projectId, $fcmAccessToken, (string)$dev['token'], $dataPayload, $notification);
        if (!empty($res['ok'])) {
            $sent++;
            if ($firstName === '' && !empty($res['name'])) {
                $firstName = (string)$res['name'];
            }
        } else {
            $failed++;
        }
    }

    if ($sent > 0) {
        return [
            'ok' => true,
            'name' => $firstName,
            'mode' => 'device_tokens',
            'sent_tokens' => $sent,
            'failed_tokens' => $failed
        ];
    }

    return [
        'ok' => false,
        'error' => $failed > 0 ? 'all_device_tokens_failed' : 'no_registered_devices',
        'mode' => 'none',
        'sent_tokens' => 0,
        'failed_tokens' => $failed
    ];
}

function sendFcmV1ToToken(string $projectId, string $accessToken, string $token, array $dataPayload, array $notification): array
{
    if (!function_exists('curl_init')) {
        return ['ok' => false, 'error' => 'curl no disponible'];
    }

    $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
    $channelId = 'cobaed_alerts_v1';
    $body = [
        'message' => [
            'token' => $token,
            'data' => $dataPayload,
            'notification' => [
                'title' => (string)($notification['title'] ?? ''),
                'body' => (string)($notification['body'] ?? '')
            ],
            'android' => [
                'priority' => 'HIGH',
                'notification' => [
                    'channel_id' => $channelId,
                    'sound' => 'default',
                    'default_sound' => true,
                    'default_vibrate_timings' => true,
                    'notification_priority' => 'PRIORITY_HIGH'
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($body),
        CURLOPT_TIMEOUT => 20
    ]);

    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($resp === false) {
        return ['ok' => false, 'error' => $err !== '' ? $err : 'curl error'];
    }

    $json = json_decode($resp, true);
    if ($http < 200 || $http >= 300) {
        return ['ok' => false, 'error' => 'http ' . $http . ': ' . $resp];
    }

    return ['ok' => true, 'name' => (string)($json['name'] ?? '')];
}

function sendFcmV1ToTopic(string $projectId, string $accessToken, string $topic, array $dataPayload, array $notification): array
{
    if (!function_exists('curl_init')) {
        return ['ok' => false, 'error' => 'curl no disponible'];
    }

    $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';
    $channelId = 'cobaed_alerts_v1';
    $body = [
        'message' => [
            'topic' => $topic,
            'data' => $dataPayload,
            'notification' => [
                'title' => (string)($notification['title'] ?? ''),
                'body' => (string)($notification['body'] ?? '')
            ],
            'android' => [
                'priority' => 'HIGH',
                'notification' => [
                    'channel_id' => $channelId,
                    'sound' => 'default',
                    'default_sound' => true,
                    'default_vibrate_timings' => true,
                    'notification_priority' => 'PRIORITY_HIGH'
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($body),
        CURLOPT_TIMEOUT => 20
    ]);

    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($resp === false) {
        return ['ok' => false, 'error' => $err !== '' ? $err : 'curl error'];
    }

    $json = json_decode($resp, true);
    if ($http < 200 || $http >= 300) {
        return ['ok' => false, 'error' => 'http ' . $http . ': ' . $resp];
    }

    return ['ok' => true, 'name' => (string)($json['name'] ?? '')];
}

function getGoogleAccessToken(string $serviceAccountPath, string $scope): array
{
    if (!function_exists('openssl_sign') || !function_exists('curl_init')) {
        return ['ok' => false, 'error' => 'openssl/curl no disponible'];
    }

    if (!file_exists($serviceAccountPath)) {
        return ['ok' => false, 'error' => 'service account no encontrado'];
    }

    $sa = json_decode((string)file_get_contents($serviceAccountPath), true);
    if (!$sa || empty($sa['client_email']) || empty($sa['private_key']) || empty($sa['token_uri'])) {
        return ['ok' => false, 'error' => 'service account inválido'];
    }

    $now = time();
    $jwtHeader = ['alg' => 'RS256', 'typ' => 'JWT'];
    $jwtClaim = [
        'iss' => $sa['client_email'],
        'scope' => $scope,
        'aud' => $sa['token_uri'],
        'iat' => $now,
        'exp' => $now + 3600
    ];

    $base = b64url(json_encode($jwtHeader)) . '.' . b64url(json_encode($jwtClaim));
    $signature = '';
    $okSign = openssl_sign($base, $signature, $sa['private_key'], OPENSSL_ALGO_SHA256);
    if (!$okSign) {
        return ['ok' => false, 'error' => 'no se pudo firmar JWT'];
    }

    $jwt = $base . '.' . b64url($signature);
    $post = http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]);

    $ch = curl_init($sa['token_uri']);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_TIMEOUT => 20
    ]);

    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($resp === false) {
        return ['ok' => false, 'error' => $err !== '' ? $err : 'error oauth'];
    }
    if ($http < 200 || $http >= 300) {
        return ['ok' => false, 'error' => $resp];
    }

    $json = json_decode($resp, true);
    if (empty($json['access_token'])) {
        return ['ok' => false, 'error' => 'access_token ausente'];
    }

    return ['ok' => true, 'access_token' => (string)$json['access_token']];
}

function rtdbGet(string $rtdbUrl, string $pathWithQuery, string $accessToken): array
{
    $res = rtdbRequest('GET', $rtdbUrl, $pathWithQuery, null, $accessToken);
    if (!$res['ok']) {
        return ['ok' => false, 'error' => $res['error'] ?? 'rtdb get fail'];
    }
    $decoded = json_decode((string)$res['body'], true);
    return ['ok' => true, 'data' => $decoded];
}

function rtdbPut(string $rtdbUrl, string $pathWithQuery, array $payload, string $accessToken): array
{
    return rtdbRequest('PUT', $rtdbUrl, $pathWithQuery, $payload, $accessToken);
}

function rtdbPatch(string $rtdbUrl, string $pathWithQuery, array $payload, string $accessToken): array
{
    return rtdbRequest('PATCH', $rtdbUrl, $pathWithQuery, $payload, $accessToken);
}

function rtdbDelete(string $rtdbUrl, string $pathWithQuery, string $accessToken): array
{
    return rtdbRequest('DELETE', $rtdbUrl, $pathWithQuery, null, $accessToken);
}

function rtdbRequest(string $method, string $rtdbUrl, string $pathWithQuery, ?array $payload, string $accessToken): array
{
    if (!function_exists('curl_init')) {
        return ['ok' => false, 'error' => 'curl no disponible'];
    }

    $url = rtrim($rtdbUrl, '/') . $pathWithQuery;
    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    $ch = curl_init($url);
    $opts = [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 20
    ];
    if ($payload !== null) {
        $opts[CURLOPT_POSTFIELDS] = json_encode($payload);
    }
    curl_setopt_array($ch, $opts);

    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($resp === false) {
        return ['ok' => false, 'error' => $err !== '' ? $err : 'curl error'];
    }
    if ($http < 200 || $http >= 300) {
        return ['ok' => false, 'error' => 'http ' . $http . ': ' . $resp, 'body' => $resp];
    }

    return ['ok' => true, 'body' => $resp];
}

function b64url(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function resolveServiceAccountPath(): string
{
    $env = getenv('COBAED_FIREBASE_SERVICE_ACCOUNT');
    if ($env && file_exists($env)) {
        return $env;
    }

    $candidates = [
        __DIR__ . '/cobaedlomas-service-account.json',
        __DIR__ . '/../../cam/cobaedlomas-firebase-adminsdk-fbsvc-69bc07f243.json',
        __DIR__ . '/../../cam/cobaedlomas-firebase-adminsdk-fbsvc-896c424477.json',
        __DIR__ . '/../../cobaedlomas-firebase-adminsdk-fbsvc-896c424477.json',
        __DIR__ . '/../../../cobaedlomas-firebase-adminsdk-fbsvc-896c424477.json'
    ];
    foreach ($candidates as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }

    $patterns = [
        __DIR__ . '/cobaedlomas-service-account*.json',
        __DIR__ . '/../../cam/cobaedlomas-firebase-adminsdk-*.json',
        __DIR__ . '/../../cobaedlomas-firebase-adminsdk-*.json',
        __DIR__ . '/../../../cobaedlomas-firebase-adminsdk-*.json'
    ];
    foreach ($patterns as $pattern) {
        $found = glob($pattern);
        if ($found && !empty($found[0])) {
            return $found[0];
        }
    }

    return __DIR__ . '/../../cam/cobaedlomas-firebase-adminsdk-fbsvc-69bc07f243.json';
}

function respond(bool $isCli, array $payload): void
{
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($isCli) {
        echo $json . PHP_EOL;
        return;
    }
    echo $json;
}
