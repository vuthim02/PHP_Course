# Chapter 11: Advanced Functions

## Learning Objectives

- Use variable functions and call_user_func
- Master arrow functions syntax
- Implement function composition
- Understand first-class callables

---

## 11.1 Variable Functions

```php
<?php
class MathOperations
{
    public static function add(int $a, int $b): int
    {
        return $a + $b;
    }

    public function subtract(int $a, int $b): int
    {
        return $a - $b;
    }
}

// Variable functions
$operation = 'strtoupper';
echo $operation('hello'); // HELLO

// Variable method calls
$class = 'MathOperations';
$method = 'add';

// Call static method
echo $class::$method(5, 3); // 8

// Call instance method
$obj = new $class();
echo $obj->subtract(10, 4); // 6

// call_user_func / call_user_func_array
echo call_user_func('strtoupper', 'hello');
echo call_user_func_array([$class, 'add'], [5, 3]);
echo call_user_func_array([$obj, 'subtract'], [10, 4]);

// First-class callable (PHP 8.1+)
$toUpper = strtoupper(...);
echo $toUpper('hello'); // HELLO

$add = MathOperations::add(...);
echo $add(5, 3); // 8
```

---

## 11.2 Arrow Functions

```php
<?php
// Arrow functions (fn) — automatically capture variables by value
$factor = 2;
$multiply = fn(int $x): int => $x * $factor;

// Compare with closures
$closure = function (int $x) use ($factor): int {
    return $x * $factor;
};

// Arrow functions in array operations
$prices = [10, 20, 30, 40, 50];

$withTax = array_map(fn($price) => $price * 1.2, $prices);
$discounted = array_map(fn($price) => $price * 0.9, $prices);

// Nested arrow functions
$users = [
    ['name' => 'Alice', 'age' => 30],
    ['name' => 'Bob', 'age' => 25],
    ['name' => 'Charlie', 'age' => 35],
];

$adultNames = array_map(
    fn(array $user) => $user['name'],
    array_filter(
        $users,
        fn(array $user) => $user['age'] >= 18
    )
);
```

---

## 11.3 Exercises

1. Implement a simple plugin system using variable functions
2. Create a function composition helper that chains transformations
3. Use arrow functions to build a data processing pipeline
4. Implement a middleware pipeline using call_user_func

---

## Further Reading

- **Doc:** [PHP Callables](https://www.php.net/manual/en/language.types.callable.php)
- **Doc:** [Arrow Functions](https://www.php.net/manual/en/functions.arrow.php)
