<?php

namespace App\core;

class Router {

    private array $routes = [];

   
    public function get(string $path, callable|array $handler , array $middlewares = []) {
        $this->routes['GET'][$path]['handler'] = $handler;
        $this->routes['GET'][$path]['middlewares'] = $middlewares;
    }


    public function post(string $path, callable|array $handler ,array $middlewares = []) {
        $this->routes['POST'][$path]['handler'] = $handler;
        $this->routes['POST'][$path]['middlewares'] = $middlewares;
    }

    
    public function dispatch(Application $app) {
        $requestUri = $app->request->getUri(); 
        $requestMethod = $app->request->getMethod();
    
        if (!isset($this->routes[$requestMethod][$requestUri])) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }
    
        $route = $this->routes[$requestMethod][$requestUri];
        $handler = $route['handler'];
        $middlewares = $route['middlewares'] ?? [];
    
       
        foreach ($middlewares as $middleware) {
            $middlewareInstance = new $middleware();
            $response = $middlewareInstance->handle($app->request);
            
            if ($response === false) {
                break;
            }
        }
    
        if (is_callable($handler)) {
            return call_user_func($handler);
        } elseif (is_array($handler) && count($handler) === 2) {
            [$controller, $method] = $handler;
            if (class_exists($controller) && method_exists($controller, $method)) {
                $controllerInstance = new $controller($app);
                return call_user_func([$controllerInstance, $method]);
            } else {
                http_response_code(500);
                echo "Controller or method not found.";
            }
        }
    }
    
}