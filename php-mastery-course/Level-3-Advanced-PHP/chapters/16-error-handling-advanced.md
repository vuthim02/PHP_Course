# Chapter 16: Advanced Error Handling

## Learning Objectives

- Understand Throwable interface hierarchy
- Implement global exception handlers
- Convert errors to exceptions
- Design robust error handling strategies

---

## 16.1 Throwable Interface

```php
<?php
// PHP 7+ Throwable hierarchy:
// Throwable
//   ├── Error
//   │   ├── TypeError
//   │   ├── ParseError
//   │   ├── ArithmeticError
//   │   ├── DivisionByZeroError
//   │   └── AssertionError
//   └── Exception
//       ├── RuntimeException
//       ├── LogicException
//       └── ...

function processInput(mixed $value): string
{
    try {
        if (is_int($value)) {
            return (string) $value;
        }
        if (is_string($value)) {
            return $value;
        }
        throw new \InvalidArgumentException('Expected int or string');
    } catch (\TypeError $e) {
        // Catch type errors
        return 'Type error: ' . $e->getMessage();
    } catch (\InvalidArgumentException $e) {
        // Catch logic exceptions
        return 'Invalid: ' . $e->getMessage();
    } catch (\Throwable $e) {
        // Catch ANY error or exception
        return 'Unexpected: ' . $e->getMessage();
    }
}
```

---

## 16.2 Global Exception Handler

```php
<?php
class GlobalErrorHandler
{
    public static function register(): void
    {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleError(
        int $severity,
        string $message,
        string $file,
        int $line
    ): bool {
        // Convert error to ErrorException
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    public static function handleException(\Throwable $e): void
    {
        $logEntry = sprintf(
            "[%s] %s in %s:%d\nStack trace:\n%s\n",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        // Log to file
        error_log($logEntry, 3, '/var/log/app_errors.log');

        // Return JSON response for API
        if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api')) {
            http_response_code($e->getCode() ?: 500);
            header('Content-Type: application/json');
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
        } else {
            // Show user-friendly error page
            http_response_code(500);
            echo '<h1>Internal Server Error</h1>';
            echo '<p>Something went wrong. Our team has been notified.</p>';
        }
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR])) {
            self::handleException(new \ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            ));
        }
    }
}

// In bootstrap
GlobalErrorHandler::register();
```

---

## 16.3 Exercises

1. Implement a global error handler that logs errors to a file
2. Convert PHP warnings to exceptions
3. Create a shutdown handler for fatal errors
4. Build an API error handler that returns appropriate HTTP status codes

---

## Further Reading

- **Doc:** [PHP Error Handling](https://www.php.net/manual/en/book.errorfunc.php)
- **Doc:** [PHP Exception Handling](https://www.php.net/manual/en/language.exceptions.php)
