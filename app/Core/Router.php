<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class Router
{
    private array $routes = [];

    public function get(string $path, array $action): void
    {
        $this->add('GET', $path, $action);
    }

    public function post(string $path, array $action): void
    {
        $this->add('POST', $path, $action);
    }

    private function add(string $method, string $path, array $action): void
    {
        $this->routes[$method][$path] = $action;
    }

    public function dispatch(Request $request, Container $container): mixed
    {
        $method = $request->method();
        $path = rtrim($request->uri(), '/') ?: '/';
        $action = $this->routes[$method][$path] ?? null;

        if ($action === null) {
            http_response_code(404);
            echo '404 - Route not found';
            return null;
        }

        [$class, $handler] = $action;
        $controller = $container->get($class);

        if (!method_exists($controller, $handler)) {
            throw new RuntimeException("Handler [$handler] not found on [$class].");
        }

        return $controller->{$handler}($request);
    }
}
