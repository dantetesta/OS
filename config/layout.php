<?php
function renderHeader($title = 'Sistema OS') {
    $appName = env('APP_NAME', 'Sistema OS');
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title . ' | ' . $appName; ?></title>
        
        <!-- AdminLTE 3 -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Custom CSS -->
        <link rel="stylesheet" href="/css/custom.css">
        
        <style>
            /* Estilos do menu */
            .main-sidebar {
                background: #000 !important;
            }
            
            .brand-link {
                border-bottom: 1px solid rgba(255,255,255,0.1) !important;
                padding: 15px 20px !important;
                display: flex;
                align-items: center;
                gap: 10px;
                color: #ffc107 !important;
            }

            .brand-link i {
                font-size: 1.2rem;
                color: #ffc107;
            }

            .brand-link .brand-text {
                font-weight: 500 !important;
                font-size: 1.1rem;
                margin: 0;
                color: #ffc107;
            }
            
            .nav-sidebar .nav-link {
                color: #fff !important;
                padding: 10px 15px;
            }
            
            .nav-sidebar .nav-link:hover {
                background: rgba(255,255,255,0.1);
            }
            
            .nav-sidebar .nav-link.active {
                background: rgba(255,255,255,0.2) !important;
            }
            
            .nav-sidebar .nav-link.menu-header {
                color: #666 !important;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 0.9em;
                pointer-events: none;
                padding-top: 20px;
                padding-bottom: 15px;
            }
            
            .nav-sidebar .nav-link:not(.menu-header) {
                padding-left: 25px;
            }

            /* Padding do conteúdo principal */
            .content-wrapper .container-fluid {
                padding: 0 30px 30px 30px;
            }

            /* Ajuste para títulos das páginas */
            .content-header {
                padding: 15px 0 0 0;
                margin-bottom: 0;
            }

            .content-header h1 {
                margin: 0;
                font-size: 1.8rem;
            }

            /* Ajuste para o card do formulário */
            .card {
                margin-top: 15px;
                margin-bottom: 0;
                box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
            }

            .card-body {
                padding: 15px;
            }

            /* Estilos globais para formulários */
            .form-group {
                margin-bottom: 1rem;
            }

            .form-control {
                border-radius: 4px;
                border: 1px solid #ced4da;
            }

            .form-control:focus {
                border-color: #80bdff;
                box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
            }

            /* Estilos para campos de busca */
            .search-container {
                position: relative;
            }

            .search-input {
                margin-bottom: 5px;
            }

            /* Estilos para botões */
            .btn {
                border-radius: 4px;
                transition: all 0.3s ease;
            }

            .btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }

            .btn-group-toggle .btn {
                flex: 1;
            }

            /* Ajuste para mobile */
            @media (max-width: 768px) {
                .content-wrapper .container-fluid {
                    padding: 0 10px 10px 10px;
                }

                .card-body {
                    padding: 10px;
                }

                .form-group {
                    margin-bottom: 0.75rem;
                }
            }

            /* Estilos para validação de formulário */
            .was-validated .form-control:valid {
                border-color: #28a745;
                padding-right: calc(1.5em + .75rem);
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right calc(.375em + .1875rem) center;
                background-size: calc(.75em + .375rem) calc(.75em + .375rem);
            }

            .was-validated .form-control:invalid {
                border-color: #dc3545;
                padding-right: calc(1.5em + .75rem);
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right calc(.375em + .1875rem) center;
                background-size: calc(.75em + .375rem) calc(.75em + .375rem);
            }

            /* Estilos para inputs com ícones */
            .input-group-text {
                background-color: #f8f9fa;
                border: 1px solid #ced4da;
            }

            /* Estilos para selects */
            select.form-control {
                padding: .375rem .75rem;
                height: calc(1.5em + .75rem + 2px);
            }

            select.form-control[size] {
                height: auto;
            }
        </style>
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                </ul>

                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a href="/logout" class="nav-link">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="/dashboard" class="brand-link">
                    <i class="fas fa-wrench"></i>
                    <span class="brand-text"><?php echo $appName; ?></span>
                </a>

                <!-- Sidebar -->
                <div class="sidebar">
                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column">
                            <li class="nav-item">
                                <a href="/dashboard" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link menu-header">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p>Ordens de Serviço</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/os/nova" class="nav-link">
                                    <i class="nav-icon fas fa-plus-circle"></i>
                                    <p>Nova O.S</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/os" class="nav-link">
                                    <i class="nav-icon fas fa-list"></i>
                                    <p>Listar O.S</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link menu-header">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Clientes</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/clientes/novo" class="nav-link">
                                    <i class="nav-icon fas fa-user-plus"></i>
                                    <p>Novo Cliente</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/clientes" class="nav-link">
                                    <i class="nav-icon fas fa-user-friends"></i>
                                    <p>Listar Clientes</p>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </aside>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <div class="container-fluid">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
    <?php
}

function renderFooter() {
    ?>
            </div>
        </div>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- AdminLTE App -->
        <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    </body>
    </html>
    <?php
}
