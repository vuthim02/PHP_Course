# Chapter 4: Constants and Magic Constants

## Learning Objectives

- Define and use constants with define() and const
- Understand PHP magic constants
- Use class constants effectively
- Differentiate between constants and variables

---

## 4.1 Defining Constants

```php
<?php
// define() - defines at runtime
define('SITE_NAME', 'My PHP App');
define('API_VERSION', '2.0');
define('MAX_RETRIES', 3);
define('SUPPORTED_LOCALES', ['en', 'fr', 'es', 'de']);  // PHP 7+

// const - defines at compile time
const DATABASE_HOST = 'localhost';
const DATABASE_PORT = 3306;
const DEBUG_MODE = true;

// Naming conventions
define('MAX_LOGIN_ATTEMPTS', 5);  // UPPER_SNAKE_CASE
define('DB_CONFIG', [...]);       // Descriptive names

// Constants are case-sensitive by default
echo SITE_NAME;       // 'My PHP App'
echo Site_Name;       // Warning: undefined constant (case matters!)
```

### define() vs const

| Aspect | define() | const |
|--------|----------|-------|
| When defined | Runtime | Compile time |
| Where usable | Anywhere | Must be at top-level scope |
| Expressions | Can use expressions | Must be constant expression |
| Arrays | Supported (PHP 7+) | Supported |
| Conditional | Can define in if blocks | Cannot use in if blocks |
| Objects | Not supported | Not supported |

---

## 4.2 Magic Constants

```php
<?php
// These change depending on where they're used

echo __LINE__;         // Current line number in file
echo __FILE__;         // Full path and filename
echo __DIR__;          // Directory of the file
echo __FUNCTION__;     // Current function name
echo __CLASS__;        // Current class name
echo __TRAIT__;        // Current trait name
echo __METHOD__;       // Class method name (Class::method)
echo __NAMESPACE__;    // Current namespace

// Practical usage
require_once __DIR__ . '/../vendor/autoload.php';

function logError(string $message): void {
    $log = sprintf(
        "[%s] %s in %s:%d\n",
        date('Y-m-d H:i:s'),
        $message,
        __FILE__,
        __LINE__
    );
    error_log($log);
}
```

---

## 4.3 Class Constants

```php
<?php
class PaymentGateway {
    const VERSION = '2.0';
    public const MODE_SANDBOX = 'sandbox';
    public const MODE_PRODUCTION = 'production';
    private const API_KEY = 'sk_test_...';  // PHP 7.1+ visibility modifiers
    
    public static function getApiUrl(string $mode): string {
        return match ($mode) {
            self::MODE_SANDBOX => 'https://sandbox.api.payment.com',
            self::MODE_PRODUCTION => 'https://api.payment.com',
            default => throw new InvalidArgumentException('Invalid mode'),
        };
    }
}

// Access from outside
echo PaymentGateway::VERSION;       // '2.0'
echo PaymentGateway::MODE_SANDBOX;  // 'sandbox'
```

---

## 4.4 Predefined Constants

```php
<?php
// PHP version
echo PHP_VERSION;       // '8.3.0'
echo PHP_MAJOR_VERSION; // 8
echo PHP_MINOR_VERSION; // 3

// OS
echo PHP_OS;            // 'Linux', 'WINNT', 'Darwin'
echo PHP_INT_MAX;       // 9223372036854775807 (64-bit)
echo PHP_INT_MIN;       // -9223372036854775808
echo PHP_INT_SIZE;      // 8 (bytes)

// Directory separators
echo DIRECTORY_SEPARATOR;  // '/' on Linux, '\' on Windows
echo PATH_SEPARATOR;       // ':' on Linux, ';' on Windows

// Line endings
echo PHP_EOL;             // "\n" on Linux, "\r\n" on Windows
```

---

## 4.5 Exercises

1. Define 5 constants for your application configuration
2. Use __FILE__ and __DIR__ to include files relative to current file
3. Create a class with public, protected, and private constants
4. Use PHP_EOL for cross-platform line endings
5. Compare define() vs const() in different scopes

---

## Further Reading

- **Doc:** [PHP Constants](https://www.php.net/manual/en/language.constants.php)
- **Doc:** [Magic Constants](https://www.php.net/manual/en/language.constants.magic.php)
- **Doc:** [Predefined Constants](https://www.php.net/manual/en/reserved.constants.php)
