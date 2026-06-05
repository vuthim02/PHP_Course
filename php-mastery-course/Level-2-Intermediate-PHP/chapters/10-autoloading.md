# Chapter 10: Autoloading

## Learning Objectives

- Understand autoloading and why it's essential
- Implement PSR-4 autoloading with Composer
- Build a custom autoloader
- Organize code with namespaces

---

## 10.1 What is Autoloading?

Autoloading automatically loads PHP class files when they're first used, eliminating the need for manual `require` or `include` statements.

```php
<?php
// Without autoloading (manual requires)
require_once __DIR__ . '/src/Controllers/UserController.php';
require_once __DIR__ . '/src/Models/User.php';
require_once __DIR__ . '/src/Repositories/UserRepository.php';
require_once __DIR__ . '/src/Services/AuthService.php';

// With autoloading (automatic!)
$controller = new App\Controllers\UserController();
// The file is loaded automatically when UserController is first used
```

---

## 10.2 PSR-4 Autoloading

PSR-4 maps namespaces to directory structures:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

This means:
- `App\Controllers\UserController` → `src/Controllers/UserController.php`
- `App\Models\User` → `src/Models/User.php`
- `App\Services\Auth\AuthService` → `src/Services/Auth/AuthService.php`

```bash
# Generate autoloader
composer dump-autoload

# Production-optimized
composer dump-autoload -o
```

---

## 10.3 Custom Autoloader

```php
<?php
spl_autoload_register(function (string $class): void {
    // Convert namespace to file path
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';
    
    if (str_starts_with($class, $prefix)) {
        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        
        if (file_exists($file)) {
            require $file;
        }
    }
});
```

---

## 10.4 Exercises

1. Set up PSR-4 autoloading with Composer for your project
2. Create a custom autoloader function
3. Add a test namespace with automatic loading
4. Run `composer dump-autoload -o` and understand the optimization
5. Debug an autoloading error (class not found)

---

## Further Reading

- **Doc:** [Autoloading](https://www.php.net/manual/en/language.oop5.autoload.php)
- **PSR:** [PSR-4 Autoloading](https://www.php-fig.org/psr/psr-4/)
