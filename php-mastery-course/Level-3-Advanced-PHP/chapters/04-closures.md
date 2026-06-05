# Chapter 4: Closures and Callables

## Learning Objectives

- Create and use closures (anonymous functions)
- Understand closure binding and `$this` scope
- Use `use()` for variable capture
- Implement callbacks effectively

---

## 4.1 Closures

```php
<?php
// Basic closure
$greet = function (string $name): string {
    return "Hello, {$name}!";
};

echo $greet('Alice'); // Hello, Alice!

// Closure with use() — captures variables from parent scope
$prefix = 'User: ';
$formatUser = function (string $name, string $email) use ($prefix): string {
    return $prefix . "{$name} <{$email}>";
};

echo $formatUser('John', 'john@example.com');

// Modifying captured variables (by reference)
$counter = 0;
$increment = function () use (&$counter): int {
    return ++$counter;
};
$increment(); // 1
$increment(); // 2
```

---

## 4.2 Closure Binding

```php
<?php
class Calculator
{
    private int $multiplier = 2;

    public function createMultiplier(): Closure
    {
        // Automatically binds $this
        return function (int $value): int {
            return $value * $this->multiplier;
        };
    }
}

$calc = new Calculator();
$double = $calc->createMultiplier();
echo $double(5); // 10

// Bind a closure to a different object
class Context
{
    private string $message = 'Hello from Context';
}

$closure = function (): string {
    return $this->message;
};

$context = new Context();
$bound = $closure->bindTo($context, Context::class);
echo $bound(); // Hello from Context

// bindTo shorthand
$bound = Closure::bind($closure, $context, Context::class);
```

---

## 4.3 Callables

```php
<?php
// Different types of callables
function globalFunction(string $value): string {
    return strtoupper($value);
}

class Formatter
{
    public static function uppercase(string $value): string
    {
        return strtoupper($value);
    }

    public function lowercase(string $value): string
    {
        return strtolower($value);
    }
}

// All of these are valid callables:
$callables = [
    'globalFunction',                          // String function name
    [Formatter::class, 'uppercase'],           // Static method
    [new Formatter(), 'lowercase'],            // Instance method
    fn(string $s) => ucfirst($s),              // Arrow function
    function (string $s): string {             // Closure
        return trim($s);
    },
];

$data = ['  Hello  ', '  WORLD  ', '  PHP  '];

foreach ($callables as $callback) {
    $result = array_map($callback, $data);
    print_r($result);
}

// Using callable type hint
function processData(array $data, callable $transformer): array
{
    return array_map($transformer, $data);
}
```

---

## 4.4 Exercises

1. Create a closure that filters an array of products by a price range
2. Implement a memoization helper using closures and static variables
3. Build a pipeline of callbacks that process a string (trim → lowercase → capitalize)
4. Use `bindTo()` to attach a closure as a method of an object

---

## Further Reading

- **Doc:** [PHP Anonymous Functions](https://www.php.net/manual/en/functions.anonymous.php)
- **Doc:** [Closure Class](https://www.php.net/manual/en/class.closure.php)
