# Chapter 11: Strings

## Learning Objectives

- Create and manipulate strings using all available syntaxes
- Master string interpolation and concatenation
- Use string functions effectively
- Understand encoding and mbstring

---

## 11.1 String Syntax

```php
<?php
// Single quotes (literal, no interpolation)
$name = 'Alice';
echo 'Hello, $name!';   // Hello, $name! (no interpolation)
echo 'Hello, ' . $name; // Hello, Alice

// Double quotes (interpolation)
echo "Hello, $name!";         // Hello, Alice!
echo "Hello, {$name}!";       // Hello, Alice! (explicit)
echo "Value: {$array['key']}"; // Array access

// Heredoc (multi-line, interpolation)
$html = <<<HTML
<div class="user">
    <h2>$name</h2>
    <p>Email: {$email}</p>
</div>
HTML;

// Nowdoc (multi-line, no interpolation, like single quotes)
$sql = <<<'SQL'
SELECT * FROM users WHERE name = '$name'
SQL;
```

---

## 11.2 String Functions

```php
<?php
$text = "  Hello, World! Welcome to PHP.  ";

// Length
echo strlen($text);           // Bytes
echo mb_strlen($text, 'UTF-8'); // Characters (multibyte safe)

// Case
echo strtoupper($text);
echo strtolower($text);
echo ucwords($text);          // Title Case
echo ucfirst($text);          // First character uppercase

// Finding
echo strpos($text, "World");    // Position (7)
echo stripos($text, "world");   // Case-insensitive
echo str_contains($text, "PHP"); // true (PHP 8+)
echo str_starts_with($text, "  Hello"); // true (PHP 8+)
echo str_ends_with($text, "PHP.  ");    // true (PHP 8+)

// Extracting
echo substr($text, 2, 5);       // "Hello"
echo substr($text, -4);         // "PHP."

// Replacing
echo str_replace("World", "PHP", $text);
echo str_ireplace("world", "PHP", $text); // Case-insensitive
echo substr_replace($text, "Hi", 2, 5);

// Formatting
echo trim($text);               // Strip whitespace both ends
echo ltrim($text);              // Left only
echo rtrim($text);              // Right only
echo str_pad("PHP", 10, "-");   // "PHP-------"
echo number_format(12345.67, 2); // "12,345.67"

// Splitting/Joining
print_r(explode(" ", trim($text)));
echo implode(", ", ['a', 'b', 'c']);  // "a, b, c"
```

---

## 11.3 Multibyte Strings

```php
<?php
// mbstring functions handle multi-byte encodings (UTF-8, etc.)
$japanese = "こんにちは世界";

echo strlen($japanese);          // 21 bytes (wrong count for characters)
echo mb_strlen($japanese, 'UTF-8'); // 7 characters (correct!)

echo strtoupper($japanese);      // May not work correctly
echo mb_strtoupper($japanese);   // Works correctly

// Always use mb_* functions for user input
function safeTruncate(string $text, int $maxChars): string {
    if (mb_strlen($text) <= $maxChars) {
        return $text;
    }
    return mb_substr($text, 0, $maxChars - 3) . '...';
}
```

---

## 11.4 Regular Expressions

```php
<?php
// preg_match - check if pattern exists
$email = "user@example.com";
if (preg_match('/^[\w.-]+@[\w.-]+\.\w{2,}$/', $email)) {
    echo "Valid email";
}

// preg_match_all - find all matches
$text = "Contact: alice@a.com or bob@b.com";
preg_match_all('/[\w.-]+@[\w.-]+\.\w{2,}/', $text, $matches);
print_r($matches[0]);  // ['alice@a.com', 'bob@b.com']

// preg_replace - search and replace
$result = preg_replace('/\s+/', ' ', "Too   many   spaces");
// "Too many spaces"

// preg_split - split by pattern
$parts = preg_split('/[,\s]+/', "apple, banana, cherry");
// ['apple', 'banana', 'cherry']
```

---

## 11.5 Exercises

1. Validate email addresses using regex
2. Truncate text to 100 characters without breaking words
3. Extract all URLs from a block of text
4. Convert camelCase to snake_case
5. Format a number as currency ($1,234.56)
6. Remove all HTML tags from a string (strip_tags)
7. Check if a string is a palindrome

---

## Further Reading

- **Doc:** [PHP String Functions](https://www.php.net/manual/en/ref.strings.php)
- **Doc:** [mbstring](https://www.php.net/manual/en/book.mbstring.php)
- **Doc:** [PCRE (Regex)](https://www.php.net/manual/en/book.pcre.php)
