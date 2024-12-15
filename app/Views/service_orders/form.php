<?php
$isEdit = isset($order);
$title = $isEdit ? 'Editar O.S' : 'Nova O.S';
$action = $isEdit ? "/os/{$order['id']}" : "/os";
?>

<!-- jQuery e Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="/assets/adminlte/js/adminlte.min.js"></script>

<style>
    /* Estilos dos botões de prioridade */
    .btn-group-toggle .btn {
        flex: 1;
        transition: all 0.3s ease;
    }
    
    .btn-outline-success.active {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: white !important;
    }
    
    .btn-outline-warning.active {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
        color: black !important;
    }
    
    .btn-outline-danger.active {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: white !important;
    }

    .btn-group-toggle .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .btn-group-toggle .btn.active {
        transform: translateY(0);
        box-shadow: none;
    }

    /* Estilos para o campo de busca de clientes */
    .client-search-container {
        position: relative;
    }

    .client-search-input {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        width: 200px;
        padding: 4px 8px;
        font-size: 0.875rem;
        margin-top: 2px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        z-index: 1000;
    }

    .search-link {
        display: block;
        margin-top: 5px;
        font-size: 0.875rem;
        color: #007bff;
        cursor: pointer;
    }

    .search-link:hover {
        text-decoration: underline;
        color: #0056b3;
    }

    /* Estilo para o select com busca */
    .select-search-container {
        position: relative;
    }

    .select-search {
        position: sticky;
        top: 0;
        left: 0;
        right: 0;
        padding: 5px;
        background: white;
        border-bottom: 1px solid #ddd;
    }

    .select-search input {
        width: 100%;
        padding: 5px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
    }

    /* Estilo para o campo de busca */
    .client-search {
        margin-bottom: 5px;
    }

  

    /* Desktop (md e acima) */
    @media (min-width: 768px) {

        .card-body {
            padding: 10px;
        }
    }

    /* Ajuste para remover margem inferior do título */
    .content-header {
        padding-bottom: 0;
    }
    .content-header .container-fluid .row {
        margin-bottom: 0 !important;
    }
</style>

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
                        <form id="serviceOrderForm" method="post" action="<?= $isEdit ? '/os/'.$order['id'] : '/os' ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <?php if ($isEdit): ?>
                                <input type="hidden" name="_method" value="PUT">
                            <?php endif; ?>

                            <div class="row">
                                <!-- Primeira linha -->
                                <!-- Título -->
                                <div class="col-12 col-md-3 mb-3">
                                    <div class="form-group">
                                        <label for="title">Título *</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                            value="<?= $isEdit ? htmlspecialchars($order['title']) : '' ?>" 
                                            required>
                                        <div class="invalid-feedback">
                                            Por favor, informe o título da O.S.
                                        </div>
                                    </div>
                                </div>

                                <!-- Cliente -->
                                <div class="col-12 col-md-3 mb-3">
                                    <div class="form-group">
                                        <label for="client_id">Cliente *</label>
                                        <input type="text" class="form-control client-search" placeholder="Digite para buscar cliente...">
                                        <select class="form-control" id="client_id" name="client_id" required>
                                            <option value="">Selecione um cliente</option>
                                            <?php foreach ($clients as $client): ?>
                                                <option value="<?= $client['id'] ?>" <?= $isEdit && $order['client_id'] == $client['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($client['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">
                                            Por favor, selecione um cliente.
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-12 col-md-3 mb-3">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="pending" <?= $isEdit && $order['status'] == 'pending' ? 'selected' : '' ?>>Pendente</option>
                                            <option value="in_progress" <?= $isEdit && $order['status'] == 'in_progress' ? 'selected' : '' ?>>Em Andamento</option>
                                            <option value="completed" <?= $isEdit && $order['status'] == 'completed' ? 'selected' : '' ?>>Concluído</option>
                                            <option value="cancelled" <?= $isEdit && $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Prioridade -->
                                <div class="col-12 col-md-3 mb-3">
                                    <div class="form-group">
                                        <label>Prioridade</label>
                                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                            <label class="btn btn-outline-success <?= $isEdit && $order['priority'] == 'low' ? 'active' : (!$isEdit ? 'active' : '') ?>">
                                                <input type="radio" name="priority" value="low" <?= $isEdit && $order['priority'] == 'low' ? 'checked' : (!$isEdit ? 'checked' : '') ?>> Baixa
                                            </label>
                                            <label class="btn btn-outline-warning <?= $isEdit && $order['priority'] == 'medium' ? 'active' : '' ?>">
                                                <input type="radio" name="priority" value="medium" <?= $isEdit && $order['priority'] == 'medium' ? 'checked' : '' ?>> Média
                                            </label>
                                            <label class="btn btn-outline-danger <?= $isEdit && $order['priority'] == 'high' ? 'active' : '' ?>">
                                                <input type="radio" name="priority" value="high" <?= $isEdit && $order['priority'] == 'high' ? 'checked' : '' ?>> Alta
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Segunda linha -->
                                <!-- Datas -->
                                <div class="col-12 col-md-8 mb-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="start_date">Data de Início</label>
                                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                                    value="<?= $isEdit ? $order['start_date'] : '' ?>">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="end_date">Data de Término</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                                    value="<?= $isEdit ? $order['end_date'] : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Valor -->
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="form-group">
                                        <label for="value">Valor</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">R$</span>
                                            </div>
                                            <input type="text" class="form-control" id="value" name="value" 
                                                value="<?= $isEdit ? number_format($order['value'], 2, ',', '.') : '' ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Descrição -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="descricao">Descrição</label>
                                        <textarea class="form-control" id="descricao" name="description" rows="3"><?php 
                                            echo $isEdit ? htmlspecialchars($order['description']) : ''; 
                                        ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                    <a href="/os" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/26hgi9pmodkqu1nmtnlvk9bg01s7tr639b9ris1w91ls9v6r/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#descricao',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount code',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | code',
        width: '100%',
        min_height: 300,
        resize: 'vertical',
        statusbar: true,
        menubar: false,
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
        language: 'pt_BR',
    });
</script>

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
});
</script>

<script>
$(document).ready(function() {
    // Busca de clientes
    $('.client-search').on('input', function() {
        var searchText = $(this).val().toLowerCase();
        var $select = $(this).next('select');
        var $options = $select.find('option');
        
        // Abre o select
        $select.attr('size', '6');
        
        // Esconde todas as opções primeiro
        $options.each(function() {
            var $option = $(this);
            var text = $option.text().toLowerCase();
            
            // Sempre mostra a opção vazia (placeholder)
            if ($option.val() === '') {
                $option.prop('hidden', false);
                return;
            }
            
            // Mostra ou esconde baseado no texto de busca
            if (text.indexOf(searchText) > -1) {
                $option.prop('hidden', false);
            } else {
                $option.prop('hidden', true);
            }
        });
    });

    // Fecha o select quando clicar fora
    $(document).on('click', function(e) {
        if (!$(e.target).is('.client-search, #client_id, #client_id *')) {
            $('#client_id').attr('size', '1');
        }
    });

    // Fecha o select quando selecionar uma opção
    $('#client_id').on('change', function() {
        $(this).attr('size', '1');
    });

    // Abre o select quando clicar no input
    $('.client-search').on('click', function() {
        $(this).next('select').attr('size', '6');
    });
});
</script>
