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
            <div class="col-sm-6">
                <form id="filterForm" class="form-inline float-right">
                    <div class="form-group mx-2">
                        <label for="start_date" class="mr-2">Data Inicial:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date">
                    </div>
                    <div class="form-group mx-2">
                        <label for="end_date" class="mr-2">Data Final:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </form>
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
                        <span class="info-box-number" data-counter="total_clients">0</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-clipboard-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total de O.S</span>
                        <span class="info-box-number" data-counter="total_orders">0</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">O.S em Andamento</span>
                        <span class="info-box-number" data-counter="orders_in_progress">0</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-calendar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">O.S este Mês</span>
                        <span class="info-box-number" data-counter="orders_this_month">0</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <!-- Status das O.S e Faturamento -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Análise de Desempenho</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-center mb-3">Status das O.S</h5>
                                <div id="osStatusChart" style="min-height: 300px;">
                                    <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Carregando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-center mb-3">Faturamento <?php echo date('Y'); ?></h5>
                                <div id="revenueChart" style="min-height: 300px;">
                                    <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Carregando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <!-- Últimas O.S -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Últimas O.S</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody id="latestOrdersTable">
                                    <tr>
                                        <td colspan="4" class="text-center">Carregando...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let statusChart = null;
    let revenueChart = null;

    // Função para mostrar mensagem de erro
    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show';
        errorDiv.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        document.querySelector('.content').insertBefore(errorDiv, document.querySelector('.content').firstChild);
    }

    // Função para carregar os dados do dashboard
    async function loadDashboardData(startDate = '', endDate = '') {
        try {
            // Mostrar spinners
            document.querySelectorAll('[data-counter]').forEach(counter => {
                counter.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
            });

            const response = await fetch(`/dashboard/stats?start_date=${startDate}&end_date=${endDate}`);
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.error || 'Erro ao carregar dados do dashboard');
            }

            const data = result.data;

            // Atualizar contadores
            document.querySelector('[data-counter="total_clients"]').textContent = data.totalClients;
            document.querySelector('[data-counter="total_orders"]').textContent = data.totalOrders;
            document.querySelector('[data-counter="orders_in_progress"]').textContent = data.ordersInProgress;
            document.querySelector('[data-counter="orders_this_month"]').textContent = data.ordersThisMonth;

            // Atualizar gráficos
            updateStatusChart(data.formattedStatus);
            updateRevenueChart(data.revenueByMonth, data.months);

            // Atualizar tabela de últimas OS
            updateLatestOrders(data.latestOrders);

        } catch (error) {
            console.error('Erro:', error);
            showError(error.message);
        }
    }

    // Função para atualizar o gráfico de status
    function updateStatusChart(statusData) {
        const chartContainer = document.querySelector("#osStatusChart");
        chartContainer.innerHTML = '';

        if (!statusData || statusData.length === 0) {
            chartContainer.innerHTML = '<div class="alert alert-info">Nenhum dado disponível para o período selecionado</div>';
            return;
        }

        const options = {
            series: statusData.map(item => item.count),
            labels: statusData.map(item => item.label),
            chart: {
                type: 'donut',
                height: 300
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        statusChart = new ApexCharts(chartContainer, options);
        statusChart.render();
    }

    // Função para atualizar o gráfico de faturamento
    function updateRevenueChart(revenueData, months) {
        const chartContainer = document.querySelector("#revenueChart");
        chartContainer.innerHTML = '';

        if (!revenueData || revenueData.length === 0) {
            chartContainer.innerHTML = '<div class="alert alert-info">Nenhum dado disponível para o período selecionado</div>';
            return;
        }

        const values = revenueData.map(item => item.value);
        const orders = revenueData.map(item => item.orders);

        const options = {
            series: [{
                name: 'Faturamento',
                type: 'column',
                data: values
            }, {
                name: 'Ordens de Serviço',
                type: 'line',
                data: orders
            }],
            chart: {
                height: 300,
                type: 'line',
                stacked: false
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: [1, 4]
            },
            title: {
                text: '',
                align: 'left',
                offsetX: 110
            },
            xaxis: {
                categories: months,
            },
            yaxis: [{
                axisTicks: {
                    show: true,
                },
                axisBorder: {
                    show: true,
                    color: '#008FFB'
                },
                labels: {
                    style: {
                        colors: '#008FFB',
                    },
                    formatter: function (value) {
                        return 'R$ ' + value.toFixed(0);
                    }
                },
                title: {
                    text: "Faturamento (R$)",
                    style: {
                        color: '#008FFB',
                    }
                },
                tooltip: {
                    enabled: true
                }
            }, {
                seriesName: 'Ordens de Serviço',
                opposite: true,
                axisTicks: {
                    show: true,
                },
                axisBorder: {
                    show: true,
                    color: '#00E396'
                },
                labels: {
                    style: {
                        colors: '#00E396',
                    }
                },
                title: {
                    text: "Ordens de Serviço",
                    style: {
                        color: '#00E396',
                    }
                },
            }],
            tooltip: {
                fixed: {
                    enabled: true,
                    position: 'topLeft',
                    offsetY: 30,
                    offsetX: 60
                },
            },
            legend: {
                horizontalAlign: 'left',
                offsetX: 40
            }
        };

        revenueChart = new ApexCharts(chartContainer, options);
        revenueChart.render();
    }

    // Função para atualizar a tabela de últimas OS
    function updateLatestOrders(orders) {
        const tbody = document.getElementById('latestOrdersTable');
        if (!orders || orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">Nenhuma ordem de serviço encontrada</td></tr>';
            return;
        }

        tbody.innerHTML = orders.map(order => `
            <tr>
                <td>${order.id}</td>
                <td>${order.client_name}</td>
                <td>
                    <span class="badge badge-${getStatusColor(order.status)}">
                        ${getStatusLabel(order.status)}
                    </span>
                </td>
                <td>${new Date(order.created_at).toLocaleDateString('pt-BR')}</td>
            </tr>
        `).join('');
    }

    // Função auxiliar para cor do status
    function getStatusColor(status) {
        switch(status) {
            case 'pending': return 'warning';
            case 'in_progress': return 'info';
            case 'completed': return 'success';
            case 'cancelled': return 'danger';
            default: return 'secondary';
        }
    }

    // Função auxiliar para label do status
    function getStatusLabel(status) {
        switch(status) {
            case 'pending': return 'Pendente';
            case 'in_progress': return 'Em Andamento';
            case 'completed': return 'Finalizada';
            case 'cancelled': return 'Cancelada';
            default: return status;
        }
    }

    // Listener para o formulário de filtro
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (startDate && endDate) {
            if (startDate > endDate) {
                showError('A data inicial não pode ser maior que a data final');
                return;
            }
            loadDashboardData(startDate, endDate);
        } else if (startDate || endDate) {
            showError('Por favor, preencha ambas as datas');
        } else {
            loadDashboardData();
        }
    });

    // Carregar dados iniciais
    loadDashboardData();
});
</script>

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
