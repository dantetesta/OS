<?php
/**
 * Arquivo de configuração principal
 * Contém todas as configurações necessárias para o funcionamento do sistema
 */

return [
    'database' => [
        'host' => '187.33.241.61',
        'dbname' => 'wprevolu_laravel',
        'username' => 'wprevolu_laravel',
        'password' => 'laravel@25',
        'charset' => 'utf8mb4'
    ],
    'mail' => [
        'host' => 'mail.wprevolution.com.br',
        'username' => 'no-reply@wprevolution.com.br',
        'password' => 'facil@25',
        'port' => 465,
        'encryption' => 'ssl'
    ],
    'app' => [
        'name' => 'Sistema OS',
        'url' => 'http://localhost', // Alterar para URL de produção
        'debug' => true
    ]
];
