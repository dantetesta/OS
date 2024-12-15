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
            // Busca a ordem de serviço com informações do cliente
            $stmt = $this->db->prepare('
                SELECT 
                    so.*,
                    c.name as client_name,
                    c.email as client_email,
                    c.phone as client_phone
                FROM service_orders so
                LEFT JOIN clients c ON c.id = so.client_id
                WHERE so.id = ?
            ');
            $stmt->execute([$id]);
            $order = $stmt->fetch();

            if (!$order) {
                $_SESSION['error'] = 'Ordem de serviço não encontrada.';
                $this->redirect('/os');
                return;
            }

            // Busca a lista de clientes para o select
            $clients = $this->getClients();

            // Debug dos dados
            error_log("Dados da OS para edição:");
            error_log(json_encode($order, JSON_PRETTY_PRINT));

            // Renderiza a view de edição
            $this->view('service_orders/edit', [
                'order' => $order,
                'clients' => $clients,
                'csrf_token' => $this->csrf()
            ]);
        } catch (\PDOException $e) {
            error_log("Erro ao carregar O.S para edição: " . $e->getMessage());
            $_SESSION['error'] = 'Erro ao carregar ordem de serviço.';
            $this->redirect('/os');
        }
    }

    public function update($id) {
        try {
            $this->validateCsrf();
            
            // Debug do ID recebido
            error_log("Atualizando OS #" . $id);
            
            $data = $this->validateOrderData();
            if (isset($data['error'])) {
                $_SESSION['error'] = $data['error'];
                $this->redirect("/os/editar/{$id}");
                return;
            }

            // Debug dos dados antes da atualização
            error_log("Dados para atualização da OS #" . $id . ":");
            error_log(json_encode($data, JSON_PRETTY_PRINT));

            // Primeiro, vamos verificar se a OS existe
            $checkStmt = $this->db->prepare('SELECT id FROM service_orders WHERE id = ?');
            $checkStmt->execute([$id]);
            if (!$checkStmt->fetch()) {
                $_SESSION['error'] = 'Ordem de serviço não encontrada.';
                $this->redirect('/os');
                return;
            }

            // Prepara a query de update
            $query = '
                UPDATE service_orders 
                SET client_id = :client_id,
                    title = :title,
                    description = :description,
                    status = :status,
                    priority = :priority,
                    start_date = :start_date,
                    end_date = :end_date,
                    value = :value
                WHERE id = :id
            ';

            $stmt = $this->db->prepare($query);
            
            // Array de valores para o execute usando named parameters
            $values = [
                ':client_id' => $data['client_id'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':status' => $data['status'],
                ':priority' => $data['priority'],
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':value' => $data['value'],
                ':id' => $id
            ];

            // Debug dos valores que serão executados
            error_log("Query SQL: " . $query);
            error_log("Valores para execução do UPDATE:");
            error_log(json_encode($values, JSON_PRETTY_PRINT));

            // Executa o UPDATE
            $result = $stmt->execute($values);

            // Debug do resultado da execução
            if (!$result) {
                error_log("Erro na execução do UPDATE:");
                error_log(json_encode($stmt->errorInfo(), JSON_PRETTY_PRINT));
                throw new \PDOException("Erro ao atualizar ordem de serviço: " . implode(" ", $stmt->errorInfo()));
            } else {
                error_log("UPDATE executado com sucesso. Linhas afetadas: " . $stmt->rowCount());
            }

            $_SESSION['success'] = 'Ordem de serviço atualizada com sucesso!';
            $this->redirect('/os');
            
        } catch (\PDOException $e) {
            error_log("Erro ao atualizar O.S: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error'] = 'Erro ao atualizar ordem de serviço: ' . $e->getMessage();
            $this->redirect("/os/editar/{$id}");
        }
    }

    public function delete($id) {
        try {
            // Debug do ID recebido
            error_log("Tentando excluir OS #" . $id);
            error_log("POST data: " . print_r($_POST, true));
            error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);

            // Valida o token CSRF
            $this->validateCsrf();

            // Primeiro, vamos verificar se a OS existe
            $checkStmt = $this->db->prepare('SELECT id FROM service_orders WHERE id = ?');
            $checkStmt->execute([$id]);
            if (!$checkStmt->fetch()) {
                error_log("OS #" . $id . " não encontrada");
                $_SESSION['error'] = 'Ordem de serviço não encontrada.';
                $this->redirect('/os');
                return;
            }

            // Prepara e executa a query de DELETE
            $stmt = $this->db->prepare('DELETE FROM service_orders WHERE id = ?');
            $result = $stmt->execute([$id]);

            // Debug do resultado
            error_log("Resultado da exclusão da OS #" . $id . ": " . ($result ? 'sucesso' : 'falha'));
            if (!$result) {
                error_log("Erro ao excluir OS: " . json_encode($stmt->errorInfo()));
                throw new \PDOException("Erro ao excluir ordem de serviço");
            }

            $_SESSION['success'] = 'Ordem de serviço removida com sucesso!';
            
        } catch (\PDOException $e) {
            error_log("Erro ao excluir O.S #" . $id . ": " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error'] = 'Erro ao remover ordem de serviço: ' . $e->getMessage();
        }

        // Se for uma requisição AJAX, retorna JSON
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => !isset($_SESSION['error']),
                'message' => $_SESSION['success'] ?? $_SESSION['error'] ?? ''
            ]);
            exit;
        }

        // Se não for AJAX, redireciona
        $this->redirect('/os');
    }

    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    private function getClients() {
        $stmt = $this->db->query('SELECT id, name FROM clients ORDER BY name');
        return $stmt->fetchAll();
    }

    private function validateOrderData() {
        // Debug dos dados brutos recebidos
        error_log("POST data recebida:");
        error_log(json_encode($_POST, JSON_PRETTY_PRINT));

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
        if (is_string($value)) {
            $value = str_replace('.', '', $value); // Remove pontos de milhar
            $value = str_replace(',', '.', $value); // Troca vírgula decimal por ponto
        }
        $value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        
        // Converte para float para garantir formato numérico
        $value = floatval($value);

        // Debug dos dados processados
        error_log("Dados processados:");
        error_log("- client_id: " . $client_id . " (tipo: " . gettype($client_id) . ")");
        error_log("- title: " . $title);
        error_log("- description: " . $description);
        error_log("- status: " . $status);
        error_log("- priority: " . $priority);
        error_log("- start_date: " . $start_date);
        error_log("- end_date: " . ($end_date ?? 'null'));
        error_log("- value: " . $value . " (tipo: " . gettype($value) . ")");

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

        // Validação dos valores permitidos para status e prioridade
        $allowed_status = ['pending', 'in_progress', 'completed', 'cancelled'];
        $allowed_priority = ['low', 'medium', 'high'];

        if (!in_array($status, $allowed_status)) {
            error_log("Status inválido: " . $status);
            error_log("Status permitidos: " . implode(', ', $allowed_status));
            return ['error' => 'Status inválido.'];
        }

        if (!in_array($priority, $allowed_priority)) {
            error_log("Prioridade inválida: " . $priority);
            error_log("Prioridades permitidas: " . implode(', ', $allowed_priority));
            return ['error' => 'Prioridade inválida.'];
        }

        // Formata as datas para o formato do banco de dados
        $start_date = !empty($start_date) ? date('Y-m-d', strtotime($start_date)) : null;
        $end_date = !empty($end_date) ? date('Y-m-d', strtotime($end_date)) : null;

        return [
            'client_id' => $client_id,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'priority' => $priority,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'value' => $value
        ];
    }
}
