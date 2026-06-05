# PHP Debugging Guide for Beginners

## Essential Debugging Techniques

### 1. The `var_dump()` & `die()` Combo

```php
// Quick-and-dirty variable inspection
$user = ['name' => 'Alice', 'age' => 30];
var_dump($user);
die();  // or exit;
```

**Tip:** If your output is buried in HTML, wrap in `<pre>` tags:
```php
echo '<pre>';
var_dump($user);
echo '</pre>';
```

### 2. `print_r()` for Arrays

```php
// More readable than var_dump for arrays
print_r($array);
// Or with return:
$output = print_r($array, true);
echo nl2br($output);
```

### 3. `error_log()` — The Right Way

```php
// Writes to PHP's error log (check php.ini for path)
error_log("User ID: $userId");

// For debugging without polluting output:
error_log(print_r($complexData, true));
```

### 4. Debugging with `xdebug`

Install Xdebug and configure:
```ini
; php.ini
zend_extension=xdebug
xdebug.mode=debug
xdebug.start_with_request=yes
xdebug.client_port=9003
```

Then set breakpoints in your IDE. PHP will pause execution, and you can inspect variables, step through code, and watch the call stack.

### 5. PHP Built-in Server Logs

```bash
php -S localhost:8000 2>&1 | tee server.log
```

## Common Error Types

| Error | Severity | Description |
|-------|----------|-------------|
| `E_ERROR` | Fatal | Script stops — memory exhausted, undefined class |
| `E_WARNING` | Warning | Non-fatal — include fails, division by zero |
| `E_NOTICE` | Notice | Minor — undefined variable, array key |
| `E_PARSE` | Parse | Syntax error — script never runs |
| `E_DEPRECATED` | Notice | Feature will be removed in future PHP |

## Reading a PHP Error

```
PHP Fatal error:  Uncaught TypeError: Return value of calculateTotal()
must be of the type float, string returned in /var/www/app.php:25
Stack trace:
#0 /var/www/index.php(10): calculateTotal()
#1 {main}
  thrown in /var/www/app.php on line 25
```

**Breakdown:**
- **Level:** `Fatal error` — execution stopped
- **Type:** `TypeError` — wrong return type
- **Message:** Function `calculateTotal` should return float but returned string
- **File:** `/var/www/app.php` line 25
- **Trace:** Called from `index.php` line 10

## Debugging Checklist

1. **Check the error log:**
   ```bash
   tail -f /var/log/php_errors.log
   # Or find your log:
   php -i | grep error_log
   ```

2. **Enable all errors during development:**
   ```php
   ini_set('display_errors', '1');
   ini_set('display_startup_errors', '1');
   error_reporting(E_ALL);
   ```

3. **Isolate the problem:**
   - Comment out sections until the error disappears
   - Check the last line before the error

4. **Check common culprits:**
   - Missing semicolons
   - Unmatched braces/parentheses
   - Undefined variables (typos)
   - Wrong array keys
   - Incorrect include paths

5. **Use `debug_backtrace()`:**
   ```php
   echo '<pre>';
   print_r(debug_backtrace());
   echo '</pre>';
   ```
   Shows the full call stack leading to the current point.

6. **Use `get_defined_vars()`:**
   ```php
   echo '<pre>';
   print_r(get_defined_vars());
   echo '</pre>';
   ```
   Dumps ALL variables in scope — great for finding where a value goes wrong.

## PHP Interactive Shell (REPL)

```bash
# Start interactive mode
php -a

# Now you can type PHP interactively:
php > $x = 10;
php > echo $x * 2;
20
php > function square($n) { return $n * $n; }
php > echo square(5);
25
php > exit
```

## Linting (Syntax Check)

```bash
# Check syntax without executing
php -l file.php

# Check all PHP files in a project
find . -name "*.php" -exec php -l {} \;
```

## Common Errors & Solutions

| Error | Likely Cause | Fix |
|-------|-------------|-----|
| `Undefined variable` | Typo or variable not in scope | Check spelling, pass as parameter |
| `Undefined index` | Array key doesn't exist | Use `$arr['key'] ?? 'default'` |
| `Call to undefined function` | Missing include, typo | Check require_once, function name |
| `Cannot modify header information` | Output before header() | Check for whitespace before `<?php` |
| `Maximum execution time exceeded` | Infinite loop or slow operation | Fix loop, increase `max_execution_time` |
| `Allowed memory size exhausted` | Processing too much data | Increase `memory_limit`, optimize code |
| `Unexpected '{'` | Missing semicolon on previous line | Check the line before the error |
