<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $p, callable|array $h): void { $this->add('GET', $p, $h); }
    public function post(string $p, callable|array $h): void { $this->add('POST', $p, $h); }

    private function add(string $m, string $p, callable|array $h): void
    {
        $p = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $p);
        $this->routes[] = ['method' => $m, 'pattern' => '#^' . $p . '$#', 'handler' => $h];
    }

    public function dispatch(string $m, string $u): void
    {
        $u = rtrim(parse_url($u, PHP_URL_PATH), '/') ?: '/';
        foreach ($this->routes as $r) {
            if ($r['method'] !== $m) continue;
            if (preg_match($r['pattern'], $u, $matches)) {
                $p = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
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
