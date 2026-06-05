# PHP Router & Micro-framework

A minimalist PHP micro-framework featuring a regex-powered router with named routes and groups, a PSR-15 style middleware pipeline, and a dependency injection container with Reflection-based autowiring.

## Advanced PHP Concepts Demonstrated

| Concept | File |
|---|---|
| **Reflection API (DI Autowiring)** | `src/Container/Container.php:85-125` — `ReflectionClass`, `ReflectionMethod`, `ReflectionParameter` for constructor injection |
| **Closures as Route Handlers** | `examples/blog-app.php:43-55` — Route handlers registered as anonymous functions |
| **SPL Arrays for Route Storage** | `src/Router.php:13-14` — Typed array for route collection with indexed iteration |
| **Streams for Request Body** | `src/Http/Request.php:30` — `file_get_contents('php://input')` stream |
| **Regular Expressions** | `src/Route.php:69-80` — `preg_match` and `preg_replace_callback` for route parameter matching |
| **Late Static Binding** | `src/Http/Response.php:76-84` — `static` return types for `withStatus()`, `withBody()`, `withHeader()` |
| **Anonymous Classes** | `src/Middleware/MiddlewareStack.php:33-38` — Wrapping callables as MiddlewareInterface |
| **Advanced Type System** | `src/Container/Container.php:85-125` — `ReflectionNamedType`, `isBuiltin()`, nullables, default values |
| **Variadic Arguments** | `src/Router.php:35-40` — `match(array $methods, ...)` supporting multiple HTTP methods |

## Features

- **Full REST Router** — `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `ANY` + `match()`
- **Regex Path Parameters** — `{id}` with custom constraints `->where('id', '\d+')`
- **Named Routes** — Generate URLs from route names with `$router->url('posts.show', ['id' => 5])`
- **Route Groups** — Prefix and scoped middleware with `$router->group('admin', fn() => ...)`
- **Middleware Pipeline** — PSR-15 style with `MiddlewareInterface`, callable middleware, and `addGlobalMiddleware()`
- **DI Container** — Autowiring with Reflection, singleton/transient binding, aliases, `ServiceProviderInterface`
- **Request/Response** — Immutable HTTP abstractions with `with*()` methods, `json()`, `redirect()`
- **Handler Resolution** — Closures, `[Class, method]` arrays, invokable objects, automatic parameter injection

## Setup

```bash
composer install
```

## Run Demo

```bash
php examples/blog-app.php
```

## Usage

```php
<?php

use MicroFramework\Router;
use MicroFramework\Http\Request;
use MicroFramework\Http\Response;
use MicroFramework\Container\Container;

$container = new Container();
$router = new Router();

// Define routes
$router->get('/posts/{id}', function (int $id): Response {
    return Response::json(['id' => $id, 'title' => 'Post Title']);
})->where('id', '\d+')->name('posts.show');

$router->post('/posts', function (Request $req): Response {
    $data = $req->getParsedBody();
    return Response::json($data, 201);
});

// Route groups
$router->group('api', function (Router $router) {
    $router->get('/status', fn() => Response::json(['status' => 'ok']));
});

// Middleware
$router->addGlobalMiddleware(function (Request $req, callable $next): Response {
    $start = microtime(true);
    $res = $next($req);
    // log timing
    return $res;
});

// Dispatch
$response = $router->dispatch(Request::fromGlobals());
$response->send();

// Named URL generation
echo $router->url('posts.show', ['id' => 42]); // /posts/42
```

## What Makes It "Advanced PHP"

This project brings together **Reflection API** for DI autowiring (inspecting constructor parameters, resolving type-hinted dependencies recursively), **anonymous functions and classes** for route handlers and middleware wrappers, **regular expressions** for dynamic route matching with parameter constraints, **SPL data structures** for route storage and matching, and **streams** (`php://input`) for request body parsing. The **late static binding** pattern in the Response class enables immutable message mutations. The middleware pipeline demonstrates functional composition of closures in a PSR-15 compatible architecture.
