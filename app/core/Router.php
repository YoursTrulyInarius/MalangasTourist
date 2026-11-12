<?php
// app/core/Router.php

class Router {
    private $routes = [];

    public function add($method, $route, $controller, $action) {
        // Remove leading/trailing slashes for normalization
        $route = trim($route, '/');
        $this->routes[] = [
            'method' => strtoupper($method),
            'route' => $route,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch($url, $method) {
        $url = trim($url, '/');
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method || $route['method'] === 'ANY') {
                // Simple string match or basic regex for variables like {id}
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $route['route']);
                $pattern = "@^" . $pattern . "$@D";
                
                if (preg_match($pattern, $url, $matches)) {
                    // Extract named parameters
                    $params = [];
                    foreach ($matches as $key => $value) {
                        if (is_string($key)) {
                            $params[$key] = $value;
                        }
                    }

                    require_once "../app/controllers/{$route['controller']}.php";
                    $controller = new $route['controller']();
                    
                    // Call the action with parameters
                    call_user_func_array([$controller, $route['action']], $params);
                    return;
                }
            }
        }

        http_response_code(404);
        echo "404 Not Found - URL: " . htmlspecialchars($url);
    }
}
