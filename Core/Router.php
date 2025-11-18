<?php

namespace Core;

use Core\Middleware\Middleware;

class Router {

    protected $routes = [];

    protected function addRoute ($method, $uri, $controller, $middleware = null) {
        $this->routes[] = compact('method', 'uri', 'controller', 'middleware');
        return $this;
    }

    public function get($uri, $controller) {
        return $this->addRoute('GET', $uri, $controller);
    }

    public function post($uri, $controller) {
        return $this->addRoute('POST', $uri, $controller);
    }

    public function delete($uri, $controller) {
        return $this->addRoute('DELETE', $uri, $controller);
    }

    public function patch($uri, $controller) {
        return $this->addRoute('PATCH', $uri, $controller);
    }

    public function put($uri, $controller) {
        return $this->addRoute('PUT', $uri, $controller);
    }

    public function only($user) {
        $this->routes[array_key_last($this->routes)]['middleware'] = $user;
    }

    public function routeToController($uri, $requestMethod) {
        foreach($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $requestMethod) {
                Middleware::resolve($route['middleware']);

                return require base_path($route['controller']);
            }
        }
        abort();
    }

}