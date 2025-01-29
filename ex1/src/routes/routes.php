<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function addRoute(
        string $method,
        string $path,
        callable $handler
    ): void {
        $this->routes[] = [
            "method" => $method,
            "path" => $path,
            "handler" => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): string
        {
            foreach ($this->routes as $route) {
                if ($route['method'] !== $method) {
                    continue;
                }

                $pathRegex = str_replace('/', '\/', $route['path']);
                $pathRegex = preg_replace('/\{([^\/]+)\}/', '([^\/]+)', $pathRegex);
                $pathRegex = '/^' . $pathRegex . '$/';

                preg_match_all('/\{([^\/]+)\}/', $route['path'], $paramNames);
                $paramNames = $paramNames[1] ?? []; // Get the parameter names without brackets

                if (preg_match($pathRegex, $uri, $matches)) {
                    array_shift($matches);

                    $params = array_combine($paramNames, $matches) ?? [];

                    return call_user_func_array($route['handler'], array_values($params));
                }
            }

            return json_encode([
                'status' => 'error',
                'message' => 'Route not found',
                'requested_path' => $uri,
                'requested_method' => $method,
                'available_routes' => array_map(function($route) {
                    return $route['path'];
                }, $this->routes)
            ]);
        }

    private function convertRouteToRegex(string $route): string
    {
        $route = preg_quote($route, "/");

        $route = preg_replace(
            "/\\\{([a-zA-Z0-9_]+)\\\}/",
            '(?P<$1>[^/]+)',
            $route
        );

        return "/^" . $route . '$/';
    }
}
