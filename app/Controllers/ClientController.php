<?php
namespace App\Controllers;

use App\Core\DB;
use PDOException;

class ClientController extends BaseController {
    private $db;

    public function __construct() {
        try {
            $this->db = DB::getInstance()->getConnection();
            $this->requireAuth();
        } catch (PDOException $e) {
            error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
            $_SESSION['error'] = 'Erro na conexão com o banco de dados.';
        }
    }

    public function index() {
        try {
            if (!$this->db) {
                throw new PDOException("Conexão com o banco de dados não estabelecida");
            }

            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            error_log("Termo de busca: " . $search); // Log do termo de busca
            
            $query = 'SELECT * FROM clients';
            $params = [];
            
            if ($search !== '') {
                $query .= ' WHERE (name LIKE :search_name OR email LIKE :search_email OR phone LIKE :search_phone)';
                $params['search_name'] = "%{$search}%";
                $params['search_email'] = "%{$search}%";
                $params['search_phone'] = "%{$search}%";
            }
            
            $query .= ' ORDER BY name ASC';
            
            error_log("Query SQL: " . $query); // Log da query
            error_log("Parâmetros: " . json_encode($params)); // Log dos parâmetros
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $clients = $stmt->fetchAll();
            
            error_log("Número de resultados: " . count($clients)); // Log do número de resultados
            
            return $this->view('clients/index', [
                'clients' => $clients,
                'search' => $search,
                'csrf_token' => $this->csrf()
            ]);
        } catch (PDOException $e) {
            error_log("Erro detalhado ao buscar clientes: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            $_SESSION['error'] = 'Erro ao carregar a lista de clientes: ' . $e->getMessage();
            return $this->view('clients/index', [
                'clients' => [],
                'search' => $search ?? '',
                'csrf_token' => $this->csrf()
            ]);
        }
    }

    public function create() {
        $this->view('clients/form', [
            'csrf_token' => $this->csrf()
        ]);
    }

    public function store() {
        try {
            $this->validateCsrf();
            
            $data = $this->validateClientData();
            if (isset($data['error'])) {
                $_SESSION['error'] = $data['error'];
                $this->redirect('/clientes/novo');
                return;
            }

            $stmt = $this->db->prepare('
                INSERT INTO clients (name, email, phone, address) 
                VALUES (?, ?, ?, ?)
            ');

            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['address']
            ]);

            $_SESSION['success'] = 'Cliente cadastrado com sucesso!';
            $this->redirect('/clientes');
            
        } catch (\PDOException $e) {
            error_log("Erro ao cadastrar cliente: " . $e->getMessage());
            $_SESSION['error'] = 'Erro ao cadastrar cliente.';
            $this->redirect('/clientes/novo');
        }
    }

    public function edit($id) {
        try {
            $stmt = $this->db->prepare('SELECT * FROM clients WHERE id = ?');
            $stmt->execute([$id]);
            $client = $stmt->fetch();

            if (!$client) {
                $_SESSION['error'] = 'Cliente não encontrado.';
                $this->redirect('/clientes');
                return;
            }

            $this->view('clients/form', [
                'client' => $client,
                'csrf_token' => $this->csrf()
            ]);
        } catch (\PDOException $e) {
            error_log("Erro ao buscar cliente: " . $e->getMessage());
            $_SESSION['error'] = 'Erro ao carregar dados do cliente.';
            $this->redirect('/clientes');
        }
    }

    public function update($id) {
        try {
            $this->validateCsrf();
            
            $data = $this->validateClientData();
            if (isset($data['error'])) {
                $_SESSION['error'] = $data['error'];
                $this->redirect("/clientes/{$id}");
                return;
            }

            $stmt = $this->db->prepare('
                UPDATE clients 
                SET name = ?, email = ?, phone = ?, address = ?
                WHERE id = ?
            ');

            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['address'],
                $id
            ]);

            $_SESSION['success'] = 'Cliente atualizado com sucesso!';
            $this->redirect('/clientes');
            
        } catch (\PDOException $e) {
            error_log("Erro ao atualizar cliente: " . $e->getMessage());
            $_SESSION['error'] = 'Erro ao atualizar cliente.';
            $this->redirect("/clientes/{$id}");
        }
    }

    public function delete($id) {
        try {
            $this->validateCsrf();

            // Verificar se existem ordens de serviço vinculadas
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM service_orders WHERE client_id = ?');
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $_SESSION['error'] = 'Não é possível excluir o cliente pois existem ordens de serviço vinculadas.';
                $this->redirect('/clientes');
                return;
            }

            $stmt = $this->db->prepare('DELETE FROM clients WHERE id = ?');
            $stmt->execute([$id]);
            $_SESSION['success'] = 'Cliente removido com sucesso!';
            
        } catch (\PDOException $e) {
            error_log("Erro ao excluir cliente: " . $e->getMessage());
            $_SESSION['error'] = 'Erro ao remover cliente.';
        }

        $this->redirect('/clientes');
    }

    private function validateClientData() {
        // Sanitização moderna usando trim e htmlspecialchars
        $name = trim(htmlspecialchars($_POST['name'] ?? ''));
        $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $phone = trim(preg_replace('/[^0-9]/', '', $_POST['phone'] ?? ''));
        $address = trim(htmlspecialchars($_POST['address'] ?? ''));

        // Validações
        if (empty($name)) {
            return ['error' => 'O nome é obrigatório.'];
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'E-mail inválido.'];
        }

        if (!empty($phone) && strlen($phone) < 10) {
            return ['error' => 'Telefone inválido.'];
        }

        return [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'address' => $address
        ];
    }
}
