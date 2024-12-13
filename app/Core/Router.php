<?php
namespace App\Core;

/**
 * Classe responsável pelo gerenciamento de rotas
 * Implementa um sistema simples de roteamento MVC
 */
class Router {
    private $routes = [];
    private static $instance = null;

    private function __construct() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Adiciona uma nova rota
     */
    public function add($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    /**
     * Processa a rota atual
     */
    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        error_log('URI recebida: ' . $uri);
        
        $method = $this->getRequestMethod();
        error_log('Método HTTP: ' . $method);
        
        $params = [];

        foreach ($this->routes as $route) {
            if ($this->matchRoute($route['path'], $uri, $params) && $route['method'] === $method) {
                error_log('Rota encontrada: ' . $route['path']);
                error_log('Parâmetros: ' . print_r($params, true));
                
                list($controller, $action) = $route['handler'];
                $controllerInstance = new $controller();
                
                if (!empty($params)) {
                    return call_user_func_array([$controllerInstance, $action], $params);
                }
                
                return $controllerInstance->$action();
            }
        }

        error_log('Nenhuma rota encontrada para: ' . $uri);
        header("HTTP/1.0 404 Not Found");
        require BASE_PATH . '/app/Views/errors/404.php';
    }

    /**
     * Verifica se a rota corresponde ao padrão e extrai os parâmetros
     */
    private function matchRoute($pattern, $uri, &$params) {
        // Remover query string da URI se existir
        $uri = strtok($uri, '?');
        error_log('URI para match: ' . $uri);
        error_log('Pattern: ' . $pattern);
        
        // Converter padrão da rota para expressão regular
        $pattern = preg_replace('/\/{([^\/]+)}/', '/([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches);
            $params = $matches;
            error_log('Match encontrado com parâmetros: ' . print_r($params, true));
            return true;
        }
        
        return false;
    }

    /**
     * Obtém o método HTTP real da requisição
     */
    private function getRequestMethod() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method === 'POST') {
            if (isset($_POST['_method'])) {
                $method = strtoupper($_POST['_method']);
            }
        }
        
        return $method;
    }

    // Métodos auxiliares para definir rotas
    public function get($path, $handler) {
        $this->add('GET', $path, $handler);
    }

    public function post($path, $handler) {
        $this->add('POST', $path, $handler);
    }

    public function put($path, $handler) {
        $this->add('PUT', $path, $handler);
    }

    public function delete($path, $handler) {
        $this->add('DELETE', $path, $handler);
    }
}
