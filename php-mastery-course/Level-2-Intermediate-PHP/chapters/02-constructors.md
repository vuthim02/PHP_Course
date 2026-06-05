# Chapter 2: Constructors and Destructors

## Learning Objectives

- Use constructors for object initialization
- Use PHP 8 constructor property promotion
- Understand destructors
- Implement dependency injection via constructors

---

## 2.1 Constructor Basics

```php
<?php
class User
{
    public string $name;
    public string $email;
    public DateTimeImmutable $createdAt;
    
    // Constructor: called when object is created with 'new'
    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
        $this->createdAt = new DateTimeImmutable();
    }
}

$user = new User('Alice', 'alice@example.com');
echo $user->name;  // 'Alice'
```

---

## 2.2 Constructor Property Promotion (PHP 8+)

```php
<?php
// Old way (boilerplate)
class UserOld
{
    public string $name;
    public string $email;
    
    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }
}

// PHP 8+ way (concise)
class User
{
    public function __construct(
        public string $name,
        public string $email,
        private string $password,
        public readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {}
}

// Usage:
$user = new User(
    name: 'Alice',
    email: 'alice@example.com',
    password: password_hash('secret123', PASSWORD_BCRYPT),
);
```

---

## 2.3 Destructor

```php
<?php
class DatabaseConnection
{
    private ?PDO $connection = null;
    
    public function __construct(
        private string $dsn,
        private string $user,
        private string $pass
    ) {
        $this->connect();
    }
    
    public function connect(): void
    {
        $this->connection = new PDO($this->dsn, $this->user, $this->pass);
    }
    
    public function getConnection(): PDO
    {
        return $this->connection;
    }
    
    // Destructor: called when object is destroyed
    public function __destruct()
    {
        $this->connection = null;  // Close connection
    }
}
```

---

## 2.4 Exercises

1. Create a class with constructor property promotion
2. Implement a destructor that logs when an object is destroyed
3. Build a Logger class that opens a file handle in constructor and closes in destructor
4. Create a Config class that loads config from a file in constructor

---

## Further Reading

- **Doc:** [Constructors](https://www.php.net/manual/en/language.oop5.decon.php)
- **RFC:** [Constructor Property Promotion](https://wiki.php.net/rfc/constructor_promotion)
