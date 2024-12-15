<?php
/**
 * Arquivo principal de entrada da aplicação
 * Responsável por inicializar o sistema e gerenciar as rotas
 */

session_start();

// Definir constantes do sistema
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Carregar o autoloader do Composer
require BASE_PATH . '/vendor/autoload.php';

// Carregar configurações
$config = require CONFIG_PATH . '/config.php';

// Inicializar o roteador
require BASE_PATH . '/routes/web.php';

// Tratar erros e exceções
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Iniciar a aplicação
try {
    App\Core\Application::getInstance()->run();
} catch (Exception $e) {
    if ($config['app']['debug']) {
        echo '<pre>';
        echo $e->getMessage() . "\n";
        echo $e->getTraceAsString();
        echo '</pre>';
    } else {
        // Log do erro
        error_log($e->getMessage());
        // Redirecionar para página de erro
        header('Location: /error');
    }
}
