<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $p, callable|array $h, array $m = []): void { $this->add('GET', $p, $h, $m); }
    public function post(string $p, callable|array $h, array $m = []): void { $this->add('POST', $p, $h, $m); }

    private function add(string $method, string $path, callable|array $handler, array $middleware): void
    {
        $p = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        $this->routes[] = ['method' => $method, 'pattern' => '#^' . $p . '$#', 'handler' => $handler, 'middleware' => $middleware];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = rtrim(parse_url($uri, PHP_URL_PATH), '/') ?: '/';
        foreach ($this->routes as $r) {
            if ($r['method'] !== $method) continue;
            if (preg_match($r['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                foreach ($r['middleware'] as $mw) { (new $mw())->handle(); }
                $h = $r['handler'];
                if (is_array($h)) (new $h[0])->{$h[1]}(...$params);
                else $h($params);
                return;
            }
        }
        http_response_code(404);
        echo '404';
    }
}
