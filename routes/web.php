<?php
/**
 * Arquivo de definição das rotas da aplicação
 */

use App\Core\Router;
use App\Controllers\{
    AuthController,
    DashboardController,
    ClientController,
    ServiceOrderController
};

$router = Router::getInstance();

// Rotas de autenticação
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Rota raiz redireciona para login
$router->get('/', [AuthController::class, 'showLogin']);

// Rotas do Dashboard
$router->get('/dashboard', [DashboardController::class, 'index']);

// Rotas de Clientes
$router->get('/clientes', [ClientController::class, 'index']);
$router->get('/clientes/novo', [ClientController::class, 'create']);
$router->post('/clientes', [ClientController::class, 'store']);
$router->get('/clientes/{id}', [ClientController::class, 'edit']);
$router->put('/clientes/{id}', [ClientController::class, 'update']);
$router->delete('/clientes/{id}', [ClientController::class, 'delete']);

// Rotas de Ordens de Serviço
$router->get('/os', [ServiceOrderController::class, 'index']);
$router->get('/os/nova', [ServiceOrderController::class, 'create']);
$router->post('/os', [ServiceOrderController::class, 'store']);
$router->get('/os/{id}', [ServiceOrderController::class, 'edit']);
$router->put('/os/{id}', [ServiceOrderController::class, 'update']);
$router->delete('/os/{id}', [ServiceOrderController::class, 'delete']);
