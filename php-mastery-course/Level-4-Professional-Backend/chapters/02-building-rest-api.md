# Chapter 2: Building a REST API in PHP

## Learning Objectives

- Design a RESTful API architecture
- Implement routing, controllers, and middleware
- Handle request validation and response formatting
- Build a complete API from scratch

---

## 2.1 API Router

```php
<?php
namespace App\Routing;

class Router
{
    private array $routes = [];
    private array $middleware = [];
    private array $groupMiddleware = [];

    public function group(array $attributes, callable $callback): void
    {
        $previousMiddleware = $this->groupMiddleware;
        if (isset($attributes['middleware'])) {
            $this->groupMiddleware = array_merge(
                $this->groupMiddleware,
                (array)$attributes['middleware']
            );
        }
        $callback($this);
        $this->groupMiddleware = $previousMiddleware;
    }

    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, string $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, string $handler): void
    {
        $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, string $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $this->pathToPattern($path),
            'handler' => $handler,
            'middleware' => $this->groupMiddleware,
        ];
    }

    private function pathToPattern(string $path): string
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            
            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Run middleware
                foreach ($route['middleware'] as $middleware) {
                    $instance = new $middleware();
                    $instance->handle($params);
                }
                
                [$controller, $action] = explode('@', $route['handler']);
                $class = "App\\Http\\Controllers\\{$controller}";
                $instance = new $class();
                
                return $instance->$action(...$params);
            }
        }

        ApiResponse::error('Route not found', 404);
    }
}
```

---

## 2.2 API Controller

```php
<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function __construct(private UserRepository $users) {}

    public function index(): never
    {
        $users = $this->users->paginate(
            page: (int)($_GET['page'] ?? 1),
            perPage: min((int)($_GET['per_page'] ?? 15), 100),
        );

        ApiResponse::paginated(
            $users->items(),
            $users->total(),
            $users->currentPage(),
            $users->perPage()
        );
    }

    public function show(int $id): never
    {
        $user = $this->users->find($id);
        
        if (!$user) {
            ApiResponse::error('User not found', 404);
        }

        ApiResponse::success(new UserResource($user));
    }

    public function store(): never
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $validator = new StoreUserRequest($data);
        
        if ($validator->fails()) {
            ApiResponse::error('Validation failed', 422, $validator->errors());
        }

        $user = $this->users->create($validator->validated());
        ApiResponse::success(new UserResource($user), 201);
    }
}
```

---

## 2.3 Exercises

1. Build a complete REST API router with middleware support
2. Create controllers for CRUD operations on a resource
3. Implement request validation
4. Add pagination with proper meta information

---

## Further Reading

- **Doc:** [PSR-7 HTTP Messages](https://www.php-fig.org/psr/psr-7/)
- **Resource:** [JSON:API Specification](https://jsonapi.org/)
