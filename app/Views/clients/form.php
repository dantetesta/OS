<?php
$isEdit = isset($client);
$title = $isEdit ? 'Editar Cliente' : 'Novo Cliente';
$action = $isEdit ? "/clientes/{$client['id']}" : "/clientes";
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

                            <div class="form-group">
                                <label for="name">Nome *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                    value="<?php echo $isEdit ? htmlspecialchars($client['name']) : ''; ?>" 
                                    required>
                                <div class="invalid-feedback">
                                    Por favor, informe o nome do cliente.
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                    value="<?php echo $isEdit ? htmlspecialchars($client['email']) : ''; ?>" 
                                    required>
                                <div class="invalid-feedback">
                                    Por favor, informe um email válido.
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone">Telefone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                    value="<?php echo $isEdit ? htmlspecialchars($client['phone']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="address">Endereço</label>
                                <textarea class="form-control" id="address" name="address" rows="3"><?php 
                                    echo $isEdit ? htmlspecialchars($client['address']) : ''; 
                                ?></textarea>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                                <a href="/clientes" class="btn btn-secondary">Cancelar</a>
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

// Máscara para telefone
document.getElementById('phone').addEventListener('input', function (e) {
    var x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
    e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
});
</script>
