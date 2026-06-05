# Chapter 4: Traits and Interfaces

## Learning Objectives

- Differentiate between traits and interfaces
- Implement multiple interfaces
- Use traits for code reuse
- Understand trait resolution order

---

## 4.1 Interfaces

```php
<?php
interface CanFly
{
    public function fly(): string;
}

interface CanSwim
{
    public function swim(): string;
}

interface CanWalk
{
    public function walk(): string;
}

class Duck implements CanSwim, CanWalk, CanFly
{
    public function swim(): string
    {
        return "Duck swims on water";
    }

    public function walk(): string
    {
        return "Duck waddles";
    }

    public function fly(): string
    {
        return "Duck flies south";
    }
}

class Penguin implements CanSwim, CanWalk
{
    public function swim(): string
    {
        return "Penguin swims gracefully";
    }

    public function walk(): string
    {
        return "Penguin waddles slowly";
    }
}
```

---

## 4.2 Traits

```php
<?php
trait Timestampable
{
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function initializeTimestamps(): void
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}

trait SoftDeletable
{
    private ?DateTimeImmutable $deletedAt = null;

    public function delete(): void
    {
        $this->deletedAt = new DateTimeImmutable();
    }

    public function restore(): void
    {
        $this->deletedAt = null;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}

trait Loggable
{
    private array $log = [];

    public function log(string $message): void
    {
        $this->log[] = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    }

    public function getLog(): array
    {
        return $this->log;
    }
}

class User
{
    use Timestampable, SoftDeletable, Loggable;

    public function __construct(
        private string $name,
        private string $email
    ) {
        $this->initializeTimestamps();
        $this->log("User {$this->name} created");
    }
}
```

---

## 4.3 Trait Resolution

```php
<?php
trait A
{
    public function sayHello(): string
    {
        return 'Hello from A';
    }
}

trait B
{
    public function sayHello(): string
    {
        return 'Hello from B';
    }
}

class MyClass
{
    use A, B {
        B::sayHello insteadof A;  // Use B's version
        A::sayHello as sayHelloFromA; // Alias A's version
    }
}

$obj = new MyClass();
echo $obj->sayHello();      // Hello from B
echo $obj->sayHelloFromA(); // Hello from A
```

---

## 4.4 Exercises

1. Create `PaymentInterface`, `RefundableInterface`, and `VerifiableInterface`
2. Implement all three in a `CreditCardPayment` class
3. Create `Notifiable` trait with `sendEmail()`, `sendSMS()` methods
4. Use `Notifiable` in both `User` and `Order` classes
5. Resolve a method conflict between two traits using `insteadof`

---

## Further Reading

- **Doc:** [PHP Traits](https://www.php.net/manual/en/language.oop5.traits.php)
- **Doc:** [PHP Interfaces](https://www.php.net/manual/en/language.oop5.interfaces.php)
