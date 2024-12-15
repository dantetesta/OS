<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Classe responsável pela conexão com o banco de dados
 * Implementa o padrão Singleton para garantir uma única instância
 */
class Database {
    private static $instance = null;
    private $connection;
    private $config;

    private function __construct() {
        $this->config = require CONFIG_PATH . '/config.php';
        $this->connect();
    }

    /**
     * Obtém a instância única da conexão
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Estabelece a conexão com o banco de dados
     */
    private function connect() {
        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                $this->config['database']['host'],
                $this->config['database']['dbname'],
                $this->config['database']['charset']
            );

            $this->connection = new PDO(
                $dsn,
                $this->config['database']['username'],
                $this->config['database']['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            throw new \Exception("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }

    /**
     * Retorna a conexão ativa
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Previne a clonagem da instância
     */
    private function __clone() {}

    /**
     * Previne a deserialização da instância
     */
    private function __wakeup() {}
}
