# Chapter 12: Slim Framework

## Learning Objectives

- Build microservices with Slim
- Use middleware for cross-cutting concerns
- Implement DI with PHP-DI
- Test Slim applications

---

## 12.1 Basic Application

```php
<?php
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Middleware
$app->addErrorMiddleware(true, true, true);

// CORS middleware
$app->add(function (Request $request, RequestHandler $handler): Response {
    if ($request->getMethod() === 'OPTIONS') {
        $response = new \Slim\Psr7\Response();
    } else {
        $response = $handler->handle($request);
    }

    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Routes
$app->get('/api/users', function (Request $request, Response $response) {
    $users = UserRepository::findAll();
    $payload = json_encode(['data' => $users]);
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api/users/{id}', function (Request $request, Response $response, array $args) {
    $user = UserRepository::find((int)$args['id']);
    
    if (!$user) {
        $response->getBody()->write(json_encode(['error' => 'Not found']));
        return $response->withStatus(404);
    }

    $response->getBody()->write(json_encode(['data' => $user]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/api/users', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $user = UserRepository::create($data);
    
    $response->getBody()->write(json_encode(['data' => $user]));
    return $response->withStatus(201);
});

// Group routes
$app->group('/api/admin', function (\Slim\Routing\RouteCollectorProxy $group) {
    $group->get('/dashboard', 'AdminController:dashboard');
    $group->get('/users', 'AdminController:users');
})->add(new AdminAuthMiddleware());

$app->run();
```

---

## 12.2 Exercises

1. Build a RESTful microservice with Slim
2. Add JWT authentication middleware
3. Implement validation middleware
4. Write unit tests with PHPUnit

---

## Further Reading

- **Doc:** [Slim Framework](https://www.slimframework.com/docs/v4/)
