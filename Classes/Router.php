<?php

namespace Classes;


use Classes\Middleware\Middleware;

class Router {

    // start with defing a routes variables which we will put routes and methods inside
    protected $routes = [];

    protected function add_route($method, $uri, $controller, $middleware = null) {
        $this->routes[] = compact('method', 'uri', 'controller', 'middleware');
        return $this;
    }

    public function post($uri, $controller) {
        return $this->add_route('POST', $uri, $controller);
    }

    public function delete($uri, $controller) {
        return $this->add_route('DELETE', $uri, $controller);
    }

    public function get($uri, $controller) {
        return $this->add_route('GET', $uri, $controller);
    }

    public function put($uri, $controller) {
        return $this->add_route('PUT', $uri, $controller);
    }

    public function patch($uri, $controller) {
        return $this->add_route('PATCH', $uri, $controller);
    }

    public function only (string $user) {
        $this->routes[array_key_last($this->routes)]['middleware'] = $user;
    }

    public function routeToController($uri, $requestMethod) {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && strtoupper($route['method']) === $requestMethod) {
                Middleware::resolve($route['middleware']);

                return base_path($route['controller']);
            }
        }
        abort(); // only called if no matching route
    }


}