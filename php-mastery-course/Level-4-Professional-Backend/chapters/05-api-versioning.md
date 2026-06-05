# Chapter 5: API Versioning

## Learning Objectives

- Implement URI-based versioning
- Use header-based versioning
- Handle breaking changes
- Maintain backward compatibility

---

## 5.1 URI Versioning Strategy

```php
<?php
// /api/v1/users
// /api/v2/users

class VersionRouter
{
    private array $routes = [];

    public function add(string $version, Router $router): void
    {
        $this->routes[$version] = $router;
    }

    public function dispatch(string $method, string $uri): mixed
    {
        // Extract version from URI: /api/v1/users → version=1
        if (preg_match('#^/api/v(\d+)#', $uri, $matches)) {
            $version = (int)$matches[1];
            $router = $this->routes['v' . $version] ?? null;
            
            if ($router) {
                return $router->dispatch($method, $uri);
            }
        }

        // Default to latest version
        return $this->routes['v2']->dispatch($method, $uri);
    }
}

// Version 1 controller
class UserControllerV1
{
    public function index(): never
    {
        $users = User::all();
        ApiResponse::success($users->toArray());
    }
}

// Version 2 controller (breaking changes)
class UserControllerV2
{
    public function index(): never
    {
        $users = User::paginate();
        ApiResponse::paginated(
            UserResource::collection($users),
            $users->total(),
            $users->currentPage(),
            $users->perPage()
        );
    }
}

// Register versions
$router = new VersionRouter();
$router->add('v1', $v1Router);
$router->add('v2', $v2Router);
```

---

## 5.2 Exercises

1. Implement URI versioning for a product API
2. Add Accept header versioning as an alternative
3. Create a migration guide for v1 to v2
4. Build a version negotiation middleware

---

## Further Reading

- **Doc:** [API Versioning Best Practices](https://restfulapi.net/versioning/)
