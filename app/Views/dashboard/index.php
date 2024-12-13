<?php
// Definir o conteúdo da página
ob_start();
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de Clientes</span>
                        <span class="info-box-number"><?php echo $stats['total_clients'] ?? 0; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-clipboard-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de O.S</span>
                        <span class="info-box-number"><?php echo $stats['total_orders'] ?? 0; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">O.S em Andamento</span>
                        <span class="info-box-number">
                            <?php
                            $inProgress = isset($stats['orders_by_status']) ? array_filter($stats['orders_by_status'], function($item) {
                                return $item['status'] === 'in_progress';
                            }) : [];
                            echo !empty($inProgress) ? current($inProgress)['count'] : 0;
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-calendar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">O.S este Mês</span>
                        <span class="info-box-number"><?php echo $stats['orders_this_month'] ?? 0; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Status das O.S -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Status das O.S</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="osStatusChart" style="min-height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Últimas O.S -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Últimas O.S</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table m-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($latestOrders)): ?>
                                        <?php foreach ($latestOrders as $order): ?>
                                        <tr>
                                            <td><?php echo $order['id']; ?></td>
                                            <td><?php echo $order['client_name']; ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo getStatusBadge($order['status']); ?>">
                                                    <?php echo getStatusLabel($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Nenhuma O.S encontrada</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados para o gráfico
    const statusData = <?php echo json_encode($stats['orders_by_status'] ?? []); ?>;
    
    // Preparar dados para o Chart.js
    const labels = statusData.map(item => getStatusLabel(item.status));
    const data = statusData.map(item => item.count);
    const colors = statusData.map(item => getStatusColor(item.status));
    
    // Criar o gráfico
    const ctx = document.getElementById('osStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});</script>

<?php
// Funções auxiliares para o status
function getStatusLabel($status) {
    $labels = [
        'pending' => 'Pendente',
        'in_progress' => 'Em Andamento',
        'completed' => 'Concluído',
        'cancelled' => 'Cancelado'
    ];
    return $labels[$status] ?? $status;
}

function getStatusBadge($status) {
    $badges = [
        'pending' => 'warning',
        'in_progress' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger'
    ];
    return $badges[$status] ?? 'secondary';
}

function getStatusColor($status) {
    $colors = [
        'pending' => '#ffc107',
        'in_progress' => '#17a2b8',
        'completed' => '#28a745',
        'cancelled' => '#dc3545'
    ];
    return $colors[$status] ?? '#6c757d';
}
?>
