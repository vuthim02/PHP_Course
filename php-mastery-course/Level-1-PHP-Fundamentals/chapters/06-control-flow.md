# Chapter 6: Control Flow

## Learning Objectives

- Use if, else, elseif for conditional logic
- Master the match expression (PHP 8+)
- Use switch statements appropriately
- Understand ternary operator best practices
- Handle null with null coalescing operator

---

Think of control flow like a choose-your-own-adventure book. At each decision point, your code asks a question ("Is the user logged in?"), and based on the answer, it takes a different path. Without control flow, your code would just run top-to-bottom every time — like reading a book with no choices.

## 6.1 if / else / elseif

```php
<?php
// Basic if
if ($score >= 90) {
    echo "A";
}

// if/else
if ($age >= 18) {
    echo "Adult";
} else {
    echo "Minor";
}

// if/elseif/else
if ($grade >= 90) {
    echo "A";
} elseif ($grade >= 80) {
    echo "B";
} elseif ($grade >= 70) {
    echo "C";
} elseif ($grade >= 60) {
    echo "D";
} else {
    echo "F";
}
```

### Alternative Syntax (for templates)

```php
<?php if ($loggedIn): ?>
    <p>Welcome, <?= htmlspecialchars($name) ?></p>
<?php elseif ($isAdmin): ?>
    <p>Welcome, Admin</p>
<?php else: ?>
    <p>Please log in</p>
<?php endif; ?>
```

### Best Practices

```php
<?php
// BAD: Deep nesting
if ($user) {
    if ($user->isActive()) {
        if ($user->hasPermission('edit')) {
            // do something
        }
    }
}

// GOOD: Early returns
if (!$user) {
    return;
}
if (!$user->isActive()) {
    return;
}
if (!$user->hasPermission('edit')) {
    return;
}
// do something

// GOOD: Guard clause
function processOrder(?Order $order): void {
    if ($order === null || !$order->isPaid()) {
        throw new InvalidOrderException('Cannot process unpaid order');
    }
    // Process order...
}
```

---

## 6.2 match Expression (PHP 8+)

```php
<?php
// match is like switch but returns a value and uses strict comparison

// Basic match
$statusCode = 200;
$message = match ($statusCode) {
    200 => 'OK',
    201 => 'Created',
    301 => 'Moved Permanently',
    404 => 'Not Found',
    500 => 'Internal Server Error',
    default => 'Unknown Status',
};

// Multiple conditions (OR)
$day = 'Saturday';
$type = match ($day) {
    'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday' => 'Weekday',
    'Saturday', 'Sunday' => 'Weekend',
};

// With conditions (boolean match)
$age = 25;
$category = match (true) {
    $age < 13 => 'Child',
    $age < 18 => 'Teenager',
    $age < 65 => 'Adult',
    default => 'Senior',
};

// Strict comparison (match uses ===, switch uses ==)
match ('5') {
    5 => 'integer five',        // Won't match (string !== int)
    '5' => 'string five',       // This matches
};

// No default throws UnhandledMatchError
match (42) {
    1 => 'one',
    2 => 'two',
    // UnhandledMatchError: 42 not handled
};
```

---

## 6.3 switch Statement

```php
<?php
// Basic switch (uses loose comparison ==)
switch ($statusCode) {
    case 200:
    case 201:
        echo "Success";
        break;
    case 301:
    case 302:
        echo "Redirect";
        break;
    case 404:
        echo "Not Found";
        break;
    case 500:
        echo "Server Error";
        break;
    default:
        echo "Unknown";
}

// Common mistake: forgetting break
switch ($value) {
    case 1:
        echo "One";  // Falls through to case 2!
    case 2:
        echo "Two";  // Prints "OneTwo" if value is 1
        break;
}
```

### match vs switch

| Aspect | match | switch |
|--------|-------|--------|
| Comparison | Strict (===) | Loose (==) |
| Returns value | Yes | No |
| Fall-through | No (automatic) | Yes (needs break) |
| Exception on no match | UnhandledMatchError | No (continues) |
| Multiple conditions | Comma-separated | Case stacking |
| Expression support | Yes | No |

---

## 6.4 Ternary Operator

```php
<?php
// Basic ternary
$status = ($age >= 18) ? 'Adult' : 'Minor';

// Chained ternary (avoid! hard to read)
$result = ($a > $b) ? 'A wins' : ($a < $b) ? 'B wins' : 'Tie';

// Better approach
if ($a > $b) {
    $result = 'A wins';
} elseif ($a < $b) {
    $result = 'B wins';
} else {
    $result = 'Tie';
}

// Short ternary (returns value if truthy)
$name = $userName ?: 'Guest';  // Same as: $userName ? $userName : 'Guest'
```

---

## 6.5 Exercises

1. Write a grade calculator using match expression
2. Convert a deep nested if/else to early-return style
3. Create a routing function using match
4. Compare match vs switch with loose vs strict comparison
5. Write an age verification function with multiple conditions

---

## Further Reading

- **Doc:** [Control Structures](https://www.php.net/manual/en/language.control-structures.php)
- **RFC:** [Match Expression](https://wiki.php.net/rfc/match_expression_v2)
- **Doc:** [Comparison Operators](https://www.php.net/manual/en/language.operators.comparison.php)
