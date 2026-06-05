# Chapter 10: Regular Expressions

## Learning Objectives

- Write PCRE patterns for validation
- Use preg_match, preg_replace, preg_split
- Understand regex performance
- Build pattern-based utilities

---

## 10.1 Patterns

```php
<?php
// Validation patterns
$patterns = [
    'email' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
    'phone' => '/^\+?[\d\s()-]{7,15}$/',
    'url' => '/^https?:\/\/[\w\-]+(\.[\w\-]+)+[/#?]?.*$/',
    'ip' => '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/',
    'uuid' => '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
    'username' => '/^[a-zA-Z][a-zA-Z0-9_]{2,19}$/',
    'slug' => '/^[a-z0-9]+(-[a-z0-9]+)*$/',
    'hex_color' => '/^#([0-9a-f]{3}){1,2}$/i',
];

class Validator
{
    public static function email(string $value): bool
    {
        return preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $value) === 1;
    }

    public static function url(string $value): bool
    {
        return preg_match('/^https?:\/\/[\w\-]+(\.[\w\-]+)+[/#?]?.*$/', $value) === 1;
    }

    public static function strongPassword(string $value): bool
    {
        return preg_match(
            '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/',
            $value
        ) === 1;
    }
}
```

---

## 10.2 preg_match / preg_replace

```php
<?php
// preg_match
$text = 'Contact: john@example.com or support@company.com';
$pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';

if (preg_match($pattern, $text, $matches)) {
    echo $matches[0]; // john@example.com
}

// preg_match_all — get all matches
preg_match_all($pattern, $text, $allMatches);
print_r($allMatches[0]);
// ['john@example.com', 'support@company.com']

// preg_replace
$text = 'The event is on 2024-12-25 at 15:30:00';
$pattern = '/(\d{4})-(\d{2})-(\d{2})/';
$replaced = preg_replace($pattern, '$3/$2/$1', $text);
// The event is on 25/12/2024 at 15:30:00

// preg_replace with callback
$text = 'User: alice, bob, charlie';
$result = preg_replace_callback(
    '/(\w+)/',
    fn(array $m) => ucfirst($m[1]),
    $text
);
// User: Alice, Bob, Charlie

// preg_split
$csv = 'apple,banana,cherry,date';
$fruits = preg_split('/,\s*/', $csv);
// ['apple', 'banana', 'cherry', 'date']
```

---

## 10.3 Exercises

1. Write regex patterns for validating credit card numbers, postal codes, and dates
2. Build a URL router that uses regex patterns for route matching
3. Create a template engine that replaces {{variable}} placeholders
4. Implement a Markdown-to-HTML parser using regex

---

## Further Reading

- **Doc:** [PHP PCRE](https://www.php.net/manual/en/book.pcre.php)
- **Resource:** [Regex101](https://regex101.com/)
