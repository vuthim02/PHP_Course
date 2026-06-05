# Chapter 19: Knowledge Verification

## Level 1 Assessment

### Quiz (20 Questions)

1. What does PHP stand for?
2. How do you output text in PHP?
3. What's the difference between `==` and `===`?
4. How do you define a constant?
5. What's the difference between `include` and `require`?
6. What's a superglobal? Name 3.
7. How do you start a session in PHP?
8. What does `try/catch` do?
9. How do you declare a function with type hints?
10. What's the difference between `foreach` and `for`?
11. How do you read a file in PHP?
12. What's the purpose of `htmlspecialchars()`?
13. How do you handle form submissions?
14. What's a multidimensional array?
15. How do you generate a random number?
16. What's the null coalescing operator (`??`)?
17. How do you format dates in PHP?
18. What's the difference between `echo` and `print`?
19. How do you prevent SQL injection in PHP?
20. What's OPcache and why is it important?

### Coding Challenges

1. **FizzBuzz**: Print numbers 1-100, replacing multiples of 3 with "Fizz", 5 with "Buzz", both with "FizzBuzz"
2. **Palindrome Checker**: Check if a string reads the same forwards and backwards
3. **CSV Parser**: Read a CSV file and convert it to an array of associative arrays
4. **Form Validator**: Build a complete form validation class
5. **Array Transform**: Use array_map to transform an array of user data
6. **Session Counter**: Track page visits using sessions
7. **File Logger**: Write a logging function with timestamps
8. **Date Calculator**: Calculate age from a birth date
9. **Random Password Generator**: Generate cryptographically secure passwords
10. **JSON API Client**: Fetch and parse JSON from an API

### Projects

**Beginner:** Build a calculator that handles GET/POST input
**Intermediate:** Create a contact form with validation, sanitization, CSRF protection
**Professional:** Build a URL shortener with file-based storage
**Enterprise:** Create a templating engine with includes, layouts, and escaping

---

## Answer Key

### Quiz Answers

1. **PHP: Hypertext Preprocessor** (recursive acronym)
2. `echo`, `print`, or `printf()`. `echo` is the most common: `echo "Hello";`
3. `==` compares values after type coercion. `===` compares values and types (strict). `"1" == 1` is true, `"1" === 1` is false.
4. Using `define('NAME', value)` or `const NAME = value;` (const is compile-time, define is runtime)
5. `require` throws a fatal error if the file is missing; `include` emits a warning and continues
6. A superglobal is a built-in variable accessible everywhere. Examples: `$_GET`, `$_POST`, `$_SERVER`, `$_SESSION`, `$_COOKIE`, `$_FILES`, `$_REQUEST`, `$GLOBALS`
7. `session_start()` — must be called before any output
8. `try` attempts code, `catch` handles exceptions thrown in the try block, `finally` runs regardless
9. `function add(int $a, int $b): int { return $a + $b; }`
10. `foreach` iterates over arrays/objects; `for` iterates with a counter. `foreach` is preferred for arrays
11. `file_get_contents()` for reading entire file, `fopen()`/`fgets()` for line-by-line, `file()` for array of lines
12. Escapes HTML special characters (`<`, `>`, `&`, `"`, `'`) to prevent XSS attacks
13. Check request method (`$_SERVER['REQUEST_METHOD']`), validate input, sanitize with `filter_var()`, process, redirect
14. An array where elements are themselves arrays: `$matrix = [[1,2], [3,4]];`
15. `rand($min, $max)` or `random_int($min, $max)` (cryptographically secure)
16. `$value = $var ?? 'default';` — returns `$var` if set and non-null, otherwise `'default'`
17. Using `DateTime` class or `date('Y-m-d', $timestamp)` with format characters
18. `echo` returns void, `print` returns 1. `echo` can take multiple arguments: `echo $a, $b;`
19. Use prepared statements with PDO: `$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?'); $stmt->execute([$id]);`
20. OPcache stores compiled PHP opcodes in shared memory, eliminating the compilation step on every request. Dramatically improves performance.

### Coding Challenge Solutions

**Challenge 1: FizzBuzz**
```php
<?php
for ($i = 1; $i <= 100; $i++) {
    if ($i % 15 === 0) echo "FizzBuzz\n";
    elseif ($i % 3 === 0) echo "Fizz\n";
    elseif ($i % 5 === 0) echo "Buzz\n";
    else echo "$i\n";
}
```

**Challenge 2: Palindrome Checker**
```php
<?php
function isPalindrome(string $str): bool {
    $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($str));
    return $cleaned === strrev($cleaned);
}
```

**Challenge 3: CSV Parser**
```php
<?php
function csvToArray(string $path): array {
    $rows = array_map('str_getcsv', file($path));
    $headers = array_shift($rows);
    return array_map(fn($row) => array_combine($headers, $row), $rows);
}
```

**Challenge 4: Form Validator**
```php
<?php
class Validator {
    private array $errors = [];

    public function required(string $field, mixed $value): self {
        if (empty($value)) $this->errors[$field][] = "$field is required";
        return $this;
    }

    public function email(string $field, string $value): self {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) 
            $this->errors[$field][] = "$field must be a valid email";
        return $this;
    }

    public function minLength(string $field, string $value, int $min): self {
        if (strlen($value) < $min) 
            $this->errors[$field][] = "$field must be at least $min characters";
        return $this;
    }

    public function isValid(): bool { return empty($this->errors); }
    public function getErrors(): array { return $this->errors; }
}
```

**Challenge 5: Array Transform**
```php
<?php
$users = [
    ['name' => 'Alice', 'email' => 'ALICE@EXAMPLE.COM'],
    ['name' => 'Bob', 'email' => 'BOB@EXAMPLE.COM'],
];
$formatted = array_map(fn($u) => [
    'name' => ucwords(strtolower($u['name'])),
    'email' => strtolower($u['email']),
], $users);
```

**Challenge 6: Session Counter**
```php
<?php
session_start();
$_SESSION['visits'] = ($_SESSION['visits'] ?? 0) + 1;
echo "Page visits: {$_SESSION['visits']}";
```

**Challenge 7: File Logger**
```php
<?php
function logMessage(string $level, string $message): void {
    $entry = sprintf("[%s] %s: %s%s", date('Y-m-d H:i:s'), strtoupper($level), $message, PHP_EOL);
    file_put_contents(__DIR__ . '/app.log', $entry, FILE_APPEND);
}
```

**Challenge 8: Date Calculator**
```php
<?php
function calculateAge(string $birthDate): int {
    $birth = new DateTime($birthDate);
    $now = new DateTime();
    return (int) $birth->diff($now)->y;
}
```

**Challenge 9: Random Password Generator**
```php
<?php
function generatePassword(int $length = 16): string {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}
```

**Challenge 10: JSON API Client**
```php
<?php
function fetchApi(string $url): array {
    $context = stream_context_create(['http' => [
        'method' => 'GET',
        'header' => "Accept: application/json\r\n",
        'timeout' => 10,
    ]]);
    $response = file_get_contents($url, false, $context);
    return json_decode($response, true) ?? [];
}
```

---

*End of Chapter 19. Proceed to Chapter 20: Capstone Project.*
