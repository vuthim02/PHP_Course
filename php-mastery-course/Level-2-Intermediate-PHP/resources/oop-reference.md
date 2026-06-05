# Level 2 — OOP Quick Reference

## Classes & Objects

```php
class User
{
    // Properties
    public string $name;
    protected string $email;
    private int $age;
    public static int $count = 0;
    public readonly string $id;

    // Constructor (PHP 8+ promoted properties)
    public function __construct(
        string $name,
        private string $password
    ) {
        $this->name = $name;
        self::$count++;
        $this->id = uniqid();
    }

    // Methods
    public function greet(): string
    {
        return "Hello, {$this->name}!";
    }

    // Static method
    public static function getCount(): int
    {
        return self::$count;
    }

    // Destructor
    public function __destruct()
    {
        self::$count--;
    }
}

// Usage
$user = new User('Alice', 'secret');
echo $user->greet();
echo User::getCount();
```

## Visibility

| Modifier | Same Class | Subclass | Outside |
|----------|-----------|----------|---------|
| `public` | ✅ | ✅ | ✅ |
| `protected` | ✅ | ✅ | ❌ |
| `private` | ✅ | ❌ | ❌ |

## Inheritance

```php
class Admin extends User
{
    public function greet(): string
    {
        return "Admin: {$this->name}";
        // $this->email is accessible (protected)
        // $this->age is NOT accessible (private in User)
    }

    // Override constructor
    public function __construct(
        string $name,
        string $password,
        private string $role
    ) {
        parent::__construct($name, $password);
    }
}

// Final class — cannot be extended
final class Database { ... }

// Final method — cannot be overridden
class Base {
    final public function connect(): void { ... }
}
```

## Abstract Classes

```php
abstract class Shape
{
    protected string $color;

    public function __construct(string $color)
    {
        $this->color = $color;
    }

    // Must be implemented by child
    abstract public function area(): float;

    // Can have concrete methods
    public function getColor(): string
    {
        return $this->color;
    }
}

class Circle extends Shape
{
    public function __construct(
        string $color,
        private float $radius
    ) {
        parent::__construct($color);
    }

    public function area(): float
    {
        return pi() * $this->radius ** 2;
    }
}
```

## Interfaces

```php
// Define contract
interface LoggerInterface
{
    public function log(string $message): void;
    public function getLogs(): array;
}

// Multiple interfaces supported
interface JsonSerializable
{
    public function toJson(): string;
}

// Implement
class FileLogger implements LoggerInterface, JsonSerializable
{
    public function log(string $message): void
    {
        // Write to file
    }

    public function getLogs(): array
    {
        return file('logs.txt');
    }

    public function toJson(): string
    {
        return json_encode($this->getLogs());
    }
}
```

## Traits

```php
trait Timestampable
{
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function initializeTimestamps(): void
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function touch(): void
    {
        $this->updatedAt = new DateTime();
    }
}

trait SoftDeletable
{
    private ?DateTime $deletedAt = null;

    public function delete(): void
    {
        $this->deletedAt = new DateTime();
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}

class Post
{
    use Timestampable, SoftDeletable;

    public function __construct()
    {
        $this->initializeTimestamps();
    }
}
```

## Magic Methods

```php
class MagicExample
{
    // Called when accessing inaccessible property
    public function __get(string $name): mixed { ... }

    // Called when setting inaccessible property
    public function __set(string $name, mixed $value): void { ... }

    // Called by isset() / empty()
    public function __isset(string $name): bool { ... }

    // Called by unset()
    public function __unset(string $name): void { ... }

    // Called when calling inaccessible method
    public function __call(string $name, array $args): mixed { ... }

    // Static version
    public static function __callStatic(string $name, array $args): mixed { ... }

    // Object as string
    public function __toString(): string { ... }

    // Object as function
    public function __invoke(mixed ...$args): mixed { ... }

    // Serialize/unserialize
    public function __serialize(): array { ... }
    public function __unserialize(array $data): void { ... }
}
```

## Design Patterns (Creational)

```php
// Singleton
final class Database
{
    private static ?self $instance = null;

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct() { }
    private function __clone(): void { }
    public function __wakeup(): void {
        throw new \Exception("Cannot unserialize singleton");
    }
}

// Factory
class LoggerFactory
{
    public static function create(string $type): LoggerInterface
    {
        return match ($type) {
            'file' => new FileLogger(),
            'db' => new DatabaseLogger(),
            default => throw new \InvalidArgumentException()
        };
    }
}

// Builder
class QueryBuilder
{
    private string $select = '*';
    private string $from = '';
    private array $where = [];

    public function select(string $columns): self
    {
        $this->select = $columns;
        return $this;
    }

    public function from(string $table): self
    {
        $this->from = $table;
        return $this;
    }

    public function where(string $condition): self
    {
        $this->where[] = $condition;
        return $this;
    }

    public function build(): string
    {
        $sql = "SELECT {$this->select} FROM {$this->from}";
        if ($this->where) {
            $sql .= ' WHERE ' . implode(' AND ', $this->where);
        }
        return $sql;
    }
}

$query = (new QueryBuilder())
    ->select('id, name')
    ->from('users')
    ->where('active = 1')
    ->build();
```

## Design Patterns (Structural)

```php
// Adapter — make incompatible interfaces work together
interface PaymentProcessor
{
    public function charge(float $amount): bool;
}

class StripeProcessor implements PaymentProcessor { ... }

class PayPalAdapter implements PaymentProcessor
{
    public function __construct(private PayPalAPI $paypal) { }

    public function charge(float $amount): bool
    {
        return $this->paypal->sendPayment($amount);
    }
}

// Decorator — add behavior without changing class
interface Notifier
{
    public function send(string $message): void;
}

class EmailNotifier implements Notifier { ... }

class SlackNotifierDecorator implements Notifier
{
    public function __construct(private Notifier $wrapped) { }

    public function send(string $message): void
    {
        $this->wrapped->send($message);
        // Also send via Slack
    }
}
```

## Design Patterns (Behavioral)

```php
// Strategy — interchangeable algorithms
interface SortStrategy
{
    public function sort(array $data): array;
}

class QuickSort implements SortStrategy { ... }
class BubbleSort implements SortStrategy { ... }

class Sorter
{
    public function __construct(private SortStrategy $strategy) { }

    public function sort(array $data): array
    {
        return $this->strategy->sort($data);
    }
}

// Observer — event-driven
interface Subscriber
{
    public function handle(Event $event): void;
}

class EventBus
{
    private array $subscribers = [];

    public function subscribe(string $event, Subscriber $sub): void
    {
        $this->subscribers[$event][] = $sub;
    }

    public function dispatch(Event $event): void
    {
        foreach ($this->subscribers[$event->name] ?? [] as $sub) {
            $sub->handle($event);
        }
    }
}
```

## Dependency Injection

```php
// Without DI (tight coupling)
class UserController
{
    private MySQLDatabase $db;

    public function __construct()
    {
        $this->db = new MySQLDatabase();  // Hard to test
    }
}

// With DI (loose coupling)
class UserController
{
    public function __construct(
        private DatabaseInterface $db
    ) { }
}

// Wiring it up
$container = new SimpleContainer();
$container->set(DatabaseInterface::class, fn() => new MySQLDatabase());
$controller = new UserController($container->get(DatabaseInterface::class));
```

## Autoloading (PSR-4)

```php
// Composer autoloading
{
    "autoload": {
        "psr-4": {
            "App\\": "src/",
            "App\\Tests\\": "tests/"
        }
    }
}

// Then just:
use App\Models\User;
use App\Services\PaymentService;
```

## Late Static Binding

```php
class Base
{
    public static function who(): string
    {
        return __CLASS__;      // Always "Base"
    }

    public static function newWho(): string
    {
        return static::class;  // Resolves at runtime
    }

    public static function create(): static
    {
        return new static();   // Returns the called class
    }
}

class Child extends Base { }

echo Child::who();     // "Base"
echo Child::newWho();  // "Child"
$obj = Child::create();  // Instance of Child
```

## Best Practices

```php
declare(strict_types=1);

// Favor composition over inheritance
// Program to interfaces, not implementations
// Keep classes small (Single Responsibility)
// Use readonly properties for immutable values
// Use promoted constructor properties
// Always declare return types
// Never use public properties — use getters or readonly
```
