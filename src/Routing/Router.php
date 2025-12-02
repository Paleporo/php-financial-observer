<?php

declare(strict_types=1);

namespace FinancialObserver\Routing;

use FinancialObserver\Http\JsonResponse;

class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function dispatch(string $method, string $path): void
    {
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler) {
            $data = $handler();
            JsonResponse::send(['data' => $data]);
            return;
        }

        foreach ($this->routes[$method] ?? [] as $routePath => $routeHandler) {
            $params = $this->matchDynamicRoute($routePath, $path);

            if ($params !== null) {
                $data = $routeHandler(...$params);
                JsonResponse::send(['data' => $data]);
                return;
            }
        }

        JsonResponse::send(['error' => 'Route not found'], 404);
    }

    private function matchDynamicRoute(string $routePath, string $incomingPath): ?array
    {
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if ($pattern === null) {
            return null;
        }

        if (preg_match($pattern, $incomingPath, $matches)) {
            array_shift($matches);
            return $matches;
        }

        return null;
    }
}
