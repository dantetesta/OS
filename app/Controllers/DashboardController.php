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
        // Buscar estatísticas
        $stats = $this->getStats();
        $latestOrders = $this->getLatestOrders();
        
        $this->view('dashboard/index', [
            'stats' => $stats,
            'latestOrders' => $latestOrders,
            'csrf_token' => $this->csrf()
        ]);
    }

    private function getStats() {
        try {
            // Total de clientes
            $stmt = $this->db->query('SELECT COUNT(*) as total FROM clients');
            $totalClients = $stmt->fetch()['total'];

            // Total de OS
            $stmt = $this->db->query('SELECT COUNT(*) as total FROM service_orders');
            $totalOrders = $stmt->fetch()['total'];

            // OS por status
            $stmt = $this->db->query('
                SELECT status, COUNT(*) as count 
                FROM service_orders 
                GROUP BY status
            ');
            $ordersByStatus = $stmt->fetchAll();

            // OS do mês atual
            $stmt = $this->db->query('
                SELECT COUNT(*) as total 
                FROM service_orders 
                WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
                AND YEAR(created_at) = YEAR(CURRENT_DATE())
            ');
            $ordersThisMonth = $stmt->fetch()['total'];

            return [
                'total_clients' => $totalClients,
                'total_orders' => $totalOrders,
                'orders_by_status' => $ordersByStatus,
                'orders_this_month' => $ordersThisMonth
            ];
        } catch (\PDOException $e) {
            error_log("Erro ao buscar estatísticas: " . $e->getMessage());
            return [
                'total_clients' => 0,
                'total_orders' => 0,
                'orders_by_status' => [],
                'orders_this_month' => 0
            ];
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
