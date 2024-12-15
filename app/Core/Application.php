<?php
namespace App\Core;

class Application {
    private static $instance = null;
    private $router;

    private function __construct() {
        $this->router = Router::getInstance();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function run() {
        $this->router->dispatch();
    }

    public function getRouter() {
        return $this->router;
    }
}
