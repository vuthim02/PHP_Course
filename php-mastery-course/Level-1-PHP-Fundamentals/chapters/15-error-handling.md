# Chapter 15: Error Handling

## Learning Objectives

- Differentiate errors, warnings, and exceptions
- Use try/catch/finally blocks
- Create custom exception classes
- Set up error and exception handlers
- Implement logging

---

## 15.1 Error Levels

```php
<?php
// Fatal errors (script stops)
require 'nonexistent.php';
// Fatal error: require(): Failed opening required

// Warnings (script continues)
include 'maybe.php';     // Warning if not found

// Notices (minor issues)
echo $undefinedVariable;  // Notice: Undefined variable

// Parse errors (syntax)
// $x = "unclosed string  (Parse error)

// Deprecation warnings
$my_var = "old style";  // Deprecated in some contexts
```

---

## 15.2 Exceptions (try/catch)

```php
<?php
try {
    // Code that might throw an exception
    $file = fopen('config.json', 'r');
    if (!$file) {
        throw new RuntimeException('Cannot open config file');
    }
    $data = fread($file, filesize('config.json'));
    fclose($file);
    
    $config = json_decode($data, flags: JSON_THROW_ON_ERROR);
    
} catch (RuntimeException $e) {
    // Catch specific exception
    echo "File error: " . $e->getMessage();
    error_log($e->getMessage());
    
} catch (JsonException $e) {
    echo "JSON error: " . $e->getMessage();
    
} catch (Throwable $e) {
    // Catch any exception/error (Throwable is the base)
    echo "Unexpected error: " . $e->getMessage();
    
} finally {
    // Always executes (even if exception thrown or caught)
    if (isset($file) && is_resource($file)) {
        fclose($file);
    }
}
```

---

## 15.3 Custom Exceptions

```php
<?php
class ValidationException extends RuntimeException
{
    private array $errors;

    public function __construct(array $errors)
    {
        parent::__construct('Validation failed');
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

class AuthenticationException extends RuntimeException
{
    public function __construct(string $message = 'Authentication required')
    {
        parent::__construct($message, 401);
    }
}

class NotFoundException extends RuntimeException
{
    public function __construct(string $resource = 'Resource')
    {
        parent::__construct("{$resource} not found", 404);
    }
}

// Usage
if (empty($data['email'])) {
    throw new ValidationException(['email' => 'Email is required']);
}
```

---

## 15.4 Global Handlers

```php
<?php
// Custom error handler (converts PHP errors to exceptions)
set_error_handler(function (
    int $level,
    string $message,
    string $file = '',
    int $line = 0
): bool {
    if (error_reporting() & $level) {
        throw new ErrorException($message, 0, $level, $file, $line);
    }
    return true;
});

// Custom exception handler (for uncaught exceptions)
set_exception_handler(function (Throwable $e): void {
    $log = sprintf(
        "[%s] %s in %s:%d\n%s\n",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    
    error_log($log);
    
    if (php_sapi_name() === 'cli') {
        echo $log;
    } else {
        http_response_code(500);
        echo "An error occurred. Please try again later.";
    }
});
```

---

## 15.5 Exercises

1. Create custom exception classes for your application
2. Implement try/catch for file operations
3. Set up a global exception handler for CLI scripts
4. Convert PHP warnings to exceptions
5. Write a safe JSON parser that handles errors gracefully

---

## Further Reading

- **Doc:** [PHP Errors](https://www.php.net/manual/en/language.errors.php)
- **Doc:** [Exceptions](https://www.php.net/manual/en/language.exceptions.php)
- **Doc:** [Error Handling](https://www.php.net/manual/en/book.errorfunc.php)
