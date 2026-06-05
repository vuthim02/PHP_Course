<?php

declare(strict_types=1);

namespace MicroFramework;

use MicroFramework\Http\Request;

class Route
{
    private array $middleware = [];
    private ?string $name = null;
    private array $patterns = [];

    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly mixed $handler,
    ) {}

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHandler(): mixed
    {
        return $this->handler;
    }

    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function middleware(Middleware\MiddlewareInterface|callable $middleware): static
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function where(string $param, string $pattern): static
    {
        $this->patterns[$param] = $pattern;
        return $this;
    }

    public function getPatterns(): array
    {
        return $this->patterns;
    }

    public function matches(Request $request): bool|array
    {
        if (strtoupper($this->method) !== strtoupper($request->getMethod())) {
            return false;
        }

        $path = $request->getPath();
        $path = '/' . trim(parse_url($path, PHP_URL_PATH) ?: $path, '/');
        $routePath = '/' . trim($this->path, '/');

        if ($routePath === $path) {
            return [];
        }

        $pattern = $this->buildPattern($routePath);
        if (preg_match($pattern, $path, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return false;
    }

    private function buildPattern(string $routePath): string
    {
        $pattern = preg_replace_callback(
            '/\{(\w+)\}/',
            function (array $m) {
                $param = $m[1];
                $regex = $this->patterns[$param] ?? '[^/]+';
                return "(?P<$param>$regex)";
            },
            $routePath
        );

        return '#^' . $pattern . '$#';
    }
}
