<?php
namespace App\Controllers;

use App\Core\DB;

class AuthController extends BaseController {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance()->getConnection();
    }

    public function showLogin() {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth/login', ['csrf_token' => $this->csrf()]);
    }

    public function login() {
        try {
            $this->validateCsrf();

            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            error_log("Tentativa de login - Email: " . $email);

            if (!$email || !$password) {
                $_SESSION['error'] = 'Por favor, preencha todos os campos.';
                $this->redirect('/login');
                return;
            }

            // Verificar se o usuário existe
            $stmt = $this->db->prepare('SELECT id, email, password FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            error_log("Usuário encontrado: " . ($user ? "Sim" : "Não"));
            
            if ($user) {
                error_log("Hash da senha no banco: " . $user['password']);
                error_log("Senha fornecida: " . $password);
                
                // Criar o mesmo hash para comparação
                $testHash = password_hash('admin123', PASSWORD_DEFAULT);
                error_log("Hash de teste com admin123: " . $testHash);
                
                $verified = password_verify($password, $user['password']);
                error_log("Verificação da senha: " . ($verified ? "Sucesso" : "Falha"));
            }

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $this->redirect('/dashboard');
            } else {
                $_SESSION['error'] = 'Email ou senha incorretos.';
                $this->redirect('/login');
            }
        } catch (\Exception $e) {
            error_log("Erro no login: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error'] = 'Erro ao realizar login. Por favor, tente novamente.';
            $this->redirect('/login');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}
