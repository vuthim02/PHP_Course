# Chapter 3: Variables and Data Types

## Learning Objectives

- Declare and use variables in PHP
- Understand all PHP data types
- Master type juggling and type casting
- Understand variable variables and references
- Check types with PHP's type functions

---

## 3.1 Variables

Think of a variable like a labeled box. The `$name` is a box labeled "name", and you put the value "Alice" inside it. Later, you can look inside the box, replace what's inside, or pass the box to someone else. The box stays the same — what's inside can change.

### Variable Declaration

```php
<?php
// Variables start with $, followed by a letter or underscore
$name = "Alice";
$age = 30;
$_isActive = true;
$user1 = "Bob";

// Invalid variable names:
// $1user (starts with number)
// $user-name (hyphen not allowed)
// $user name (space not allowed)

// Valid but not recommended:
${'dynamic_name'} = "value";  // Variable variable
```

### Variable Naming Conventions

```php
<?php
// camelCase (recommended for variables)
$firstName = "Alice";
$lastName = "Smith";
$userEmailAddress = "alice@example.com";

// PascalCase (classes)
class UserAccount { }

// UPPER_SNAKE_CASE (constants)
define('MAX_LOGIN_ATTEMPTS', 5);

// snake_case (sometimes used for function names)
function get_user_name() { }  // Legacy style, avoid in new code
```

### Variable Scope

```php
<?php
$globalVar = "I'm global";

function testScope(): void {
    $localVar = "I'm local";
    
    // Can't access $globalVar here!
    echo $globalVar;  // Warning: undefined variable
    
    // Access global with 'global' keyword
    global $globalVar;
    echo $globalVar;  // Works
    
    // Or use $GLOBALS array
    echo $GLOBALS['globalVar'];
}

static $counter = 0;  // Static variable (persists between function calls)

function increment(): int {
    static $count = 0;
    $count++;
    return $count;
}

echo increment();  // 1
echo increment();  // 2
echo increment();  // 3
```

---

## 3.2 Data Types

### PHP Data Types Overview

```php
<?php
// Scalar types (single value)
$bool = true;          // boolean
$int = 42;             // integer
$float = 3.14;         // float (double)
$string = "Hello";     // string

// Compound types (multiple values)
$array = [1, 2, 3];                     // array
$object = new stdClass();               // object
$callable = fn() => 'hello';            // callable
$iterable = [1, 2, 3];                  // iterable

// Special types
$null = null;                           // null
// resource (external resource like file handle)
$resource = fopen('file.txt', 'r');     // resource
```

### Type Checking Functions

```php
<?php
$value = 42;

var_dump(is_int($value));      // true
var_dump(is_string($value));   // false
var_dump(is_bool($value));     // false
var_dump(is_float($value));    // false
var_dump(is_array($value));    // false
var_dump(is_object($value));   // false
var_dump(is_null($value));     // false
var_dump(is_numeric($value));  // true (42 is numeric)
var_dump(is_scalar($value));   // true (scalar = int, float, string, bool)

// Get the type
echo gettype($value);  // "integer"

// Validate type (strict)
var_dump(is_int(42));       // true
var_dump(is_int("42"));     // false (string, not int)
```

### Type Juggling (Coercion)

```php
<?php
// PHP automatically converts types when needed

// String to number
$result = "5" + 3;           // 8 (string "5" converted to int 5)
$result = "5 apples" + 3;    // 8 (PHP 8, warning: "5 apples" → 5)
$result = "apples" + 3;      // 3 (PHP 8, warning: "apples" → 0)

// Number to string
$text = "You have " . 5 . " messages";  // "You have 5 messages"
$text = "Value: " . 42;                  // "Value: 42"

// Boolean conversions
var_dump((bool)"");        // false (empty string)
var_dump((bool)"0");       // false (string "0")
var_dump((bool)"hello");   // true (non-empty string)
var_dump((bool)[]);        // false (empty array)
var_dump((bool)[1]);       // true (non-empty array)
var_dump((bool)0);         // false
var_dump((bool)1);         // true
var_dump((bool)null);      // false
```

### Type Casting (Explicit)

```php
<?php
// Explicit conversion
$int = (int)"42";              // 42
$float = (float)"3.14";        // 3.14
$string = (string)42;          // "42"
$bool = (bool)1;               // true
$array = (array)$object;       // Object to array
$object = (object)['name' => 'Alice'];  // Array to stdClass

// Using functions
$int = intval("42");           // 42
$float = floatval("3.14");     // 3.14
$string = strval(42);          // "42"
$bool = boolval(1);            // true
```

---

## 3.3 Type Declarations (PHP 8)

```php
<?php
declare(strict_types=1);  // Enable strict mode

// Scalar type declarations
function add(int $a, int $b): int {
    return $a + $b;
}

// Without strict_types=1:
// add("5", 3) → 8 (string coerced to int)

// With strict_types=1:
// add("5", 3) → TypeError: Argument #1 must be of type int

// Union types (PHP 8+)
function processValue(int|string $value): int|string {
    return $value;
}

// Mixed type
function debug(mixed $value): void {
    var_dump($value);
}

// Nullable types
function findUser(int $id): ?User {
    // Returns User or null
}

// Void return type
function logMessage(string $message): void {
    // No return value
}

// Never return type (PHP 8.1+)
function redirect(string $url): never {
    header("Location: {$url}");
    exit;  // Never returns
}

// True/False types (PHP 8.2+)
function isValid(): true {
    return true;
}
```

---

## 3.4 Constants

```php
<?php
// define() - runtime constant
define('APP_NAME', 'My Application');
define('MAX_USERS', 1000);
define('FEATURES', ['auth', 'payment', 'analytics']);  // Array constants (PHP 7+)

// const keyword - compile-time constant
const VERSION = '1.0.0';
const API_URL = 'https://api.example.com/v1';

// Class constants
class Database {
    const HOST = 'localhost';
    const PORT = 3306;
    const DB_NAME = 'myapp';
}

echo Database::HOST;  // 'localhost'

// Magic constants
echo __LINE__;     // Current line number
echo __FILE__;     // Full path to current file
echo __DIR__;      // Directory of current file
echo __FUNCTION__; // Current function name
echo __CLASS__;    // Current class name
echo __METHOD__;   // Current method name
echo __NAMESPACE__;// Current namespace
```

---

## 3.5 References

```php
<?php
// Assignment by reference (both variables point to same data)
$a = 5;
$b = &$a;  // $b is a reference to $a
$b = 10;   // Changes $a to 10 as well
echo $a;   // 10

// Passing by reference
function addFive(int &$number): void {
    $number += 5;
}

$value = 10;
addFive($value);
echo $value;  // 15

// Returning by reference
class Registry {
    private array $data = [];
    
    public function &get(string $key): mixed {
        return $this->data[$key];
    }
}

$registry = new Registry();
$registry->get('settings')['theme'] = 'dark';  // Modifies directly
```

---

## 3.6 Exercises

1. Create variables of each data type and var_dump them
2. Write a function with strict types that adds two integers
3. Demonstrate type juggling with arithmetic and string operations
4. Create constants for your application configuration
5. Use references to modify an array in-place without returning
6. Write a function that accepts int|string|null and handles each type differently

---

## Further Reading

- **Doc:** [PHP Variables](https://www.php.net/manual/en/language.variables.php)
- **Doc:** [PHP Types](https://www.php.net/manual/en/language.types.intro.php)
- **Doc:** [Type Declarations](https://www.php.net/manual/en/language.types.declarations.php)
- **RFC:** [Strict Types](https://wiki.php.net/rfc/strict_types)
