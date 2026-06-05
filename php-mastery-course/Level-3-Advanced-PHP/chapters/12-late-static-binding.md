# Chapter 12: Late Static Binding

## Learning Objectives

- Understand `static::` vs `self::`
- Use `get_called_class()`
- Implement the static factory pattern
- Apply late static binding in real scenarios

---

## 12.1 The Problem

```php
<?php
// ❌ Without late static binding
class BaseModel
{
    protected static string $table = 'base';

    public static function getTable(): string
    {
        return self::$table;  // Always 'base', even when called from child
    }

    public static function find(int $id): string
    {
        // self:: always refers to BaseModel
        return "SELECT * FROM " . self::getTable() . " WHERE id = {$id}";
    }
}

class UserModel extends BaseModel
{
    protected static string $table = 'users';
}

echo UserModel::getTable(); // 'base' ❌ Should be 'users'
echo UserModel::find(1);    // SELECT * FROM base WHERE id = 1 ❌

// ✅ With late static binding
class BaseModelCorrect
{
    protected static string $table = 'base';

    public static function getTable(): string
    {
        return static::$table;  // Resolves at runtime
    }

    public static function find(int $id): string
    {
        return "SELECT * FROM " . static::getTable() . " WHERE id = {$id}";
    }

    public static function create(array $data): static
    {
        return new static();  // Returns the actual called class
    }
}

class User extends BaseModelCorrect
{
    protected static string $table = 'users';
}

class Admin extends BaseModelCorrect
{
    protected static string $table = 'admins';
}

echo User::getTable();  // 'users' ✅
echo User::find(1);     // SELECT * FROM users WHERE id = 1 ✅
echo Admin::find(1);    // SELECT * FROM admins WHERE id = 1 ✅

$user = User::create(['name' => 'Alice']);
echo get_class($user);  // User ✅
```

---

## 12.2 get_called_class()

```php
<?php
abstract class Singleton
{
    private static array $instances = [];

    protected function __construct() {}

    public static function getInstance(): static
    {
        $class = get_called_class();
        
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }
        
        return self::$instances[$class];
    }

    protected function __clone() {}
    public function __wakeup(): void
    {
        throw new \Exception('Cannot unserialize singleton');
    }
}

class DatabaseConnection extends Singleton
{
    private ?PDO $pdo = null;

    public function connect(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = new PDO('mysql:host=localhost;dbname=app', 'root', '');
        }
        return $this->pdo;
    }
}

class Logger extends Singleton
{
    public function log(string $message): void
    {
        $called = get_called_class();
        echo "[{$called}] {$message}\n";
    }
}

$db1 = DatabaseConnection::getInstance();
$db2 = DatabaseConnection::getInstance();
var_dump($db1 === $db2); // true

Logger::getInstance()->log('Application started');
// [Logger] Application started
```

---

## 12.3 Exercises

1. Implement an ActiveRecord-style base model using late static binding
2. Create a factory that returns instances of the called class
3. Build a singleton registry using `get_called_class()`
4. Implement polymorphic collections that return `static` types

---

## Further Reading

- **Doc:** [Late Static Binding](https://www.php.net/manual/en/language.oop5.late-static-bindings.php)
