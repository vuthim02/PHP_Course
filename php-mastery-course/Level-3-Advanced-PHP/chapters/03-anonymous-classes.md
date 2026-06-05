# Chapter 3: Anonymous Classes

## Learning Objectives

- Create inline class definitions
- Understand use cases for anonymous classes
- Implement mock objects and simple implementations

---

## 3.1 Anonymous Class Syntax

```php
<?php
interface Logger
{
    public function log(string $message): void;
}

// Anonymous class implementing an interface
$logger = new class implements Logger {
    public function log(string $message): void
    {
        echo "[LOG]: {$message}\n";
    }
};

// Anonymous class extending another class
$request = new class('GET', '/api/users') extends HttpRequest {
    public function __construct(string $method, string $uri)
    {
        parent::__construct($method, $uri);
        $this->setHeader('X-Custom', 'value');
    }
};

// With constructor arguments
$cache = new class('/tmp/cache', 3600) extends FileCache {
    public function __construct(string $path, int $ttl)
    {
        parent::__construct($path);
        $this->ttl = $ttl;
    }
};
```

---

## 3.2 Real-World Usage

```php
<?php
// Test doubles / mocks
class UserService
{
    public function __construct(private UserRepository $repository) {}

    public function register(array $data): User
    {
        if ($this->repository->findByEmail($data['email'])) {
            throw new UserAlreadyExistsException();
        }
        return $this->repository->create($data);
    }
}

// In tests, create anonymous mock
$repository = new class implements UserRepository {
    private array $users = [];

    public function findByEmail(string $email): ?User
    {
        return $this->users[$email] ?? null;
    }

    public function create(array $data): User
    {
        $user = new User(count($this->users) + 1, $data);
        $this->users[$data['email']] = $user;
        return $user;
    }
};

// One-off implementations
$paymentGateway = new class implements PaymentGateway {
    public function charge(int $amount, array $details): PaymentResult
    {
        // Simple implementation for testing
        if ($amount <= 0) {
            throw new PaymentException('Invalid amount');
        }
        return new PaymentResult(true, 'txn_' . uniqid());
    }

    public function refund(string $transactionId): PaymentResult
    {
        return new PaymentResult(true, $transactionId);
    }
};
```

---

## 3.3 Exercises

1. Create an anonymous class that implements `CacheInterface` using an in-memory array
2. Use an anonymous class as a strategy in the Strategy pattern
3. Create an anonymous event listener that logs events to a file
4. Implement a simple anonymous router

---

## Further Reading

- **Doc:** [PHP Anonymous Classes](https://www.php.net/manual/en/language.oop5.anonymous.php)
