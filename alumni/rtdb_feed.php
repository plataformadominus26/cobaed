<?php
header('Content-Type: application/json; charset=utf-8');

$rtdbUrl = 'https://cobaedlomas-default-rtdb.firebaseio.com';
$serviceAccountPath = __DIR__ . '/../../cam/cobaedlomas-firebase-adminsdk-fbsvc-69bc07f243.json';

if (!function_exists('curl_init')) {
    echo json_encode(['ok' => false, 'error' => 'cURL extension not available']);
    exit;
}
if (!function_exists('openssl_sign')) {
    echo json_encode(['ok' => false, 'error' => 'OpenSSL extension not available']);
    exit;
}

$tokenRes = getGoogleAccessToken(
    $serviceAccountPath,
    'https://www.googleapis.com/auth/firebase.database https://www.googleapis.com/auth/userinfo.email'
);
if (empty($tokenRes['ok'])) {
    echo json_encode(['ok' => false, 'error' => 'token_error', 'detail' => $tokenRes]);
    exit;
}

$ch = curl_init(rtrim($rtdbUrl, '/') . '/cam_uploads.json');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $tokenRes['access_token']
    ],
    CURLOPT_TIMEOUT => 20
]);

$resp = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($resp === false) {
    echo json_encode(['ok' => false, 'error' => $err]);
    exit;
}
if ($http < 200 || $http >= 300) {
    echo json_encode(['ok' => false, 'http' => $http, 'response' => $resp]);
    exit;
}

$data = json_decode($resp, true);
if (!is_array($data)) {
    echo json_encode(['ok' => true, 'items' => []]);
    exit;
}

$items = [];
foreach ($data as $estacion => $node) {
    if (!is_array($node)) continue;

    $payload = isset($node['latest']) && is_array($node['latest']) ? $node['latest'] : $node;
    $url = isset($payload['url']) ? $payload['url'] : '';
    $ts = isset($payload['ts']) ? $payload['ts'] : 0;
    $updatedAt = isset($payload['updated_at']) ? $payload['updated_at'] : '';

    if ($url === '') continue;

    $items[] = [
        'estacion' => $estacion,
        'url' => $url,
        'image_url' => isset($payload['image_url']) ? $payload['image_url'] : $url,
        'topic' => isset($payload['topic']) ? $payload['topic'] : '',
        'ts' => $ts,
        'updated_at' => $updatedAt
    ];
}

echo json_encode(['ok' => true, 'items' => $items], JSON_UNESCAPED_UNICODE);

function getGoogleAccessToken($serviceAccountPath, $scope)
{
    if (!file_exists($serviceAccountPath)) {
        return ['ok' => false, 'error' => 'service-account.json not found: ' . $serviceAccountPath];
    }

    $sa = json_decode(file_get_contents($serviceAccountPath), true);
    if (!$sa || empty($sa['client_email']) || empty($sa['private_key']) || empty($sa['token_uri'])) {
        return ['ok' => false, 'error' => 'Invalid service-account.json'];
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
    if (!$okSign) return ['ok' => false, 'error' => 'JWT signing failed'];

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

    if ($resp === false) return ['ok' => false, 'error' => $err];
    if ($http < 200 || $http >= 300) return ['ok' => false, 'error' => $resp];

    $json = json_decode($resp, true);
    if (empty($json['access_token'])) return ['ok' => false, 'error' => 'No access_token in response'];

    return ['ok' => true, 'access_token' => $json['access_token']];
}

function b64url($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
