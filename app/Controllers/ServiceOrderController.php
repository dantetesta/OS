<?php
namespace App\Controllers;

use App\Core\DB;
use PDOException;

class ServiceOrderController extends BaseController {
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

            // Primeiro, vamos contar quantas ordens existem no total
            $countStmt = $this->db->query('SELECT COUNT(*) as total FROM service_orders');
            $totalCount = $countStmt->fetch()['total'];
            error_log("Total de ordens no banco: " . $totalCount);

            $search = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';
            $status = isset($_GET['status']) ? htmlspecialchars(trim($_GET['status'])) : '';
            
            // Query completa com LEFT JOIN para garantir que todas as ordens sejam retornadas
            $query = "
                SELECT 
                    so.*,
                    c.name as client_name,
                    c.email as client_email,
                    c.phone as client_phone
                FROM 
                    service_orders so
                    LEFT JOIN clients c ON c.id = so.client_id
                WHERE 1=1
            ";
            
            $params = [];
            
            // Adiciona condições de busca apenas se houver termos de busca
            if (!empty($search)) {
                $query .= " AND (
                    so.title LIKE ? OR 
                    so.description LIKE ? OR 
                    c.name LIKE ?
                )";
                $searchTerm = "%{$search}%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
            }
            
            if (!empty($status)) {
                $query .= " AND so.status = ?";
                $params[] = $status;
            }
            
            // Ordena por ID decrescente para mostrar as mais recentes primeiro
            $query .= " ORDER BY so.id DESC";
            
            // Debug da query
            error_log("Query SQL: " . $query);
            error_log("Parâmetros: " . json_encode($params));
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $orders = $stmt->fetchAll();
            
            // Debug detalhado dos resultados
            error_log("Número de resultados retornados: " . count($orders));
            if (!empty($orders)) {
                foreach ($orders as $index => $order) {
                    error_log("Ordem #{$index}:");
                    error_log("- ID: " . ($order['id'] ?? 'não definido'));
                    error_log("- Cliente: " . ($order['client_name'] ?? 'não definido'));
                    error_log("- Título: " . ($order['title'] ?? 'não definido'));
                    error_log("- Status: " . ($order['status'] ?? 'não definido'));
                    error_log("- Prioridade: " . ($order['priority'] ?? 'não definido'));
                    error_log("- Data Início: " . ($order['start_date'] ?? 'não definido'));
                    error_log("- Data Fim: " . ($order['end_date'] ?? 'não definido'));
                    error_log("- Valor: " . ($order['value'] ?? 'não definido'));
                }
            } else {
                error_log("Nenhuma ordem retornada da query");
            }
            
            // Debug da conexão PDO
            if ($stmt->errorInfo()[0] !== '00000') {
                error_log("PDO Error Info: " . json_encode($stmt->errorInfo()));
            }
            
            return $this->view('service_orders/index', [
                'orders' => $orders,
                'search' => $search,
                'status' => $status,
                'csrf_token' => $this->csrf()
            ]);
        } catch (PDOException $e) {
            error_log("Erro detalhado ao buscar ordens de serviço: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            $_SESSION['error'] = 'Erro ao carregar a lista de ordens de serviço: ' . $e->getMessage();
            return $this->view('service_orders/index', [
                'orders' => [],
                'search' => $search ?? '',
                'status' => $status ?? '',
                'csrf_token' => $this->csrf()
            ]);
        }
    }

    public function create() {
        try {
            $clients = $this->getClients();
            
            $this->view('service_orders/form', [
                'clients' => $clients,
                'csrf_token' => $this->csrf()
            ]);
        } catch (\PDOException $e) {
            error_log("Erro ao carregar formulário de O.S: " . $e->getMessage());
            $_SESSION['error'] = 'Erro ao carregar o formulário.';
            $this->redirect('/os');
        }
    }

    public function store() {
        try {
            $this->validateCsrf();
            
            $data = $this->validateOrderData();
            if (isset($data['error'])) {
                $_SESSION['error'] = $data['error'];
                $this->redirect('/os/nova');
                return;
            }

            // Debug dos dados antes de inserir
            error_log("Dados a serem inseridos: " . json_encode($data));

            $stmt = $this->db->prepare('
                INSERT INTO service_orders 
                (client_id, title, description, status, priority, start_date, end_date, value) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');

            $result = $stmt->execute([
                $data['client_id'],
                $data['title'],
                $data['description'],
                $data['status'] ?: 'pending',
                $data['priority'] ?: 'low',
                $data['start_date'],
                $data['end_date'],
                $data['value']
            ]);

            // Debug do resultado da inserção
            error_log("Resultado da inserção: " . ($result ? "sucesso" : "falha"));
            if (!$result) {
                error_log("Erro PDO: " . json_encode($stmt->errorInfo()));
            }

            $_SESSION['success'] = 'Ordem de serviço cadastrada com sucesso!';
            $this->redirect('/os');
            
        } catch (PDOException $e) {
            error_log("Erro ao cadastrar O.S: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error'] = 'Erro ao cadastrar ordem de serviço: ' . $e->getMessage();
            $this->redirect('/os/nova');
        }
    }

    public function edit($id) {
        try {
            $stmt = $this->db->prepare('SELECT * FROM service_orders WHERE id = ?');
            $stmt->execute([$id]);
            $order = $stmt->fetch();

            if (!$order) {
                $_SESSION['error'] = 'Ordem de serviço não encontrada.';
                $this->redirect('/os');
                return;
            }

            $clients = $this->getClients();

            $this->view('service_orders/form', [
                'order' => $order,
                'clients' => $clients,
                'csrf_token' => $this->csrf()
            ]);
        } catch (\PDOException $e) {
            error_log("Erro ao buscar O.S: " . $e->getMessage());
            $_SESSION['error'] = 'Erro ao carregar dados da ordem de serviço.';
            $this->redirect('/os');
        }
    }

    public function update($id) {
        try {
            $this->validateCsrf();
            
            $data = $this->validateOrderData();
            if (isset($data['error'])) {
                $_SESSION['error'] = $data['error'];
                $this->redirect("/os/{$id}");
                return;
            }

            $stmt = $this->db->prepare('
                UPDATE service_orders 
                SET client_id = ?, title = ?, description = ?, status = ?, 
                    priority = ?, start_date = ?, end_date = ?, value = ?
                WHERE id = ?
            ');

            $stmt->execute([
                $data['client_id'],
                $data['title'],
                $data['description'],
                $data['status'],
                $data['priority'],
                $data['start_date'],
                $data['end_date'],
                $data['value'],
                $id
            ]);

            $_SESSION['success'] = 'Ordem de serviço atualizada com sucesso!';
            $this->redirect('/os');
            
        } catch (\PDOException $e) {
            error_log("Erro ao atualizar O.S: " . $e->getMessage());
            $_SESSION['error'] = 'Erro ao atualizar ordem de serviço.';
            $this->redirect("/os/{$id}");
        }
    }

    public function delete($id) {
        try {
            $this->validateCsrf();

            $stmt = $this->db->prepare('DELETE FROM service_orders WHERE id = ?');
            $stmt->execute([$id]);
            $_SESSION['success'] = 'Ordem de serviço removida com sucesso!';
            
        } catch (\PDOException $e) {
            error_log("Erro ao excluir O.S: " . $e->getMessage());
            $_SESSION['error'] = 'Erro ao remover ordem de serviço.';
        }

        $this->redirect('/os');
    }

    private function getClients() {
        $stmt = $this->db->query('SELECT id, name FROM clients ORDER BY name');
        return $stmt->fetchAll();
    }

    private function validateOrderData() {
        // Sanitização moderna usando trim e htmlspecialchars
        $client_id = isset($_POST['client_id']) ? filter_var($_POST['client_id'], FILTER_SANITIZE_NUMBER_INT) : '';
        $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
        $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : '';
        $status = isset($_POST['status']) ? htmlspecialchars(trim($_POST['status'])) : 'pending';
        $priority = isset($_POST['priority']) ? htmlspecialchars(trim($_POST['priority'])) : 'low';
        $start_date = isset($_POST['start_date']) ? htmlspecialchars(trim($_POST['start_date'])) : '';
        $end_date = isset($_POST['end_date']) ? htmlspecialchars(trim($_POST['end_date'])) : null;
        
        // Tratamento especial para o valor
        $value = isset($_POST['value']) ? $_POST['value'] : '0';
        $value = str_replace('.', '', $value); // Remove pontos
        $value = str_replace(',', '.', $value); // Troca vírgula por ponto
        $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        // Debug dos dados recebidos
        error_log("Dados do formulário:");
        error_log("- client_id: " . $client_id);
        error_log("- title: " . $title);
        error_log("- status: " . $status);
        error_log("- priority: " . $priority);
        error_log("- start_date: " . $start_date);
        error_log("- end_date: " . $end_date);
        error_log("- value: " . $value);

        // Validações
        if (empty($client_id)) {
            return ['error' => 'O cliente é obrigatório.'];
        }

        if (empty($title)) {
            return ['error' => 'O título é obrigatório.'];
        }

        if (!empty($start_date) && !strtotime($start_date)) {
            return ['error' => 'Data de início inválida.'];
        }

        if (!empty($end_date) && !strtotime($end_date)) {
            return ['error' => 'Data de término inválida.'];
        }

        if (!empty($end_date) && !empty($start_date) && strtotime($end_date) < strtotime($start_date)) {
            return ['error' => 'A data de término deve ser maior que a data de início.'];
        }

        return [
            'client_id' => $client_id,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'priority' => $priority,
            'start_date' => $start_date ?: null,
            'end_date' => $end_date ?: null,
            'value' => $value ?: 0
        ];
    }
}
