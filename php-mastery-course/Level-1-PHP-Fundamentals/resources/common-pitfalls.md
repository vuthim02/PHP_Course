# Level 1 — Common Pitfalls & How to Avoid Them

## 1. Loose Comparison Issues

```php
// ❌ BAD: Loose comparison leads to surprises
if (in_array("search", [0, 1, 2])) {
    // This is TRUE because 0 == "search"!
}

if ("php" == 0) {
    // TRUE — PHP converts "php" to 0
}

// ✅ GOOD: Always use strict comparison
if (in_array("search", [0, 1, 2], true)) {
    // FALSE — correct behavior
}
```

**Rule:** Pass `true` as the third argument to `in_array`, `array_search`, and `array_keys`. Use `===` and `!==` everywhere.

---

## 2. Forgetting to Start Sessions

```php
// ❌ BAD: Session used without starting
$_SESSION['user_id'] = 5;
// Warning: session_start() was never called

// ✅ GOOD: Always call session_start() on every page
session_start();
$_SESSION['user_id'] = 5;
```

**Rule:** Put `session_start()` at the top of every page that needs sessions. Even better — use a front controller or middleware.

---

## 3. Trusting User Input

```php
// ❌ BAD: Direct output of user input — XSS vulnerability
echo $_GET['name'];

// ✅ GOOD: Always escape output
echo htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8');

// ❌ BAD: Direct SQL embedding — SQL injection
$sql = "SELECT * FROM users WHERE id = " . $_GET['id'];

// ✅ GOOD: Always use prepared statements
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_GET['id']]);
```

**Rule:** Never trust `$_GET`, `$_POST`, `$_COOKIE`, `$_FILES`, or `$_SERVER`. Filter input, escape output.

---

## 4. String Interpolation Confusion

```php
// ❌ BAD: Array keys don't work in double quotes
echo "$user[name]";  // Works but PHP looks for constant "name" first

// ✅ GOOD: Use braces
echo "{$user['name']}";

// ❌ BAD: Object properties
echo "$user->name";  // Works sometimes, confusing

// ✅ GOOD
echo "{$user->name}";
```

**Rule:** Always use `{$var}` braces in double-quoted strings. Or just use concatenation/string interpolation with clarity.

---

## 5. Off-by-One in Loops

```php
$items = ['a', 'b', 'c'];

// ❌ BAD: Off by one
for ($i = 0; $i <= count($items); $i++) {
    echo $items[$i];  // Warning: undefined offset 3
}

// ✅ GOOD
for ($i = 0; $i < count($items); $i++) {
    echo $items[$i];
}

// ✅ BEST: foreach when possible
foreach ($items as $item) {
    echo $item;
}
```

**Rule:** Use `foreach` for arrays. Use `<` not `<=` in `for` loops.

---

## 6. Modifying Arrays While Iterating

```php
// ❌ BAD: Removing items while iterating
foreach ($items as $key => $value) {
    if ($value < 0) {
        unset($items[$key]);  // Can skip elements
    }
}

// ✅ GOOD: Collect keys first, then remove
$toRemove = [];
foreach ($items as $key => $value) {
    if ($value < 0) {
        $toRemove[] = $key;
    }
}
foreach ($toRemove as $key) {
    unset($items[$key]);
}

// ✅ BETTER: Use array_filter
$items = array_filter($items, fn($v) => $v >= 0);
```

---

## 7. Reference vs Copy Confusion

```php
// ❌ BAD: Unexpected mutation
function addTax($price) {
    $price *= 1.2;
}
$total = 100;
addTax($total);
echo $total;  // Still 100 — scalar passed by value!

// ✅ GOOD: Return the value
function addTax(float $price): float {
    return $price * 1.2;
}
$total = addTax(100);

// ❌ BAD: foreach modifies original array?
$items = [1, 2, 3];
foreach ($items as &$item) {
    $item *= 2;
}
// Now $items = [2, 4, 3] — the reference leaks!

// ✅ GOOD: Unset the reference
unset($item);
```

**Rule:** Scalar values are copied. Objects are referenced. Use `&` references with extreme care and always `unset()` the reference after the loop.

---

## 8. Shorthand Echo Confusion

```php
<?= "Hello" ?>  // Short echo tag — always available in PHP 5.4+
               // Equivalent to <?php echo "Hello"; ?>
```

**Rule:** `<?=` is safe and standard. Never use `<?` (short open tag) — it depends on `short_open_tag` ini setting.

---

## 9. Confusing `empty()`, `isset()`, and `is_null()`

```php
$var = 0;

isset($var);    // true — variable exists and is not null
empty($var);    // true — 0, '', '0', false, [], null are "empty"
is_null($var);  // false — 0 is not null

// Use cases:
isset($_POST['name']);         // "Does this key exist?"
empty($_POST['name']);         // "Does it exist AND have a non-empty value?"
$_POST['name'] ?? '';          // "Give me the value or a default"
```

**Rule:**
- `isset()` = exists and is not null
- `empty()` = exists and is falsy
- `??` = null coalescing (exists and is not null)
- Use `!empty()` for "has a value" checks in forms

---

## 10. Forgetting `declare(strict_types=1)`

```php
// Without strict types:
function add(int $a, int $b): int {
    return $a + $b;
}
echo add("5", "3");  // 8 — PHP coerces strings to ints silently

// With strict types (put at top of file):
declare(strict_types=1);
echo add("5", "3");  // TypeError: must be int, string given
```

**Rule:** Put `declare(strict_types=1);` as the very first line (after `<?php`) in every file. It catches type bugs early.

---

## 11. Memory & Performance

```php
// ❌ BAD: Loading entire file into memory
$data = file_get_contents('huge_file.csv');
$lines = explode("\n", $data);

// ✅ GOOD: Read line by line
$handle = fopen('huge_file.csv', 'r');
while (($line = fgets($handle)) !== false) {
    processLine($line);
}
fclose($handle);

// ❌ BAD: Building large strings with .
$result = '';
foreach ($items as $item) {
    $result .= $item;  // Creates a new string each iteration
}

// ✅ GOOD: Use array + implode
$parts = [];
foreach ($items as $item) {
    $parts[] = $item;
}
$result = implode('', $parts);
```

---

## Summary Checklist

- [ ] Always use `===` / `!==`
- [ ] Always use `htmlspecialchars()` on output
- [ ] Always use prepared statements for SQL
- [ ] Always start sessions with `session_start()`
- [ ] Always use `declare(strict_types=1)`
- [ ] Always use `??` for defaults instead of `isset()` + ternary
- [ ] Always unset `&` references after loops
- [ ] Prefer `foreach` over `for`
- [ ] Prefer `array_map` / `array_filter` over manual loops
