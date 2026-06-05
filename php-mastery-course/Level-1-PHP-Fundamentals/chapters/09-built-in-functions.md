# Chapter 9: Built-in Functions

## Learning Objectives

- Master PHP's most important built-in functions
- Use string, array, math, date, and file functions
- Understand JSON encoding/decoding
- Apply callback functions (array_map, array_filter, array_reduce)

---

## 9.1 String Functions

```php
<?php
$text = "Hello, World!";

echo strlen($text);           // 13 (length)
echo strpos($text, "World");  // 7 (position, 0-indexed)
echo strtolower($text);       // "hello, world!"
echo strtoupper($text);       // "HELLO, WORLD!"
echo substr($text, 0, 5);     // "Hello" (extract)
echo str_replace("World", "PHP", $text);  // "Hello, PHP!"
echo trim("  Hello  ");       // "Hello" (strip whitespace)
echo explode(", ", $text);    // ["Hello", "World!"] (split)
echo implode("-", ["a","b"]); // "a-b" (join)
echo htmlspecialchars("<tag>"); // "&lt;tag&gt;" (escape HTML)
echo nl2br("Line1\nLine2");   // "Line1<br />Line2"
print_r(str_split("Hello"));  // ["H","e","l","l","o"]
```

---

## 9.2 Array Functions

```php
<?php
$numbers = [3, 1, 4, 1, 5, 9, 2, 6];

echo count($numbers);           // 8 (number of elements)
sort($numbers);                 // Sort in place
echo in_array(5, $numbers);     // true (check exists)
echo array_search(5, $numbers); // index of 5

array_push($numbers, 10);       // Add to end
array_pop($numbers);            // Remove from end
array_unshift($numbers, 0);     // Add to beginning
array_shift($numbers);          // Remove from beginning

$result = array_map(fn($n) => $n * 2, $numbers);        // Transform
$result = array_filter($numbers, fn($n) => $n > 3);     // Filter
$result = array_reduce($numbers, fn($c, $n) => $c + $n); // Reduce

$users = array_combine(['id', 'name'], [1, 'Alice']);   // Keys + values
$keys = array_keys($users);
$values = array_values($users);
$merged = array_merge([1, 2], [3, 4]);  // [1, 2, 3, 4]
$diff = array_diff([1, 2, 3], [2, 4]);  // [1, 3]
$intersect = array_intersect([1, 2, 3], [2, 4]);  // [2]
```

---

## 9.3 JSON Functions

```php
<?php
$data = ['name' => 'Alice', 'age' => 30, 'skills' => ['PHP', 'JavaScript']];

// Encode PHP array → JSON string
$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
// {"name":"Alice","age":30,"skills":["PHP","JavaScript"]}

// Decode JSON string → PHP array
$decoded = json_decode($json, true);  // true = associative array
echo json_last_error_msg();           // Check for errors

// Validate JSON (PHP 8.3+)
var_dump(json_validate('{"valid": true}'));  // true
```

---

## 9.4 Math Functions

```php
<?php
echo abs(-5);           // 5
echo round(3.14159, 2); // 3.14
echo ceil(3.1);         // 4
echo floor(3.9);        // 3
echo max(1, 5, 3);      // 5
echo min(1, 5, 3);      // 1
echo rand(1, 10);       // Random between 1-10
echo mt_rand(1, 10);    // Better random (Mersenne Twister)
echo sqrt(16);          // 4
echo pow(2, 10);        // 1024 (2^10)
echo pi();              // 3.1415926535898
```

---

## 9.5 Date/Time Functions

```php
<?php
echo time();                          // Unix timestamp (seconds since 1970)
echo date('Y-m-d H:i:s');             // "2024-01-15 10:30:00"
echo date('l, F j, Y');              // "Monday, January 15, 2024"
echo strtotime('next Monday');        // Timestamp for next Monday
echo strtotime('+2 weeks');          // Timestamp in 2 weeks

$datetime = new DateTime('2024-01-15');
echo $datetime->format('Y-m-d');
$datetime->modify('+1 month');
```

---

## 9.6 Exercises

1. Use array_map to transform an array of user IDs to user objects
2. Filter an array of orders to only show paid orders
3. Use array_reduce to calculate the total price of items in a cart
4. Validate and decode a JSON string
5. Format a date in multiple timezones

---

## Further Reading

- **Doc:** [PHP Function Reference](https://www.php.net/manual/en/funcref.php)
- **Doc:** [Array Functions](https://www.php.net/manual/en/ref.array.php)
- **Doc:** [String Functions](https://www.php.net/manual/en/ref.strings.php)
