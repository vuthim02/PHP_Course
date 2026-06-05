# Chapter 5: Polymorphism

## Learning Objectives

- Understand polymorphism in OOP
- Implement polymorphic behavior with interfaces
- Use abstract classes for polymorphism
- Apply the Strategy pattern

---

## 5.1 What is Polymorphism?

Polymorphism means "many forms" — the ability of different classes to respond to the same method call in their own way.

```php
<?php
// Polymorphism via interfaces
interface PaymentMethod
{
    public function process(float $amount): string;
}

class CreditCardPayment implements PaymentMethod
{
    public function process(float $amount): string
    {
        return "Processing credit card payment of \${$amount}";
    }
}

class PayPalPayment implements PaymentMethod
{
    public function process(float $amount): string
    {
        return "Processing PayPal payment of \${$amount}";
    }
}

class CryptoPayment implements PaymentMethod
{
    public function process(float $amount): string
    {
        return "Processing crypto payment of \${$amount}";
    }
}

// Client code works with ANY PaymentMethod
class CheckoutService
{
    public function processOrder(Order $order, PaymentMethod $payment): string
    {
        return $payment->process($order->total);
    }
}

// Usage — same method, different behavior
$checkout = new CheckoutService();
$order = new Order(total: 99.99);

echo $checkout->processOrder($order, new CreditCardPayment());
echo $checkout->processOrder($order, new PayPalPayment());
echo $checkout->processOrder($order, new CryptoPayment());
```

---

## 5.2 Polymorphism via Abstract Classes

```php
<?php
abstract class Animal
{
    abstract public function makeSound(): string;
}

class Dog extends Animal
{
    public function makeSound(): string
    {
        return 'Woof!';
    }
}

class Cat extends Animal
{
    public function makeSound(): string
    {
        return 'Meow!';
    }
}

class Duck extends Animal
{
    public function makeSound(): string
    {
        return 'Quack!';
    }
}

function playSound(Animal $animal): void
{
    echo $animal->makeSound();
}

playSound(new Dog());   // 'Woof!'
playSound(new Cat());   // 'Meow!'
playSound(new Duck());  // 'Quack!'
```

---

## 5.3 Strategy Pattern

```php
<?php
interface SortStrategy
{
    public function sort(array $data): array;
}

class BubbleSortStrategy implements SortStrategy
{
    public function sort(array $data): array
    {
        // Bubble sort implementation
        sort($data);
        return $data;
    }
}

class QuickSortStrategy implements SortStrategy
{
    public function sort(array $data): array
    {
        // Quick sort implementation
        sort($data);
        return $data;
    }
}

class Sorter
{
    public function __construct(
        private SortStrategy $strategy
    ) {}
    
    public function sort(array $data): array
    {
        return $this->strategy->sort($data);
    }
    
    public function setStrategy(SortStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }
}

// Usage
$sorter = new Sorter(new BubbleSortStrategy());
$result = $sorter->sort([3, 1, 4, 1, 5]);

$sorter->setStrategy(new QuickSortStrategy());
$result = $sorter->sort([3, 1, 4, 1, 5]);
```

---

## 5.4 Exercises

1. Create an interface `NotificationChannel` with a `send(string $message)` method
2. Implement EmailNotification, SMSNotification, and PushNotification
3. Create a NotificationService that works with any channel
4. Add a new SlackNotification channel without changing existing code

---

## Further Reading

- **Doc:** [Polymorphism](https://www.php.net/manual/en/language.oop5.php)
- **Book:** "Head First Design Patterns" — Strategy Pattern
