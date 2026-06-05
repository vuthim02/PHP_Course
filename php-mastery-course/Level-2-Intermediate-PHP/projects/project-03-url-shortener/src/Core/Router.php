<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, callable|array $handler): void
    {
        $p = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        $this->routes[] = ['method' => $method, 'pattern' => '#^' . $p . '$#', 'handler' => $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = rtrim(parse_url($uri, PHP_URL_PATH), '/') ?: '/';
        foreach ($this->routes as $r) {
            if ($r['method'] !== $method) continue;
            if (preg_match($r['pattern'], $uri, $m)) {
                $p = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                $h = $r['handler'];
                if (is_array($h)) (new $h[0])->{$h[1]}(...$p);
                else $h($p);
                return;
            }
        }
        http_response_code(404);
        echo '404';
    }
}
