# Chapter 13: Type System Deep Dive

## Learning Objectives

- Understand covariance and contravariance
- Use intersection and union types
- Master PHP 8 type system features
- Apply generic-like patterns

---

## 13.1 Covariance and Contravariance

```php
<?php
// Covariance — return types can be more specific in child classes
abstract class Animal {}
class Dog extends Animal {}
class Cat extends Animal {}

interface AnimalShelter
{
    public function adopt(): Animal;  // Returns any Animal
}

class DogShelter implements AnimalShelter
{
    public function adopt(): Dog  // Returns only Dog (more specific) ✅
    {
        return new Dog();
    }
}

class CatShelter implements AnimalShelter
{
    public function adopt(): Cat  // Returns only Cat (more specific) ✅
    {
        return new Cat();
    }
}

// Contravariance — parameter types can be less specific in child classes
interface AnimalFeeder
{
    public function feed(Dog $dog): void;  // Accepts Dog
}

class AnimalFeederImpl implements AnimalFeeder
{
    public function feed(Animal $animal): void  // Accepts any Animal (less specific) ✅
    {
        echo "Feeding {$animal::class}\n";
    }
}
```

---

## 13.2 Intersection and Union Types

```php
<?php
// Union types — "type A OR type B"
class Result
{
    public function __construct(
        private string|int|float|null $value
    ) {}

    public function getValue(): string|int|float|null
    {
        return $this->value;
    }

    public function isNumeric(): bool
    {
        return is_int($this->value) || is_float($this->value);
    }
}

// Intersection types — "type A AND type B"
interface Loggable
{
    public function getLogData(): array;
}

interface Serializable
{
    public function serialize(): string;
}

class AuditLogger
{
    public function log(Loggable&Serializable $entity): void
    {
        // $entity MUST implement BOTH interfaces
        $data = $entity->getLogData();
        $serialized = $entity->serialize();
        file_put_contents('audit.log', $serialized . "\n", FILE_APPEND);
    }
}

class User implements Loggable, Serializable
{
    public function getLogData(): array
    {
        return ['action' => 'user_created', 'time' => time()];
    }

    public function serialize(): string
    {
        return json_encode($this->getLogData());
    }
}

$audit = new AuditLogger();
$audit->log(new User()); // ✅ Implements both
```

---

## 13.3 Exercises

1. Create a collection class that uses covariance for return types
2. Implement a logger that accepts union types for messages
3. Use intersection types to require an entity to be both `Storable` and `Cacheable`
4. Build a validation system using union types for field values

---

## Further Reading

- **Doc:** [PHP Type System](https://www.php.net/manual/en/language.types.type-system.php)
- **Doc:** [Covariance and Contravariance](https://www.php.net/manual/en/language.oop5.variance.php)
