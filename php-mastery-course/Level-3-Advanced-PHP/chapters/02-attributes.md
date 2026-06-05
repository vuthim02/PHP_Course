# Chapter 2: Attributes (Annotations)

## Learning Objectives

- Create and use PHP 8 attributes
- Read attributes with reflection
- Build attribute-based routing

---

## 2.1 Defining Attributes

```php
<?php
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public string $path,
        public string $method = 'GET',
    ) {}
}

#[Attribute(Attribute::TARGET_METHOD)]
class Middleware
{
    public function __construct(
        public string ...$middleware,
    ) {}
}

#[Attribute(Attribute::TARGET_PROPERTY)]
class Validate
{
    public function __construct(
        public string $rule,
        public ?string $message = null,
    ) {}
}

#[Attribute(Attribute::TARGET_CLASS)]
class Model
{
    public function __construct(
        public string $table,
    ) {}
}
```

---

## 2.2 Using Attributes

```php
<?php
#[Model(table: 'users')]
class User
{
    #[Validate('required|email')]
    public string $email;

    #[Validate('required|min:8')]
    public string $password;

    #[Validate('optional')]
    public string $name;
}

#[Route('/api/users')]
class UserController
{
    public function __construct(private UserService $service) {}

    #[Route('/api/users', 'GET')]
    #[Middleware('auth', 'throttle:60,1')]
    public function index(): array
    {
        return $this->service->all();
    }

    #[Route('/api/users/{id}', 'GET')]
    #[Middleware('auth')]
    public function show(int $id): ?User
    {
        return $this->service->find($id);
    }

    #[Route('/api/users', 'POST')]
    #[Middleware('auth', 'admin')]
    public function store(): User
    {
        return $this->service->create($_POST);
    }
}
```

---

## 2.3 Reading Attributes

```php
<?php
class AttributeRouter
{
    private array $routes = [];

    public function registerControllers(string ...$controllers): void
    {
        foreach ($controllers as $controller) {
            $this->registerController($controller);
        }
    }

    private function registerController(string $controllerClass): void
    {
        $reflectionClass = new ReflectionClass($controllerClass);
        $classPrefix = '';

        // Read class-level route
        $classAttributes = $reflectionClass->getAttributes(Route::class);
        if (!empty($classAttributes)) {
            $classPrefix = $classAttributes[0]->newInstance()->path;
        }

        // Read method-level routes
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $attributes = $method->getAttributes(Route::class);
            
            foreach ($attributes as $attribute) {
                $route = $attribute->newInstance();
                $path = $classPrefix ? $classPrefix . $route->path : $route->path;
                
                // Read middleware
                $middleware = [];
                $middlewareAttrs = $method->getAttributes(Middleware::class);
                if (!empty($middlewareAttrs)) {
                    $middleware = $middlewareAttrs[0]->newInstance()->middleware;
                }

                $this->routes[strtoupper($route->method)][$path] = [
                    'controller' => $controllerClass,
                    'method' => $method->getName(),
                    'middleware' => $middleware,
                ];
            }
        }
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $method = strtoupper($method);
        $uri = parse_url($uri, PHP_URL_PATH);
        
        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            $regex = '#^' . preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern) . '$#';
            
            if (preg_match($regex, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $controller = new $handler['controller']();
                return $controller->{$handler['method']}(...$params);
            }
        }

        throw new \RuntimeException('Route not found', 404);
    }
}

// Usage
$router = new AttributeRouter();
$router->registerControllers(UserController::class);
$router->dispatch('GET', '/api/users');
```

---

## 2.4 Exercises

1. Create a `#[Validate]` attribute for form validation
2. Build a CLI command router using attributes
3. Create an `#[Event]` attribute for event listeners
4. Implement a DI container using `#[Inject]` attribute

---

## Further Reading

- **Doc:** [PHP Attributes](https://www.php.net/manual/en/language.attributes.php)
