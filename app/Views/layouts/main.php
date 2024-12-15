<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Sistema OS - Web Design</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback&v=<?= time() ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css?v=<?= time() ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css?v=<?= time() ?>">
    
    <style>
        /* Reset e base */
        body {
            background: #f4f6f9;
        }

        /* Menu lateral */
        .main-sidebar {
            background: #000 !important;
            box-shadow: none !important;
        }

        .brand-link {
            border: none !important;
            padding: 20px;
            background: #000 !important;
            color: #fff !important;
            text-align: center;
        }

        .sidebar {
            background: #000;
            padding: 0;
        }

        /* Links do menu */
        .nav-item {
            margin: 0;
            padding: 0;
        }

        .nav-link {
            padding: 12px 20px;
            color: #999 !important;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #fff !important;
            background: #111 !important;
        }

        .nav-link.active {
            color: #fff !important;
            background: #111 !important;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* Submenus */
        .menu-parent > .nav-link {
            color: #666 !important;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9em;
            pointer-events: none;
        }

        .menu-parent ul {
            margin-top: 5px;
        }

        .menu-parent ul .nav-link {
            padding-left: 40px;
        }

        /* Navbar superior */
        .main-header {
            background: #fff;
            border: none !important;
        }

        /* Remove comportamentos do AdminLTE */
        .sidebar-mini.sidebar-collapse .main-sidebar:hover {
            width: 250px !important;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar:hover .nav-link span {
            display: inline-block !important;
        }

        @media (max-width: 768px) {
            .main-sidebar {
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
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
                <span class="brand-text font-weight-light">Sistema OS</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="/dashboard" class="nav-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item menu-parent">
                            <a href="#" class="nav-link">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Ordens de Serviço</span>
                            </a>
                            <ul class="nav flex-column pl-3">
                                <li class="nav-item">
                                    <a href="/os/nova" class="nav-link">
                                        <i class="far fa-circle"></i>
                                        <span>Nova O.S</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/os" class="nav-link">
                                        <i class="far fa-circle"></i>
                                        <span>Listar O.S</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item menu-parent">
                            <a href="#" class="nav-link">
                                <i class="fas fa-users"></i>
                                <span>Clientes</span>
                            </a>
                            <ul class="nav flex-column pl-3">
                                <li class="nav-item">
                                    <a href="/clientes/novo" class="nav-link">
                                        <i class="far fa-circle"></i>
                                        <span>Novo Cliente</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="/clientes" class="nav-link">
                                        <i class="far fa-circle"></i>
                                        <span>Listar Clientes</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $_SESSION['success'] ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $_SESSION['error'] ?>
                            <button type="button" class="close" data-dismiss="alert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <?php include $content ?? ''; ?>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; <?= date('Y') ?> Sistema OS.</strong>
            Todos os direitos reservados.
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js?v=<?= time() ?>"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js?v=<?= time() ?>"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js?v=<?= time() ?>"></script>

    <script>
        $(document).ready(function() {
            // Marca o item atual como ativo
            var currentPath = window.location.pathname;
            $('.nav-sidebar .nav-link').each(function() {
                if ($(this).attr('href') === currentPath) {
                    $(this).addClass('active');
                }
            });
        });
    </script>
</body>
</html>
