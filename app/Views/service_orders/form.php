<?php
$isEdit = isset($order);
$title = $isEdit ? 'Editar O.S' : 'Nova O.S';
$action = $isEdit ? "/os/{$order['id']}" : "/os";
?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?php echo $title; ?></h1>
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
                    <div class="card-body">
                        <form action="<?php echo $action; ?>" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <?php if ($isEdit): ?>
                                <input type="hidden" name="_method" value="PUT">
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_id">Cliente *</label>
                                        <select class="form-control" id="client_id" name="client_id" required>
                                            <option value="">Selecione um cliente</option>
                                            <?php foreach ($clients as $client): ?>
                                                <option value="<?php echo $client['id']; ?>" 
                                                    <?php echo $isEdit && $order['client_id'] == $client['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($client['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor, selecione um cliente.
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="title">Título *</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                            value="<?php echo $isEdit ? htmlspecialchars($order['title']) : ''; ?>" 
                                            required>
                                        <div class="invalid-feedback">
                                            Por favor, informe o título da O.S.
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Descrição</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"><?php 
                                            echo $isEdit ? htmlspecialchars($order['description']) : ''; 
                                        ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="pending" <?php echo $isEdit && $order['status'] == 'pending' ? 'selected' : ''; ?>>
                                                Pendente
                                            </option>
                                            <option value="in_progress" <?php echo $isEdit && $order['status'] == 'in_progress' ? 'selected' : ''; ?>>
                                                Em Andamento
                                            </option>
                                            <option value="completed" <?php echo $isEdit && $order['status'] == 'completed' ? 'selected' : ''; ?>>
                                                Concluído
                                            </option>
                                            <option value="cancelled" <?php echo $isEdit && $order['status'] == 'cancelled' ? 'selected' : ''; ?>>
                                                Cancelado
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="priority">Prioridade</label>
                                        <select class="form-control" id="priority" name="priority">
                                            <option value="low" <?php echo $isEdit && $order['priority'] == 'low' ? 'selected' : ''; ?>>
                                                Baixa
                                            </option>
                                            <option value="medium" <?php echo $isEdit && $order['priority'] == 'medium' ? 'selected' : ''; ?>>
                                                Média
                                            </option>
                                            <option value="high" <?php echo $isEdit && $order['priority'] == 'high' ? 'selected' : ''; ?>>
                                                Alta
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="start_date">Data de Início</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                            value="<?php echo $isEdit ? $order['start_date'] : ''; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="end_date">Data de Término</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                            value="<?php echo $isEdit ? $order['end_date'] : ''; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="value">Valor</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">R$</span>
                                            </div>
                                            <input type="text" class="form-control" id="value" name="value" 
                                                value="<?php echo $isEdit ? number_format($order['value'], 2, ',', '.') : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                                <a href="/os" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Validação do formulário
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Máscara para valor monetário
document.getElementById('value').addEventListener('input', function (e) {
    var value = e.target.value.replace(/\D/g, '');
    value = (value/100).toFixed(2);
    value = value.replace(".", ",");
    value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
    e.target.value = value;
});</script>
