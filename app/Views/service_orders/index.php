<?php
// Debug dos dados recebidos
error_log("Dados recebidos na view:");
error_log("orders: " . json_encode($orders ?? []));
error_log("search: " . ($search ?? 'não definido'));
error_log("status: " . ($status ?? 'não definido'));

// Funções auxiliares para formatação
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

function getPriorityLabel($priority) {
    $labels = [
        'low' => 'Baixa',
        'medium' => 'Média',
        'high' => 'Alta'
    ];
    return $labels[$priority] ?? $priority;
}

function getPriorityBadge($priority) {
    $badges = [
        'low' => 'success',
        'medium' => 'warning',
        'high' => 'danger'
    ];
    return $badges[$priority] ?? 'secondary';
}

function formatDate($date) {
    if (empty($date)) return '-';
    return date('d/m/Y', strtotime($date));
}

function formatMoney($value) {
    if (!is_numeric($value)) return 'R$ 0,00';
    return 'R$ ' . number_format($value, 2, ',', '.');
}
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Ordens de Serviço</h1>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <form action="/os" method="GET" class="form-inline">
                                    <div class="input-group mr-2">
                                        <input type="text" name="search" class="form-control" 
                                            placeholder="Buscar por título, descrição ou cliente" 
                                            value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                    </div>
                                    <select name="status" class="form-control mr-2">
                                        <option value="">Todos os Status</option>
                                        <option value="pending" <?php echo isset($status) && $status == 'pending' ? 'selected' : ''; ?>>
                                            Pendente
                                        </option>
                                        <option value="in_progress" <?php echo isset($status) && $status == 'in_progress' ? 'selected' : ''; ?>>
                                            Em Andamento
                                        </option>
                                        <option value="completed" <?php echo isset($status) && $status == 'completed' ? 'selected' : ''; ?>>
                                            Concluído
                                        </option>
                                        <option value="cancelled" <?php echo isset($status) && $status == 'cancelled' ? 'selected' : ''; ?>>
                                            Cancelado
                                        </option>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="/os/nova" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Nova O.S
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Título</th>
                                    <th>Status</th>
                                    <th>Prioridade</th>
                                    <th>Valor</th>
                                    <th>Data Início</th>
                                    <th>Data Fim</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orders)): 
                                    foreach ($orders as $order): 
                                        // Debug de cada ordem
                                        error_log("Processando ordem: " . json_encode($order));
                                ?>
                                    <tr>
                                        <td><?php echo $order['id'] ?? ''; ?></td>
                                        <td>
                                            <?php if (!empty($order['client_name'])): ?>
                                                <span data-toggle="tooltip" title="<?php echo htmlspecialchars($order['client_email'] ?? ''); ?>">
                                                    <?php echo htmlspecialchars($order['client_name']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Cliente não encontrado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['title'] ?? ''); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo getStatusBadge($order['status'] ?? ''); ?>">
                                                <?php echo getStatusLabel($order['status'] ?? ''); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo getPriorityBadge($order['priority'] ?? ''); ?>">
                                                <?php echo getPriorityLabel($order['priority'] ?? ''); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatMoney($order['value'] ?? 0); ?></td>
                                        <td><?php echo formatDate($order['start_date'] ?? ''); ?></td>
                                        <td><?php echo formatDate($order['end_date'] ?? ''); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="/os/editar/<?php echo $order['id']; ?>" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        data-toggle="modal" 
                                                        data-target="#deleteModal" 
                                                        data-id="<?php echo $order['id']; ?>"
                                                        title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            Nenhuma ordem de serviço encontrada.
                                            <?php if (!empty($search) || !empty($status)): ?>
                                                <br>
                                                <a href="/os" class="btn btn-sm btn-outline-secondary mt-2">
                                                    <i class="fas fa-times"></i> Limpar filtros
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta ordem de serviço?</p>
                <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
            </div>
            <div class="modal-footer">
                <form action="/os/excluir" method="POST" id="deleteForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Ativa os tooltips do Bootstrap
    $('[data-toggle="tooltip"]').tooltip();
    
    // Configura o modal de exclusão
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        $('#deleteId').val(id);
    });
});
</script>
