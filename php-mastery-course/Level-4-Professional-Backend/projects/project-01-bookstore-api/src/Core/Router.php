<?php

declare(strict_types=1);

namespace App\Core;

use Closure;

class Router
{
    private array $routes = [];
    private array $globalMiddleware = [];
    private string $prefix = '';

    public function addRoute(string $method, string $pattern, callable|array $handler, array $middleware = []): self
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $this->prefix . $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function get(string $pattern, callable|array $handler, array $middleware = []): self
    {
        return $this->addRoute('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, callable|array $handler, array $middleware = []): self
    {
        return $this->addRoute('POST', $pattern, $handler, $middleware);
    }

    public function put(string $pattern, callable|array $handler, array $middleware = []): self
    {
        return $this->addRoute('PUT', $pattern, $handler, $middleware);
    }

    public function patch(string $pattern, callable|array $handler, array $middleware = []): self
    {
        return $this->addRoute('PATCH', $pattern, $handler, $middleware);
    }

    public function delete(string $pattern, callable|array $handler, array $middleware = []): self
    {
        return $this->addRoute('DELETE', $pattern, $handler, $middleware);
    }

    public function group(string $prefix, Closure $callback, array $middleware = []): self
    {
        $previousPrefix = $this->prefix;
        $this->prefix .= $prefix;

        $callback($this);

        $this->prefix = $previousPrefix;

        return $this;
    }

    public function addGlobalMiddleware(callable|array $middleware): self
    {
        $this->globalMiddleware[] = $middleware;
        return $this;
    }

    public function resolve(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        foreach ($this->routes as $route) {
            $pattern = $this->patternToRegex($route['pattern']);
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $request->setRouteParam($params['id'] ?? null);

                $middlewareChain = array_merge(
                    $this->globalMiddleware,
                    $route['middleware']
                );

                $handler = $route['handler'];

                $response = $this->runMiddlewareChain($request, $middlewareChain, function (Request $req) use ($handler) {
                    if (is_array($handler) && count($handler) === 2) {
                        [$class, $method] = $handler;
                        $controller = new $class();
                        return $controller->$method($req);
                    }
                    return call_user_func($handler, $req);
                });

                if ($response instanceof Response) {
                    return $response;
                }

                if (is_array($response) || is_object($response)) {
                    return Response::success($response);
                }

                return new Response($response);
            }
        }

        return Response::error('Not Found', 404);
    }

    private function patternToRegex(string $pattern): string
    {
        $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }

    private function runMiddlewareChain(Request $request, array $middleware, callable $handler): mixed
    {
        if (empty($middleware)) {
            return $handler($request);
        }

        $middlewareInstance = array_shift($middleware);

        if (is_array($middlewareInstance)) {
            [$class, $method] = $middlewareInstance;
            $instance = new $class();
            return $instance->$method($request, function (Request $req) use ($middleware, $handler) {
                return $this->runMiddlewareChain($req, $middleware, $handler);
            });
        }

        if (is_string($middlewareInstance)) {
            $instance = new $middlewareInstance();
            return $instance->process($request, function (Request $req) use ($middleware, $handler) {
                return $this->runMiddlewareChain($req, $middleware, $handler);
            });
        }

        return $middlewareInstance($request, function (Request $req) use ($middleware, $handler) {
            return $this->runMiddlewareChain($req, $middleware, $handler);
        });
    }
}
