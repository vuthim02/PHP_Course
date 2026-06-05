# Chapter 2: PHP Syntax and Structure

## Learning Objectives

- Understand PHP tags and how PHP is embedded in HTML
- Write PHP statements and expressions
- Use comments effectively
- Understand PHP's whitespace handling
- Differentiate between echo, print, and output buffering

---

## 2.1 PHP Tags

### Opening and Closing Tags

```php
<?php
// Standard PHP tag (recommended)
echo "Hello, World!";
?>

<?php echo "Short syntax works too"; ?>

<?= "Short echo tag (equivalent to <?php echo)"; ?>

<?php /* Short open tags <? are deprecated, do not use */ ?>
```

### PHP in HTML

```php
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
    <h1><?= htmlspecialchars($heading) ?></h1>
    
    <ul>
    <?php foreach ($items as $item): ?>
        <li><?= htmlspecialchars($item) ?></li>
    <?php endforeach; ?>
    </ul>
    
    <?php if ($loggedIn): ?>
        <p>Welcome, <?= htmlspecialchars($username) ?>!</p>
    <?php endif; ?>
</body>
</html>
```

### Pure PHP Files

```php
<?php
// Omit closing tag in pure PHP files (prevents accidental whitespace output)
// All code here
$data = processRequest();
$result = json_encode($data);
echo $result;

// No closing ?> tag
```

**Why omit the closing `?>` tag?**
- Prevents accidental whitespace or newlines after the tag
- Those extra characters would be output to the browser
- Can break HTTP headers (headers already sent error)
- Cleaner, more professional style

---

## 2.2 PHP Statements

```php
<?php
// Each statement ends with a semicolon
$name = "Alice";           // Assignment statement
echo $name;                // Output statement
$result = 2 + 3;           // Expression statement
function greet() { ... }   // Function declaration
if ($x > 0) { ... }        // Control flow statement
return $value;             // Return statement
```

### Expression vs Statement

```php
<?php
// Expression: anything that has a value
5               // Integer literal (value: 5)
$name           // Variable (value: contents)
2 + 3           // Arithmetic (value: 5)
$x > 0          // Comparison (value: true/false)
greet()         // Function call (value: return value)

// Statement: an instruction that performs an action
$result = 2 + 3;    // Assignment statement - uses expression 2+3
echo $result;        // Output statement
if ($result > 0) {   // Control flow statement using expression
    echo "Positive";
}
```

---

## 2.3 Comments

```php
<?php
// Single-line comment (most common)

# Shell-style comment (less common, avoid for consistency)

/*
 * Multi-line comment
 * Often used for documentation headers
 * Each line typically starts with *
 */

/**
 * DocBlock comment (used for documentation)
 * 
 * @param string $name The user's name
 * @return string A greeting
 */
function greet(string $name): string {
    return "Hello, {$name}!";
}
```

### Comment Best Practices

```php
<?php
// BAD: Obvious comments
$counter = 0;  // Set counter to 0 (we can see that!)

// BAD: Outdated comments
// This function calculates tax (but now it calculates discount too!)

// GOOD: Explain WHY, not WHAT
// Use a binary search because the array is sorted
$index = binarySearch($sortedArray, $target);

// GOOD: TODO comments (searchable)
// TODO: Implement pagination for large result sets
// FIXME: This query is slow on datasets > 100k rows
// XXX: This approach won't scale horizontally
```

---

## 2.4 Output

### echo vs print

```php
<?php
// echo (language construct, no parentheses needed, returns void)
echo "Hello";
echo "Hello", " ", "World";  // Can output multiple strings
echo $variable;

// print (also a language construct, returns 1)
print "Hello\n";  // Slightly slower than echo due to return value

// printf (formatted output)
printf("Hello %s, you have %d messages\n", $name, $count);

// var_dump (debugging)
var_dump($variable);   // Shows type and value

// print_r (debugging, human-readable)
print_r($array);

// Output buffering
ob_start();            // Start buffering
echo "This is buffered";
$content = ob_get_clean();  // Get buffer content and clear
```

### Output Buffering

```php
<?php
// ob_start() captures all output into a buffer
ob_start();

echo "This won't be sent to the browser yet";
echo "It's stored in the buffer";

$content = ob_get_clean();  // Get and clear buffer
echo strtoupper($content);  // Transform before output
```

---

## 2.5 Best Practices

```php
<?php
// 1. Use consistent formatting
$firstName = "Alice";     // camelCase for variables
function getUserName() {} // camelCase for functions
class UserRepository {}   // PascalCase for classes
const MAX_USERS = 100;    // UPPER_SNAKE for constants

// 2. Indentation (PSR-12 standard)
if ($condition) {
    doSomething();
    if ($nested) {
        doNestedThing();
    }
}

// 3. One statement per line
$name = "Alice";
$email = "alice@example.com";

// NOT: $name = "Alice"; $email = "alice@example.com";

// 4. Avoid deep nesting (early return)
function processUser(?User $user): void {
    if ($user === null) {
        return;  // Early return
    }
    // Process user...
}
```

---

## 2.6 Exercises

1. Create a PHP file that displays "Hello, World!" using both echo and print
2. Create a PHP page embedded in HTML with dynamic title and content
3. Write a script with output buffering that transforms output to uppercase
4. Practice using var_dump and print_r on different variable types
5. Create a PHP-only file (no closing tag) and verify it works

---

## Further Reading

- **Doc:** [PHP Basic Syntax](https://www.php.net/manual/en/language.basic-syntax.php)
- **Standards:** [PSR-12 Coding Style](https://www.php-fig.org/psr/psr-12/)
- **Doc:** [Output Buffering](https://www.php.net/manual/en/book.outcontrol.php)
