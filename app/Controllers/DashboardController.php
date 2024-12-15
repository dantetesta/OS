<?php
namespace App\Controllers;

use App\Core\DB;

class DashboardController extends BaseController {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance()->getConnection();
        $this->requireAuth();
    }

    public function index() {
        $this->view('dashboard/index', [
            'csrf_token' => $this->csrf()
        ]);
    }

    public function stats() {
        try {
            // Define o mês corrente como padrão
            $startDate = isset($_GET['start_date']) ? htmlspecialchars(trim($_GET['start_date'])) : date('Y-m-01');
            $endDate = isset($_GET['end_date']) ? htmlspecialchars(trim($_GET['end_date'])) : date('Y-m-t');
            
            // Validação das datas
            if ($startDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
                throw new \Exception('Data inicial inválida');
            }
            
            if ($endDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
                throw new \Exception('Data final inválida');
            }
            
            if (strtotime($startDate) > strtotime($endDate)) {
                throw new \Exception('A data inicial não pode ser maior que a data final');
            }
            
            $stats = $this->getStats($startDate, $endDate);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            error_log('Erro no dashboard: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Erro ao carregar dados do dashboard: ' . $e->getMessage()
            ]);
        }
    }

    private function getStats($startDate = '', $endDate = '') {
        try {
            error_log("Iniciando busca de estatísticas do dashboard - Período: $startDate até $endDate");

            // Preparar condição de data para as queries
            $dateCondition = '';
            $dateParams = [];
            
            if ($startDate && $endDate) {
                $dateCondition = ' AND (
                    (start_date BETWEEN :start_date_1 AND :end_date_1) OR
                    (end_date BETWEEN :start_date_2 AND :end_date_2) OR
                    (start_date <= :start_date_3 AND (end_date >= :end_date_3 OR end_date IS NULL))
                )';
                $dateParams = [
                    'start_date_1' => $startDate,
                    'end_date_1' => $endDate,
                    'start_date_2' => $startDate,
                    'end_date_2' => $endDate,
                    'start_date_3' => $startDate,
                    'end_date_3' => $endDate
                ];
            }

            // Total de clientes ativos
            $stmt = $this->db->query('
                SELECT COUNT(*) as total 
                FROM clients
            ');
            $totalClients = $stmt->fetch()['total'];

            // Total de OS ativas (não canceladas)
            $query = 'SELECT COUNT(*) as total FROM service_orders WHERE status != "cancelled"' . $dateCondition;
            $stmt = $this->db->prepare($query);
            $stmt->execute($dateParams);
            $totalOrders = $stmt->fetch()['total'];

            // OS por status
            $query = 'SELECT status, COUNT(*) as count FROM service_orders WHERE 1=1';
            if ($dateCondition) {
                $query .= $dateCondition;
            }
            $query .= ' GROUP BY status';
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($dateParams);
            $ordersByStatus = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Garantir que todos os status existam no array
            $allStatus = ['pending', 'in_progress', 'completed', 'cancelled'];
            $statusCounts = array_fill_keys($allStatus, 0);
            
            foreach ($ordersByStatus as $status) {
                if (isset($statusCounts[$status['status']])) {
                    $statusCounts[$status['status']] = (int)$status['count'];
                }
            }

            // OS em andamento
            $ordersInProgress = $statusCounts['in_progress'];

            // OS do período atual
            $query = 'SELECT COUNT(*) as total FROM service_orders WHERE status != "cancelled"';
            $currentParams = [];
            
            if ($dateCondition) {
                $query .= str_replace(['start_date_1', 'end_date_1', 'start_date_2', 'end_date_2', 'start_date_3', 'end_date_3'], 
                                    ['start_date_4', 'end_date_4', 'start_date_5', 'end_date_5', 'start_date_6', 'end_date_6'], 
                                    $dateCondition);
                $currentParams = [
                    'start_date_4' => $startDate,
                    'end_date_4' => $endDate,
                    'start_date_5' => $startDate,
                    'end_date_5' => $endDate,
                    'start_date_6' => $startDate,
                    'end_date_6' => $endDate
                ];
            } else {
                $currentMonth = date('m');
                $currentYear = date('Y');
                $query .= ' AND MONTH(created_at) = :month AND YEAR(created_at) = :year';
                $currentParams = ['month' => $currentMonth, 'year' => $currentYear];
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($currentParams);
            $ordersThisMonth = $stmt->fetch()['total'];

            // Faturamento mensal
            $query = '
                SELECT 
                    MONTH(created_at) as month,
                    COUNT(CASE WHEN status != "cancelled" THEN 1 END) as orders,
                    COALESCE(SUM(CASE 
                        WHEN status = "completed" THEN value 
                        ELSE 0 
                    END), 0) as value
                FROM service_orders
                WHERE 1=1
            ';
            
            $revenueParams = [];
            if ($dateCondition) {
                $query .= str_replace(['start_date_1', 'end_date_1', 'start_date_2', 'end_date_2', 'start_date_3', 'end_date_3'], 
                                    ['start_date_7', 'end_date_7', 'start_date_8', 'end_date_8', 'start_date_9', 'end_date_9'], 
                                    $dateCondition);
                $revenueParams = [
                    'start_date_7' => $startDate,
                    'end_date_7' => $endDate,
                    'start_date_8' => $startDate,
                    'end_date_8' => $endDate,
                    'start_date_9' => $startDate,
                    'end_date_9' => $endDate
                ];
            } else {
                $query .= ' AND YEAR(created_at) = :year';
                $revenueParams = ['year' => date('Y')];
            }
            
            $query .= ' GROUP BY MONTH(created_at) ORDER BY month ASC';
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($revenueParams);
            $monthlyRevenue = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Formatar dados de faturamento para todos os meses
            $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            $revenueByMonth = array_fill(0, 12, ['value' => 0, 'orders' => 0]);
            
            foreach ($monthlyRevenue as $revenue) {
                $monthIndex = (int)$revenue['month'] - 1;
                $revenueByMonth[$monthIndex] = [
                    'value' => (float)$revenue['value'],
                    'orders' => (int)$revenue['orders']
                ];
            }

            // Formatar status para exibição
            $statusLabels = [
                'pending' => 'Pendente',
                'in_progress' => 'Em Andamento',
                'completed' => 'Finalizada',
                'cancelled' => 'Cancelada'
            ];
            
            $formattedStatus = [];
            foreach ($allStatus as $status) {
                $formattedStatus[] = [
                    'status' => $status,
                    'label' => $statusLabels[$status],
                    'count' => $statusCounts[$status]
                ];
            }

            // Buscar últimas OS
            $query = '
                SELECT 
                    so.id,
                    so.title,
                    so.status,
                    so.created_at,
                    c.name as client_name
                FROM service_orders so
                JOIN clients c ON c.id = so.client_id
                WHERE 1=1
            ';
            
            $latestParams = [];
            if ($dateCondition) {
                $query .= str_replace(['start_date_1', 'end_date_1', 'start_date_2', 'end_date_2', 'start_date_3', 'end_date_3'], 
                                    ['start_date_10', 'end_date_10', 'start_date_11', 'end_date_11', 'start_date_12', 'end_date_12'], 
                                    $dateCondition);
                $latestParams = [
                    'start_date_10' => $startDate,
                    'end_date_10' => $endDate,
                    'start_date_11' => $startDate,
                    'end_date_11' => $endDate,
                    'start_date_12' => $startDate,
                    'end_date_12' => $endDate
                ];
            }
            
            $query .= ' ORDER BY so.created_at DESC LIMIT 5';
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($latestParams);
            $latestOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return [
                'totalClients' => $totalClients,
                'totalOrders' => $totalOrders,
                'ordersInProgress' => $ordersInProgress,
                'ordersThisMonth' => $ordersThisMonth,
                'formattedStatus' => $formattedStatus,
                'revenueByMonth' => $revenueByMonth,
                'months' => $months,
                'latestOrders' => $latestOrders
            ];

        } catch (\Exception $e) {
            error_log("Erro ao buscar estatísticas: " . $e->getMessage());
            throw $e;
        }
    }

    private function getLatestOrders() {
        try {
            $stmt = $this->db->query('
                SELECT so.*, c.name as client_name 
                FROM service_orders so
                JOIN clients c ON c.id = so.client_id
                ORDER BY so.created_at DESC
                LIMIT 5
            ');
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Erro ao buscar últimas ordens: " . $e->getMessage());
            return [];
        }
    }
}
