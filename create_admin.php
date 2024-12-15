<?php
require_once __DIR__ . '/config/config.php';

try {
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

    // Criar hash da senha
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Deletar usuário admin se existir
    $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
    $stmt->execute(['admin@wprevolution.com.br']);

    // Inserir novo usuário admin
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute(['Administrador', 'admin@wprevolution.com.br', $hash]);

    echo "Usuário admin criado com sucesso!\n";
    echo "Email: admin@wprevolution.com.br\n";
    echo "Senha: admin123\n";
    echo "Hash gerado: " . $hash . "\n";

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
