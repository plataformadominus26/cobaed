<?php

/**
 * Servicio reusable para encolar push en RTDB y disparar consumer FCM.
 * Diseñado para ser incluido desde cualquier PHP del proyecto.
 */

function cobaedPushCreateMessageId()
{
    return date('YmdHis') . '_' . bin2hex(random_bytes(3));
}

function cobaedPushEnqueueMessageRtdb($rtdbUrl, $serviceAccountPath, $bookId, $alumnoToken, $msgId, $titulo, $mensaje, $fromToken)
{
    $payload = array(
        "message_id" => (string)$msgId,
        "book_id" => (string)$bookId,
        "alumno_token" => (string)$alumnoToken,
        "titulo" => (string)$titulo,
        "mensaje" => (string)$mensaje,
        "from" => (string)$fromToken,
        "created_at" => date('c')
    );

    $path = '/cred_messages/'
        . rawurlencode((string)$bookId) . '/'
        . rawurlencode((string)$alumnoToken) . '/'
        . rawurlencode((string)$msgId) . '.json';

    return cobaedPushRtdbPutWithServiceAccount($rtdbUrl, $serviceAccountPath, $path, $payload);
}

function cobaedPushTriggerConsumerAsync($consumerUrl, $consumerKey, $limit)
{
    if (!function_exists('curl_init')) {
        return array('ok' => false, 'triggered' => false, 'error' => 'curl_no_disponible');
    }

    if ((string)$consumerKey === '') {
        return array('ok' => false, 'triggered' => false, 'error' => 'consumer_key_vacia');
    }

    $limit = max(1, min(300, (int)$limit));
    $url = $consumerUrl
        . '?key=' . rawurlencode((string)$consumerKey)
        . '&limit=' . $limit
        . '&dry_run=0';

    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT_MS => 400,
        CURLOPT_TIMEOUT_MS => 1200,
        CURLOPT_HTTPGET => true
    ));

    $resp = curl_exec($ch);
    $err = curl_error($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resp === false) {
        // Se considera disparado aunque no esperemos respuesta completa.
        return array('ok' => true, 'triggered' => true, 'note' => 'timeout_or_no_response', 'curl_error' => $err);
    }

    $json = json_decode($resp, true);
    return array(
        'ok' => ($http >= 200 && $http < 300),
        'triggered' => true,
        'http' => $http,
        'response' => is_array($json) ? $json : $resp
    );
}

function cobaedPushReadConsumerKey($alumniDir)
{
    $env = getenv('COBAED_FCM_CONSUMER_KEY');
    if ($env) {
        return trim($env);
    }

    $files = array(
        rtrim($alumniDir, '/\\') . '/consume_cred_messages.key',
        rtrim($alumniDir, '/\\') . '/.consume_cred_messages.key',
        rtrim($alumniDir, '/\\') . '/consume_cred_messages.secret',
        rtrim($alumniDir, '/\\') . '/.consume_cred_messages.secret'
    );

    foreach ($files as $f) {
        if (file_exists($f)) {
            $v = trim((string)@file_get_contents($f));
            if ($v !== '') {
                return $v;
            }
        }
    }

    return '';
}

function cobaedPushRtdbPutWithServiceAccount($rtdbUrl, $serviceAccountPath, $path, $payload)
{
    if (!function_exists('curl_init')) {
        return false;
    }

    $scope = 'https://www.googleapis.com/auth/firebase.database https://www.googleapis.com/auth/userinfo.email';
    $tokenRes = cobaedPushGetGoogleAccessTokenLocal($serviceAccountPath, $scope);
    if (empty($tokenRes['ok'])) {
        return false;
    }

    $url = rtrim($rtdbUrl, '/') . $path;
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $tokenRes['access_token'],
            'Content-Type: application/json'
        ),
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 20
    ));

    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $resp !== false && $http >= 200 && $http < 300;
}

function cobaedPushGetGoogleAccessTokenLocal($serviceAccountPath, $scope)
{
    if (!function_exists('openssl_sign') || !function_exists('curl_init')) {
        return array('ok' => false, 'error' => 'openssl/curl no disponible');
    }
    if (!file_exists($serviceAccountPath)) {
        return array('ok' => false, 'error' => 'service account no encontrado');
    }

    $sa = json_decode((string)file_get_contents($serviceAccountPath), true);
    if (!$sa || empty($sa['client_email']) || empty($sa['private_key']) || empty($sa['token_uri'])) {
        return array('ok' => false, 'error' => 'service account inválido');
    }

    $now = time();
    $jwtHeader = array('alg' => 'RS256', 'typ' => 'JWT');
    $jwtClaim = array(
        'iss' => $sa['client_email'],
        'scope' => $scope,
        'aud' => $sa['token_uri'],
        'iat' => $now,
        'exp' => $now + 3600
    );

    $base = cobaedPushB64urlLocal(json_encode($jwtHeader)) . '.' . cobaedPushB64urlLocal(json_encode($jwtClaim));
    $signature = '';
    $okSign = openssl_sign($base, $signature, $sa['private_key'], OPENSSL_ALGO_SHA256);
    if (!$okSign) {
        return array('ok' => false, 'error' => 'no se pudo firmar JWT');
    }

    $jwt = $base . '.' . cobaedPushB64urlLocal($signature);
    $post = http_build_query(array(
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ));

    $ch = curl_init($sa['token_uri']);
    curl_setopt_array($ch, array(
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_TIMEOUT => 20
    ));

    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resp === false || $http < 200 || $http >= 300) {
        return array('ok' => false, 'error' => 'token oauth inválido');
    }

    $json = json_decode($resp, true);
    if (empty($json['access_token'])) {
        return array('ok' => false, 'error' => 'access_token ausente');
    }

    return array('ok' => true, 'access_token' => $json['access_token']);
}

function cobaedPushB64urlLocal($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
