<?php

declare(strict_types=1);

namespace MicroFramework;

use MicroFramework\Http\Request;
use MicroFramework\Http\Response;
use MicroFramework\Middleware\MiddlewareStack;
use MicroFramework\Middleware\RequestHandlerInterface;

class Router implements RequestHandlerInterface
{
    private array $routes = [];
    private array $namedRoutes = [];
    private array $groups = [];
    private MiddlewareStack $middlewareStack;
    private array $globalMiddleware = [];

    public function __construct()
    {
        $this->middlewareStack = new MiddlewareStack();
        $this->middlewareStack->setCoreHandler($this);
    }

    public function get(string $path, mixed $handler): Route
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, mixed $handler): Route
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, mixed $handler): Route
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, mixed $handler): Route
    {
        return $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, mixed $handler): Route
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    public function match(array $methods, string $path, mixed $handler): Route
    {
        $route = new Route('', $path, $handler);
        foreach ($methods as $method) {
            $route = $this->addRoute(strtoupper($method), $path, $handler);
        }
        return $route;
    }

    public function any(string $path, mixed $handler): Route
    {
        return $this->addRoute('ANY', $path, $handler);
    }

    public function group(string $prefix, callable $callback, array $middleware = []): void
    {
        $this->groups[] = ['prefix' => $prefix, 'middleware' => $middleware];
        $callback($this);
        array_pop($this->groups);
    }

    public function addGlobalMiddleware(Middleware\MiddlewareInterface|callable $middleware): void
    {
        $this->globalMiddleware[] = $middleware;
    }

    public function url(string $name, array $params = []): ?string
    {
        if (!isset($this->namedRoutes[$name])) {
            return null;
        }

        $path = $this->namedRoutes[$name];
        foreach ($params as $key => $value) {
            $path = str_replace("{{$key}}", (string) $value, $path);
        }

        return $path;
    }

    public function handle(Request $request): Response
    {
        foreach ($this->routes as $route) {
            $params = $route->matches($request);
            if ($params !== false) {
                foreach ($params as $key => $value) {
                    $request->setAttribute($key, $value);
                }

                $handler = $route->getHandler();
                return $this->resolveHandler($handler, $request);
            }
        }

        return new Response(404, json_encode([
            'error' => 'Not Found',
            'path'  => $request->getPath(),
        ]), ['content-type' => 'application/json']);
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->globalMiddleware as $mw) {
            if ($mw instanceof Middleware\MiddlewareInterface) {
                $this->middlewareStack->add($mw);
            } else {
                $this->middlewareStack->addCallable($mw);
            }
        }

        return $this->middlewareStack->handle($request);
    }

    private function addRoute(string $method, string $path, mixed $handler): Route
    {
        $prefix = '';
        $routeMiddleware = [];

        foreach ($this->groups as $group) {
            $prefix .= '/' . trim($group['prefix'], '/');
            $routeMiddleware = array_merge($routeMiddleware, $group['middleware']);
        }

        $fullPath = '/' . trim($prefix . '/' . trim($path, '/'), '/') ?: '/';

        $route = new Route($method, $fullPath, $handler);

        foreach ($routeMiddleware as $mw) {
            $route->middleware($mw);
        }

        $this->routes[] = $route;

        return $route;
    }

    private function resolveHandler(mixed $handler, Request $request): Response
    {
        if ($handler instanceof \Closure) {
            $ref = new \ReflectionFunction($handler);
            $params = $this->resolveCallableParams($ref->getParameters(), $request);
            $result = $handler(...$params);
            return $this->normalizeResponse($result);
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
            $instance = is_object($class) ? $class : new $class();
            $ref = new \ReflectionMethod($instance, $method);
            $params = $this->resolveCallableParams($ref->getParameters(), $request);
            $result = $ref->invokeArgs($instance, $params);
            return $this->normalizeResponse($result);
        }

        if (is_string($handler) && class_exists($handler)) {
            $instance = new $handler();
            if ($instance instanceof RequestHandlerInterface) {
                return $instance->handle($request);
            }
        }

        if (is_callable($handler)) {
            $result = $handler($request);
            return $this->normalizeResponse($result);
        }

        throw new \RuntimeException('Invalid route handler');
    }

    private function resolveCallableParams(array $refParams, Request $request): array
    {
        $params = [];
        foreach ($refParams as $refParam) {
            $type = $refParam->getType();
            if ($type instanceof \ReflectionNamedType) {
                if ($type->getName() === Request::class) {
                    $params[] = $request;
                } elseif ($type->getName() === Response::class) {
                    $params[] = new Response();
                } elseif ($type->isBuiltin()) {
                    $name = $refParam->getName();
                    $params[] = $request->getAttribute($name) ?? ($refParam->isDefaultValueAvailable() ? $refParam->getDefaultValue() : null);
                } else {
                    $params[] = $refParam->isDefaultValueAvailable() ? $refParam->getDefaultValue() : null;
                }
            } elseif ($type === null) {
                $name = $refParam->getName();
                $params[] = $request->getAttribute($name);
            }
        }
        return $params;
    }

    private function normalizeResponse(mixed $result): Response
    {
        if ($result instanceof Response) {
            return $result;
        }
        if (is_string($result)) {
            return new Response(200, $result);
        }
        if (is_array($result) || is_object($result)) {
            return Response::json($result);
        }
        if ($result === null) {
            return new Response(204);
        }
        return new Response(200, (string) $result);
    }
}
