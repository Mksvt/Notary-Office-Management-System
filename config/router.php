<?php
/**
 * Базовий роутер для обробки запитів
 */

class Router {
    private $routes = [];
    private $notFoundCallback;

    public function add($method, $pattern, $controller, $action) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function get($pattern, $controller, $action) {
        $this->add('GET', $pattern, $controller, $action);
    }

    public function post($pattern, $controller, $action) {
        $this->add('POST', $pattern, $controller, $action);
    }

    public function setNotFound($callback) {
        $this->notFoundCallback = $callback;
    }

    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_GET['route'] ?? '/';
        
        // Видалити параметри запиту
        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        
        $requestUri = '/' . trim($requestUri, '/');
        if ($requestUri !== '/') {
            $requestUri = rtrim($requestUri, '/');
        }

        // DEBUG: Uncomment to see routing info
        // error_log("Router: Method=$requestMethod, URI=$requestUri");

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = '#^' . preg_replace('/\{([a-z_]+)\}/', '(?P<$1>[^/]+)', $route['pattern']) . '$#';
            
            // DEBUG: Uncomment to see pattern matching
            // error_log("Trying pattern: " . $route['pattern'] . " => " . $pattern);
            
            if (preg_match($pattern, $requestUri, $matches)) {
                // Видалити числові ключі з масиву параметрів
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                $controllerName = $route['controller'];
                $actionName = $route['action'];
                
                $controllerFile = ROOT_PATH . '/app/Controllers/' . $controllerName . '.php';
                
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    $controller = new $controllerName();
                    
                    if (method_exists($controller, $actionName)) {
                        call_user_func_array([$controller, $actionName], $params);
                        return;
                    }
                }
            }
        }

        // Маршрут не знайдено
        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            http_response_code(404);
            echo "404 - Сторінку не знайдено";
        }
    }
}
