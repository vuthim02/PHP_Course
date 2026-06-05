# PHP Fundamentals — Quick Reference

## Syntax Basics

```php
<?php
// Opening tag — all PHP code goes inside <?php ... ?>

// Echo / print — output to browser
echo "Hello, World!";
print "Hello!";
?>
```

## Variables & Types

```php
// Variables start with $
$name = "Alice";
$age = 30;
$price = 19.99;
$isActive = true;
$items = [1, 2, 3];
$nothing = null;

// Variable variables
$varName = "foo";
$$varName = "bar"; // $foo = "bar"

// Checking types
gettype($var);     // "string", "integer", "boolean"...
var_dump($var);    // Type + value
is_string($var);
is_int($var);
is_array($var);
```

## Strings

```php
// Double quotes — supports interpolation
$greeting = "Hello, $name!";  // "Hello, Alice!"

// Single quotes — literal
$greeting = 'Hello, $name!';  // "Hello, $name!"

// Heredoc
$html = <<<HTML
<div>
    <h1>$title</h1>
</div>
HTML;

// Functions
strlen($str);            // Length
strpos($str, "needle");  // Find position (false if not found)
substr($str, 0, 5);      // First 5 characters
str_replace("old", "new", $str);
explode(",", $str);      // String → array
implode(",", $arr);      // Array → string
trim($str);              // Strip whitespace
strtolower / strtoupper;
json_encode / json_decode;
```

## Arrays

```php
// Indexed arrays
$colors = ["red", "green", "blue"];
$colors[] = "yellow";        // Append

// Associative arrays
$user = [
    "name" => "Alice",
    "age" => 30,
];
$user["email"] = "alice@example.com";

// Common operations
count($arr);
array_push($arr, $val);
array_pop($arr);
array_shift($arr);
array_unshift($arr, $val);
in_array($val, $arr);
array_key_exists("key", $arr);
isset($arr["key"]);
unset($arr["key"]);
array_keys($arr);
array_values($arr);
sort / rsort / asort / ksort;
array_map(fn($x) => $x * 2, $arr);
array_filter($arr, fn($x) => $x > 10);
array_merge($arr1, $arr2);
```

## Operators

```php
// Arithmetic
+ - * / % **         // Addition, subtraction, multiplication, division, modulo, exponent

// Comparison
==  ===  !=  !==  <  >  <=  >=
==  // Loose (1 == "1" → true)
=== // Strict (1 === "1" → false)

// Logical
&&  ||  !  and  or

// Null coalescing
$result = $var ?? "default";     // ?? checks isset()
$result = $var ?: "default";     // ?: checks truthy

// Spaceship
strcmp = $a <=> $b;   // -1, 0, 1

// Error suppression
@functionThatMightWarn();
```

## Control Flow

```php
// if / elseif / else
if ($score >= 90) {
    echo "A";
} elseif ($score >= 80) {
    echo "B";
} else {
    echo "F";
}

// Switch
switch ($day) {
    case 1: echo "Monday"; break;
    case 2: echo "Tuesday"; break;
    default: echo "Unknown";
}

// Match (PHP 8+)
$grade = match ($score) {
    90...100 => "A",
    80...89  => "B",
    70...79  => "C",
    default  => "F",
};

// Ternary
$status = $age >= 18 ? "Adult" : "Minor";
```

## Loops

```php
// for
for ($i = 0; $i < 10; $i++) { ... }

// foreach
foreach ($items as $item) { ... }
foreach ($items as $key => $value) { ... }

// while / do-while
while ($row = $result->fetch()) { ... }
do { ... } while ($cond);

// break / continue
break;      // Exit loop
continue;   // Skip to next iteration
break 2;    // Exit 2 levels of loops
```

## Functions

```php
// Basic function
function greet(string $name): string {
    return "Hello, $name!";
}

// Default parameters
function greet(string $name = "World"): string {
    return "Hello, $name!";
}

// Named arguments (PHP 8+)
greet(name: "Alice");

// Variadic
function sum(int ...$numbers): int {
    return array_sum($numbers);
}

// Type declarations
function process(int $id, string $name): bool { ... }

// Arrow functions (PHP 7.4+)
$doubled = array_map(fn($n) => $n * 2, [1, 2, 3]);

// Strict types — always put at top of file
declare(strict_types=1);
```

## Include / Require

```php
include "file.php";         // Warning if not found
require "file.php";         // Fatal error if not found
include_once "file.php";    // Include only once
require_once "file.php";

// Usually used for config, functions, classes
```

## Error Handling

```php
// Basic
if (!$result) {
    trigger_error("Something went wrong", E_USER_WARNING);
}

// Try / catch
try {
    $result = riskyOperation();
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo "General error: " . $e->getMessage();
} finally {
    cleanup();
}

// Custom error handler
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
```

## Superglobals

```php
$_GET      // Query string parameters (?id=5)
$_POST     // Form POST data
$_REQUEST  // GET + POST combined
$_SERVER   // Server/environment info ($_SERVER['REQUEST_METHOD'])
$_SESSION  // Session data
$_COOKIE   // Cookie data
$_FILES    // Uploaded files
$_ENV      // Environment variables
$GLOBALS   // All global variables

// Sessions
session_start();
$_SESSION['user_id'] = 5;
$userId = $_SESSION['user_id'] ?? null;
session_destroy();
```

## Forms

```php
// In HTML form: <form method="POST" action="handler.php">
$name = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

// File uploads
if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    move_uploaded_file(
        $_FILES['file']['tmp_name'],
        'uploads/' . $_FILES['file']['name']
    );
}
```

## File I/O

```php
$content = file_get_contents('file.txt');
file_put_contents('file.txt', $content);

$lines = file('file.txt');   // Returns array of lines

// Manual
$handle = fopen('file.txt', 'r');
while (($line = fgets($handle)) !== false) { ... }
fclose($handle);
```

## Common Built-in Functions

```php
// Math
abs, round, ceil, floor, min, max, rand, number_format

// Date/Time
date('Y-m-d H:i:s');
time();
strtotime('next Monday');
DateTime / DateTimeImmutable

// Array
count, array_map, array_filter, array_reduce, array_merge
array_search, in_array, array_key_exists
extract / compact

// String
trim, strlen, strpos, substr, str_replace, explode, implode
htmlspecialchars, strip_tags, nl2br
json_encode, json_decode, serialize, unserialize

// Variable
isset, empty, is_null, unset
gettype, var_dump, print_r
```

## Best Practices

```php
declare(strict_types=1);  // Always use strict types
error_reporting(E_ALL);   // Report all errors during dev
ini_set('display_errors', '1');

// Never trust user input — always validate + sanitize
// Always use prepared statements for SQL
// Always use === (strict comparison) over ==
// Name things clearly — no abbreviations
```
