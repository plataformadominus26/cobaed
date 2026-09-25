<?php
// Copia este archivo como config.local.php en cada plantel y llena los valores.
// config.local.php NO se sube a git.
return [
    'plantel' => [
        'nombre'       => 'Cobaed Lomas',
        'nombre_largo' => 'Lomas (Plantel 09)',
    ],
    'db' => [
        'host' => 'localhost',
        'user' => '',
        'pwd'  => '',
        'name' => '',
    ],
    'db_sebised_pwd' => '',
    'gemini_api_key' => '',
    'firebase' => [
        'service_account'   => '/home/USUARIO/secure/firebase-service-account.json',
        'consumer_key_file' => '/home/USUARIO/secure/consume_cred_messages.key',
    ],
    'logs_dir' => '/home/USUARIO/logs/cobaed',
];
