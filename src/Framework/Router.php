<?php

namespace App\Framework;

use App\Utils\InputSanitizer;

class Router
{
    private array $routes = [];

    public function addRoute($method, $path, $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch($method, $path)
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                return call_user_func($route['handler']);
            }
        }

        http_response_code(404);
        echo "404: No route found for method '" . $method . "' and path '" . InputSanitizer::cleanString($path) . "'";
    }
}