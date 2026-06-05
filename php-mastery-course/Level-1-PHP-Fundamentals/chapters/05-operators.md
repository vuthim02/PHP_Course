# Chapter 5: Operators

## Learning Objectives

- Use all PHP operators correctly
- Understand operator precedence
- Master comparison (including strict comparison)
- Use logical operators for complex conditions
- Apply bitwise, ternary, and null coalescing operators

---

## 5.1 Arithmetic Operators

```php
<?php
$a = 10;
$b = 3;

echo $a + $b;   // 13  (addition)
echo $a - $b;   // 7   (subtraction)
echo $a * $b;   // 30  (multiplication)
echo $a / $b;   // 3.33 (division - always returns float)
echo $a % $b;   // 1   (modulus - remainder)
echo $a ** $b;  // 1000 (exponentiation, PHP 5.6+)

// Increment/Decrement
echo ++$a;  // 11 (pre-increment: increment, then return)
echo $a++;  // 11 (post-increment: return, then increment)
echo --$a;  // 10 (pre-decrement)
echo $a--;  // 10 (post-decrement)
```

---

## 5.2 Assignment Operators

```php
<?php
$a = 10;      // Assignment
$a += 5;      // $a = $a + 5  → 15
$a -= 3;      // $a = $a - 3  → 12
$a *= 2;      // $a = $a * 2  → 24
$a /= 4;      // $a = $a / 4  → 6
$a %= 3;      // $a = $a % 3  → 0
$a **= 2;     // $a = $a ** 2 → 0 (0^2 = 0)

// String assignment
$text = "Hello";
$text .= " World";  // "Hello World"

// Null coalescing assignment (PHP 7.4+)
$data ??= 'default';  // Assign only if null
```

---

## 5.3 Comparison Operators

```php
<?php
// Loose comparison (== checks value after type juggling)
var_dump(5 == 5);       // true
var_dump(5 == "5");     // true (string "5" converted to int 5)
var_dump(5 == "5abc");  // false in PHP 8 (was true in PHP 7)
var_dump(0 == "abc");   // false in PHP 8 (was true in PHP 7)
var_dump([] == false);  // true (empty array == false)
var_dump(null == false); // true (!)

// Strict comparison (=== checks value AND type)
var_dump(5 === 5);       // true
var_dump(5 === "5");     // false (int vs string)
var_dump(0 === false);   // false (int vs bool)

// Other comparisons
var_dump(5 != 3);        // true (loose not equal)
var_dump(5 !== "5");     // true (strict not equal)
var_dump(5 > 3);         // true (greater than)
var_dump(5 < 3);         // false (less than)
var_dump(5 >= 5);        // true (greater than or equal)
var_dump(5 <= 5);        // true (less than or equal)

// Spaceship operator (PHP 7+)
echo 5 <=> 5;   // 0 (equal)
echo 5 <=> 3;   // 1 (left is greater)
echo 3 <=> 5;   // -1 (left is less)
```

---

## 5.4 Logical Operators

```php
<?php
$a = true;
$b = false;

// AND (both must be true)
var_dump($a && $b);  // false
var_dump($a and $b); // false (lower precedence)

// OR (at least one must be true)
var_dump($a || $b);  // true
var_dump($a or $b);  // true (lower precedence)

// NOT (inverts)
var_dump(!$a);       // false

// XOR (exactly one must be true)
var_dump($a xor $b); // true

// Short-circuit evaluation
function expensiveCheck(): bool {
    echo "Called!";
    return true;
}

$result = false && expensiveCheck();  // expensiveCheck NOT called
$result = true || expensiveCheck();   // expensiveCheck NOT called
```

---

## 5.5 String Operators

```php
<?php
// Concatenation
$greeting = "Hello, " . "World!";  // "Hello, World!"
$full = $firstName . " " . $lastName;

// Concatenating assignment
$message = "Error";
$message .= ": " . $errorMessage;
```

---

## 5.6 Ternary and Null Coalescing

```php
<?php
// Ternary operator: condition ? value_if_true : value_if_false
$age = 20;
$status = ($age >= 18) ? "Adult" : "Minor";

// Short ternary (PHP 5.3+): returns $value if truthy, else $default
$name = $userName ?: "Guest";  // Same as: $userName ? $userName : "Guest"

// Null coalescing (PHP 7+): returns first non-null value
$name = $_GET['name'] ?? "Guest";         // If set and not null
$name = $user['name'] ?? 'anonymous';     // Safer than ternary

// Null coalescing chain (PHP 7+)
$country = $user['address']['country'] ?? 'Unknown';

// Null coalescing assignment (PHP 7.4+)
$data['key'] ??= 'default';  // Only sets if not already set or null
```

---

## 5.7 Operator Precedence

```php
<?php
// Precedence determines which operators run first
$result = 5 + 3 * 4;    // 17 (not 32) — multiplication first
$result = (5 + 3) * 4;  // 32 — parentheses override

// String concatenation versus addition
$result = "Total: " . 5 + 3;   // "Total: 5" then +3 → 3 (weird!)
$result = "Total: " . (5 + 3); // "Total: 8" (correct!)

// Logical precedence
$result = true || false && false;  // true (&& higher precedence than ||)
$result = (true || false) && false; // false

// Use parentheses to make intent clear
$isValid = ($age >= 18) && ($country !== 'USA') || ($isVip);
// Better with parentheses:
$isValid = ($age >= 18) && (($country !== 'USA') || $isVip);
```

---

## 5.8 Exercises

1. Demonstrate the difference between == and === with various values
2. Use the spaceship operator to sort an array
3. Write a ternary expression that checks if a number is even or odd
4. Use null coalescing to safely access nested array values
5. Show short-circuit evaluation with a function call
6. Find the result of: `3 + 4 * 5 / 2` without running it, then verify

---

## Further Reading

- **Doc:** [PHP Operators](https://www.php.net/manual/en/language.operators.php)
- **Doc:** [Operator Precedence](https://www.php.net/manual/en/language.operators.precedence.php)
- **RFC:** [Null Coalescing Operator](https://wiki.php.net/rfc/isset_ternary)
