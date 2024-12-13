<?php
namespace App\Controllers;

/**
 * Controlador base com métodos comuns a todos os controllers
 */
class BaseController {
    protected function view($name, $data = []) {
        // Extrair os dados para serem acessíveis na view
        extract($data);
        
        // Incluir as funções de layout
        require_once BASE_PATH . '/config/layout.php';
        
        // Renderizar o cabeçalho
        renderHeader();
        
        // Incluir a view específica
        require BASE_PATH . "/app/Views/{$name}.php";
        
        // Renderizar o rodapé
        renderFooter();
    }

    protected function redirect($path) {
        header("Location: {$path}");
        exit;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->redirect('/login');
        }
    }

    protected function csrf() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function validateCsrf() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new \Exception('CSRF token validation failed');
        }
    }
}
