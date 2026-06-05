# Chapter 1: Reflection API

## Learning Objectives

- Inspect classes, methods, properties at runtime
- Use ReflectionClass, ReflectionMethod, ReflectionProperty
- Implement dependency injection containers
- Read attributes programmatically

---

## 1.1 What is Reflection?

Reflection allows PHP code to examine itself at runtime — inspecting classes, interfaces, methods, properties, and parameters.

```php
<?php
class User
{
    public function __construct(
        public string $name,
        private string $email,
        protected int $age = 0,
    ) {}

    public function greet(): string
    {
        return "Hello, {$this->name}";
    }

    private function validate(): bool
    {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

// Inspect the User class
$reflector = new ReflectionClass(User::class);

echo $reflector->getName();          // 'User'
echo $reflector->getShortName();     // 'User'
echo $reflector->getFileName();      // Full path to file

// Methods
$methods = $reflector->getMethods();
foreach ($methods as $method) {
    echo $method->getName();         // 'greet', 'validate'
    echo $method->isPublic() ? 'public' : 'private';
    echo $method->getNumberOfParameters();
}

// Properties
$properties = $reflector->getProperties();
foreach ($properties as $property) {
    echo $property->getName();       // 'name', 'email', 'age'
    echo $property->getType();       // 'string', 'string', 'int'
    echo $property->isPublic() ? 'public' : $property->isProtected() ? 'protected' : 'private';
}

// Constructor parameters
$constructor = $reflector->getConstructor();
$params = $constructor->getParameters();
foreach ($params as $param) {
    echo $param->getName();          // 'name', 'email', 'age'
    echo $param->getType();          // 'string', 'string', 'int'
    echo $param->isDefaultValueAvailable() ? 'has default' : 'required';
}
```

---

## 1.2 Building a Dependency Injection Container

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

    public function get(string $class): object
    {
        // Check bindings
        if (isset($this->bindings[$class])) {
            return ($this->bindings[$class])();
        }

        // Auto-resolve
        return $this->resolve($class);
    }

    private function resolve(string $class): object
    {
        $reflector = new ReflectionClass($class);
        
        if (!$reflector->isInstantiable()) {
            throw new RuntimeException("Cannot instantiate {$class}");
        }

        $constructor = $reflector->getConstructor();
        
        if ($constructor === null) {
            return new $class();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            
            if ($type === null || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new RuntimeException("Cannot resolve parameter {$parameter->getName()}");
                }
            } else {
                $dependencies[] = $this->get($type->getName());
            }
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}

// Usage
class Mailer
{
    public function send(string $to, string $subject): void
    {
        echo "Sending email to {$to}: {$subject}\n";
    }
}

class UserService
{
    public function __construct(
        private Mailer $mailer
    ) {}

    public function register(string $email): void
    {
        $this->mailer->send($email, 'Welcome!');
    }
}

$container = new Container();
$service = $container->get(UserService::class);
$service->register('alice@example.com');
// Output: Sending email to alice@example.com: Welcome!
```

---

## 1.3 Exercises

1. Use ReflectionClass to inspect a class and list all its methods with visibility
2. Build an autoloader using reflection to discover classes in a directory
3. Implement a simple DI container that auto-resolves constructor dependencies
4. Create a testing helper that runs all public methods of a class
5. Use reflection to implement a getter/setter generator

---

## Further Reading

- **Doc:** [PHP Reflection](https://www.php.net/manual/en/book.reflection.php)
- **Doc:** [ReflectionClass](https://www.php.net/manual/en/class.reflectionclass.php)
