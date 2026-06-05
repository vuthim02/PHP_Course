# Chapter 5: Functional Programming

## Learning Objectives

- Apply functional programming techniques in PHP
- Master array functions (map, filter, reduce)
- Implement currying and partial application
- Write pure functions

---

## 5.1 Pure Functions

```php
<?php
// ❌ Impure — modifies external state
$taxRate = 0.2;
function calculatePriceImpure(float $price): float
{
    global $taxRate;
    return $price + ($price * $taxRate);
}

// ✅ Pure — no side effects, same input always produces same output
function calculatePrice(float $price, float $taxRate): float
{
    return $price + ($price * $taxRate);
}

// ❌ Impure — modifies argument
function addTaxImpure(array &$items, float $rate): void
{
    foreach ($items as &$item) {
        $item['total'] = $item['price'] * (1 + $rate);
    }
}

// ✅ Pure — returns new array
function addTax(array $items, float $rate): array
{
    return array_map(
        fn(array $item) => array_merge($item, [
            'total' => $item['price'] * (1 + $rate),
        ]),
        $items
    );
}
```

---

## 5.2 Array Functions

```php
<?php
$products = [
    ['name' => 'Laptop', 'price' => 1200, 'category' => 'electronics'],
    ['name' => 'Shirt', 'price' => 25, 'category' => 'clothing'],
    ['name' => 'Phone', 'price' => 800, 'category' => 'electronics'],
    ['name' => 'Shoes', 'price' => 80, 'category' => 'clothing'],
    ['name' => 'Tablet', 'price' => 300, 'category' => 'electronics'],
];

// array_map — transform each element
$names = array_map(fn($p) => $p['name'], $products);

// array_filter — filter elements
$electronics = array_filter(
    $products,
    fn($p) => $p['category'] === 'electronics'
);

// array_reduce — reduce to single value
$totalPrice = array_reduce(
    $products,
    fn($carry, $p) => $carry + $p['price'],
    0
);

// Chaining functional operations
$result = array_reduce(
    array_map(
        fn($p) => $p['price'] * 1.1,  // Add 10% tax
        array_filter(
            $products,
            fn($p) => $p['price'] > 100
        )
    ),
    fn($carry, $price) => $carry + $price,
    0
);

// Pipeline helper
function pipeline(mixed $value, callable ...$stages): mixed
{
    return array_reduce(
        $stages,
        fn($carry, $stage) => $stage($carry),
        $value
    );
}

$expensiveTotal = pipeline(
    $products,
    fn(array $items) => array_filter($items, fn($p) => $p['price'] > 100),
    fn(array $items) => array_map(fn($p) => $p['price'] * 1.1, $items),
    fn(array $prices) => array_sum($prices),
);
```

---

## 5.3 Currying

```php
<?php
// Manual currying
function add(int $a): callable
{
    return function (int $b) use ($a): callable {
        return function (int $c) use ($a, $b): int {
            return $a + $b + $c;
        };
    };
}

echo add(1)(2)(3); // 6

// Currying helper
function curry(callable $fn, mixed ...$args): callable
{
    return function (...$more) use ($fn, $args) {
        $all = array_merge($args, $more);
        $required = (new ReflectionFunction($fn))->getNumberOfRequiredParameters();
        
        if (count($all) >= $required) {
            return $fn(...$all);
        }
        
        return curry($fn, ...$all);
    };
}

$multiply = fn(float $a, float $b, float $c): float => $a * $b * $c;
$curried = curry($multiply);

$double = $curried(2);
$doubleBy5 = $double(5);
echo $doubleBy5(3); // 30 (2 * 5 * 3)
```

---

## 5.4 Exercises

1. Transform an array of orders using map/filter/reduce to calculate total revenue
2. Create a pipeline that filters active users, formats names, and sorts by date
3. Implement a curry function for any callable
4. Write a compose function that chains functions right-to-left

---

## Further Reading

- **Doc:** [PHP Array Functions](https://www.php.net/manual/en/ref.array.php)
- **Book:** "Functional Programming in PHP" by Simon Holywell
