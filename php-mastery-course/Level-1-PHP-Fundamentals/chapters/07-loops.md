# Chapter 7: Loops

Think of a loop like a washing machine cycle. The machine repeats the same steps (fill, wash, rinse, spin) over and over until the cycle is done. A PHP loop does the same: it runs the same block of code repeatedly until a condition says "stop."

## Learning Objectives

- Use for, while, do-while, and foreach loops
- Control loop execution with break and continue
- Loop through arrays effectively
- Understand performance implications of nested loops

---

## 7.1 Loop Types

```php
<?php
// for loop (when you know the number of iterations)
for ($i = 0; $i < 10; $i++) {
    echo $i;
}

// while loop (when condition determines continuation)
$i = 0;
while ($i < 10) {
    echo $i;
    $i++;
}

// do-while (runs at least once)
$i = 0;
do {
    echo $i;
    $i++;
} while ($i < 10);

// foreach (for arrays and objects)
$users = ['Alice', 'Bob', 'Charlie'];
foreach ($users as $index => $name) {
    echo "{$index}: {$name}\n";
}
```

---

## 7.2 foreach in Depth

```php
<?php
// Indexed array
$colors = ['red', 'green', 'blue'];
foreach ($colors as $color) {
    echo $color;
}

// With key
foreach ($colors as $index => $color) {
    echo "{$index}: {$color}";
}

// Associative array
$user = [
    'name' => 'Alice',
    'email' => 'alice@example.com',
    'role' => 'admin',
];
foreach ($user as $key => $value) {
    echo "{$key}: {$value}\n";
}

// Modify while iterating (use reference)
$numbers = [1, 2, 3, 4, 5];
foreach ($numbers as &$number) {
    $number *= 2;
}
unset($number);  // Break the reference!

// Iterate over object properties
class User {
    public string $name = 'Alice';
    public string $email = 'alice@example.com';
    protected string $password = 'secret';  // Not accessible
}
$user = new User();
foreach ($user as $prop => $value) {
    echo "{$prop}: {$value}\n";  // Only public properties
}
```

---

## 7.3 break and continue

```php
<?php
// break - exit loop
$users = ['Alice', 'Bob', 'Charlie', 'Dave'];
foreach ($users as $user) {
    if ($user === 'Charlie') {
        break;  // Stops at Charlie
    }
    echo $user;  // Prints: AliceBob
}

// continue - skip to next iteration
foreach ($users as $user) {
    if ($user === 'Bob') {
        continue;  // Skip Bob
    }
    echo $user;  // Prints: AliceCharlieDave
}

// break with depth (break out of nested loops)
for ($i = 0; $i < 10; $i++) {
    for ($j = 0; $j < 10; $j++) {
        if ($i === 3 && $j === 5) {
            break 2;  // Break out of both loops
        }
    }
}
```

---

## 7.4 Performance Considerations

```php
<?php
// BAD: Counting in condition (recalculated each iteration)
for ($i = 0; $i < count($items); $i++) { ... }

// GOOD: Count once
$count = count($items);
for ($i = 0; $i < $count; $i++) { ... }

// BAD: Nested loops over large arrays
foreach ($users as $user) {
    foreach ($orders as $order) {
        if ($order['user_id'] === $user['id']) { ... }
    }
}

// GOOD: Build lookup table first
$userOrders = [];
foreach ($orders as $order) {
    $userOrders[$order['user_id']][] = $order;
}
foreach ($users as $user) {
    $userOrderList = $userOrders[$user['id']] ?? [];
}
```

---

## 7.5 Exercises

1. Print numbers 1-100 using a for loop
2. Loop through an associative array and display key-value pairs
3. Find the first element in an array that matches a condition
4. Skip every third iteration in a loop using continue
5. Build a lookup table from an array of objects
6. Write a nested loop that breaks out of both loops when a condition is met

---

## Further Reading

- **Doc:** [PHP Control Structures: Loops](https://www.php.net/manual/en/language.control-structures.php)
- **Article:** [PHP foreach performance tips](https://www.php.net/manual/en/control-structures.foreach.php)
