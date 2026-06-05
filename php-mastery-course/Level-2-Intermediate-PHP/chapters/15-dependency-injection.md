# Chapter 15: Dependency Injection

## Learning Objectives

- Understand dependency injection principles
- Implement constructor injection
- Create a simple DI container
- Apply DIP (Dependency Inversion Principle)

---

## 15.1 Why Dependency Injection?

```php
<?php
// ❌ WITHOUT DI: Tight coupling, hard to test
class UserController
{
    private Database $db;

    public function __construct()
    {
        // Hard-coded dependency - can't swap or mock
        $this->db = new Database('localhost', 'root', 'secret');
    }

    public function show(int $id): array
    {
        return $this->db->query("SELECT * FROM users WHERE id = {$id}");
    }
}

// ✅ WITH DI: Loose coupling, testable
interface UserRepositoryInterface
{
    public function find(int $id): ?User;
}

class MySQLUserRepository implements UserRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function find(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetchObject(User::class) ?: null;
    }
}

class UserController
{
    // Dependency is injected, not created internally
    public function __construct(
        private UserRepositoryInterface $users
    ) {}

    public function show(int $id): ?User
    {
        return $this->users->find($id);
    }
}
```

---

## 15.2 Simple DI Container

```php
<?php
class Container
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    public function singleton(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = function () use ($factory) {
            if (!isset($this->instances[$abstract])) {
                $this->instances[$abstract] = $factory();
            }
            return $this->instances[$abstract];
        };
    }

    public function get(string $abstract): mixed
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]();
        }

        return $this->resolve($abstract);
    }

    private function resolve(string $class): object
    {
        $reflection = new ReflectionClass($class);
        
        $constructor = $reflection->getConstructor();
        if (!$constructor) {
            return $reflection->newInstance();
        }

        $params = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();
            if (!$type || $type->isBuiltin()) {
                if ($param->isDefaultValueAvailable()) {
                    $params[] = $param->getDefaultValue();
                } else {
                    throw new ContainerException(
                        "Cannot resolve {$param->getName()}"
                    );
                }
            } else {
                $params[] = $this->get($type->getName());
            }
        }

        return $reflection->newInstanceArgs($params);
    }

    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]);
    }
}

// Usage
$container = new Container();

// Bind interfaces to implementations
$container->bind(UserRepositoryInterface::class, function () use ($container) {
    return new MySQLUserRepository($container->get(PDO::class));
});

$container->singleton(PDO::class, function () {
    return new PDO('mysql:host=localhost;dbname=app', 'root', 'secret');
});

// Auto-resolve UserController
$controller = $container->get(UserController::class);
$user = $controller->show(42);
```

---

## 15.3 Exercises

1. Refactor a class with hard-coded dependencies to use constructor injection
2. Create a DI container with autowiring support
3. Implement a Mailer interface with SMTP and SendGrid implementations
4. Register everything in the container and resolve controllers automatically

---

## Further Reading

- **Doc:** [PHP-DI Documentation](https://php-di.org/doc/)
- **Book:** "Dependency Injection in PHP" by Matthias Noback
