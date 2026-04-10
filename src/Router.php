<?php

declare(strict_types=1);

namespace App;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $path): mixed
    {
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo 'Page not found';
            return null;
        }

        if (is_array($handler) && is_string($handler[0])) {
            $className = $handler[0];
            $methodName = $handler[1];
            $instance = new $className();
            return $instance->$methodName();
        }

        return call_user_func($handler);
    }
}
