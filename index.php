<?php
session_start();

// Definir constante com o caminho base do projeto
define('BASE_PATH', __DIR__);

// Carregar o autoloader do Composer
require_once __DIR__ . '/vendor/autoload.php';

// Carregar configurações
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/layout.php';

// Carregar rotas
require_once __DIR__ . '/routes/web.php';

// Iniciar o roteador
$router = new App\Core\Router();
$router->dispatch();
