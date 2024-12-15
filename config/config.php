<?php

/**
 * Contém todas as configurações necessárias para o funcionamento do sistema
 */

require_once __DIR__ . '/../app/Helpers/functions.php';

return [
    'app' => [
        'name' => env('APP_NAME'),
        'url' => env('APP_URL'),
        'debug' => env('APP_DEBUG')
    ],
    'database' => [
        'host' => env('DB_HOST'),
        'dbname' => env('DB_NAME'),
        'username' => env('DB_USER'),
        'password' => env('DB_PASS'),
        'charset' => env('DB_CHARSET')
    ],
    'mail' => [
        'host' => env('MAIL_HOST'),
        'username' => env('MAIL_USER'),
        'password' => env('MAIL_PASS'),
        'port' => env('MAIL_PORT'),
        'encryption' => env('MAIL_ENCRYPTION')
    ]
];
