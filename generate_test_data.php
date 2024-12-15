<?php
require_once __DIR__ . '/config/config.php';

try {
    // Configuração do PDO
    $config = require __DIR__ . '/config/config.php';
    $dsn = "mysql:host={$config['database']['host']};dbname={$config['database']['dbname']};charset={$config['database']['charset']}";
    
    $pdo = new PDO(
        $dsn,
        $config['database']['username'],
        $config['database']['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // Buscar IDs dos clientes existentes
    $stmt = $pdo->query('SELECT id FROM clients');
    $clientIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($clientIds)) {
        die("Nenhum cliente encontrado. Por favor, insira clientes primeiro.\n");
    }

    // Configurações para geração de dados
    $startDate = '2023-01-01';
    $endDate = '2024-12-13';
    $totalOrders = 500;

    // Arrays para dados aleatórios
    $titles = [
        'Manutenção Preventiva',
        'Instalação de Equipamento',
        'Reparo de Emergência',
        'Configuração de Sistema',
        'Atualização de Software',
        'Suporte Técnico',
        'Consultoria',
        'Desenvolvimento',
        'Implementação',
        'Migração de Dados'
    ];

    $descriptions = [
        'Realização de manutenção preventiva no sistema',
        'Instalação e configuração de novo equipamento',
        'Reparo emergencial solicitado pelo cliente',
        'Configuração e otimização do sistema',
        'Atualização de software para nova versão',
        'Suporte técnico para resolução de problemas',
        'Consultoria especializada',
        'Desenvolvimento de nova funcionalidade',
        'Implementação de melhorias',
        'Migração de dados para novo sistema'
    ];

    $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
    $priorities = ['low', 'medium', 'high'];

    // Preparar a query de inserção
    $stmt = $pdo->prepare('
        INSERT INTO service_orders 
        (client_id, title, description, status, priority, start_date, end_date, value, created_at) 
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    // Gerar e inserir ordens de serviço
    $startTimestamp = strtotime($startDate);
    $endTimestamp = strtotime($endDate);
    $counter = 0;

    echo "Iniciando inserção de {$totalOrders} ordens de serviço...\n";

    for ($i = 0; $i < $totalOrders; $i++) {
        // Gerar datas aleatórias
        $createdTimestamp = rand($startTimestamp, $endTimestamp);
        $createdDate = date('Y-m-d H:i:s', $createdTimestamp);
        
        // Data de início alguns dias após a criação
        $startDateTimestamp = $createdTimestamp + (rand(1, 5) * 86400);
        $startDate = date('Y-m-d', $startDateTimestamp);
        
        // Status aleatório
        $status = $statuses[array_rand($statuses)];
        
        // Data de fim baseada no status
        $endDate = null;
        if ($status == 'completed' || $status == 'cancelled') {
            $endDate = date('Y-m-d', $startDateTimestamp + (rand(1, 30) * 86400));
        } elseif ($status == 'in_progress' && rand(0, 1)) {
            $endDate = date('Y-m-d', $startDateTimestamp + (rand(1, 60) * 86400));
        }

        // Gerar dados aleatórios
        $data = [
            $clientIds[array_rand($clientIds)],                    // client_id
            $titles[array_rand($titles)] . ' #' . ($i + 1),       // title
            $descriptions[array_rand($descriptions)],              // description
            $status,                                              // status
            $priorities[array_rand($priorities)],                 // priority
            $startDate,                                          // start_date
            $endDate,                                            // end_date
            number_format(rand(1000, 10000) + (rand(0, 100) / 100), 2), // value
            $createdDate                                         // created_at
        ];

        $stmt->execute($data);
        $counter++;

        if ($counter % 100 == 0) {
            echo "Inseridas {$counter} ordens de serviço...\n";
        }
    }

    echo "\nInserção concluída com sucesso! {$counter} ordens de serviço foram criadas.\n";

} catch (PDOException $e) {
    die("Erro: " . $e->getMessage() . "\n");
}
