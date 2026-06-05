# Chapter 8: Functions

Think of a function like a blender. You put ingredients in (inputs/parameters), press the button (call the function), and get a smoothie out (the return value). You don't need to know how the blades spin — you just need to know what goes in and what comes out. Functions let you write a recipe once and reuse it everywhere.

## Learning Objectives

- Define and call functions
- Understand parameter passing (by value vs by reference)
- Use type declarations and return types
- Master variable scope and static variables
- Use arrow functions and anonymous functions

---

## 8.1 Function Basics

```php
<?php
// Basic function
function greet(): void {
    echo "Hello, World!\n";
}

// Function with parameters
function greetUser(string $name): void {
    echo "Hello, {$name}!\n";
}

// Function with return value
function add(int $a, int $b): int {
    return $a + $b;
}

// Function with default parameters
function createUser(string $name, string $role = 'user'): array {
    return [
        'name' => $name,
        'role' => $role,
        'created_at' => date('Y-m-d H:i:s'),
    ];
}

// Named arguments (PHP 8+)
createUser(role: 'admin', name: 'Alice');  // Order doesn't matter

// Variadic parameters
function sum(int ...$numbers): int {
    return array_sum($numbers);
}
echo sum(1, 2, 3, 4, 5);  // 15
```

---

## 8.2 Parameter Passing

```php
<?php
// By value (default) - original is NOT modified
function addExclamation(string $str): string {
    $str .= '!';
    return $str;
}
$text = 'Hello';
addExclamation($text);
echo $text;  // 'Hello' (unchanged)

// By reference (&) - original IS modified
function addExclamationRef(string &$str): void {
    $str .= '!';
}
$text = 'Hello';
addExclamationRef($text);
echo $text;  // 'Hello!' (modified!)
```

---

## 8.3 Variable Scope

```php
<?php
$globalVar = 'I am global';

function testScope(): void {
    $localVar = 'I am local';
    
    // Cannot access $globalVar directly
    // echo $globalVar;  // Warning!
    
    // Use global keyword
    global $globalVar;
    echo $globalVar;  // Works
    
    // Or use $GLOBALS
    echo $GLOBALS['globalVar'];
    
    // Static variable (persists across calls)
    static $counter = 0;
    $counter++;
    echo $counter;  // 1, 2, 3 on subsequent calls
}
```

---

## 8.4 Type Declarations

```php
<?php
declare(strict_types=1);

// Return type
function getUser(int $id): ?array {
    // Returns array or null
}

// Union types (PHP 8+)
function formatValue(int|float $value): int|float {
    return round($value);
}

// Mixed type
function dump(mixed $value): void {
    var_dump($value);
}

// Void (no return)
function log(string $message): void {
    // Function doesn't return anything
}

// Never (PHP 8.1+) - function never returns
function abort(string $message): never {
    throw new RuntimeException($message);
}

// True/False types (PHP 8.2+)
function isAdmin(): bool {
    return true;
}
```

---

## 8.5 Arrow Functions (PHP 7.4+)

```php
<?php
// Traditional anonymous function
$multiply = function (int $a, int $b): int {
    return $a * $b;
};

// Arrow function (shorter, inherits parent scope)
$multiply = fn(int $a, int $b): int => $a * $b;

// Arrow functions capture variables by value automatically
$factor = 2;
$numbers = [1, 2, 3, 4, 5];
$doubled = array_map(fn($n) => $n * $factor, $numbers);
// No need for "use ($factor)"
```

---

## 8.6 Exercises

1. Write a function that validates an email address
2. Create a variadic sum function
3. Use a recursive function to calculate factorial
4. Demonstrate static variable usage
5. Compare anonymous functions vs arrow functions
6. Write a function with named arguments

---

## Further Reading

- **Doc:** [PHP Functions](https://www.php.net/manual/en/language.functions.php)
- **Doc:** [Arrow Functions](https://www.php.net/manual/en/functions.arrow.php)
- **RFC:** [Named Arguments](https://wiki.php.net/rfc/named_params)
