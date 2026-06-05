# Chapter 14: Composer and Autoloading

## Learning Objectives

- Install and configure Composer
- Understand PSR-4 autoloading
- Manage project dependencies
- Create application scripts

---

## 14.1 Getting Started

```json
{
    "name": "acme/my-application",
    "description": "Professional PHP application",
    "type": "project",
    "require": {
        "php": "^8.2",
        "monolog/monolog": "^3.0",
        "vlucas/phpdotenv": "^5.5",
        "illuminate/database": "^10.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "phpstan/phpstan": "^1.10",
        "squizlabs/php_codesniffer": "^3.7"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "start": "php -S localhost:8000 -t public",
        "test": "phpunit",
        "analyse": "phpstan analyse --level=max",
        "check": ["@test", "@analyse"],
        "cs-fix": "phpcbf"
    },
    "config": {
        "sort-packages": true,
        "optimize-autoloader": true
    }
}
```

---

## 14.2 PSR-4 Autoloading

```php
<?php
// With PSR-4, namespace maps to directory structure:
// App\Models\User          → src/Models/User.php
// App\Http\Controllers\HomeController → src/Http/Controllers/HomeController.php
// App\Services\PaymentService         → src/Services/PaymentService.php
// Tests\Unit\UserTest                 → tests/Unit/UserTest.php

// File: src/Models/User.php
namespace App\Models;

class User
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email
    ) {}
}

// File: src/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;

class UserController
{
    public function __construct(private UserService $userService) {}

    public function show(int $id): User
    {
        return $this->userService->findOrFail($id);
    }
}

// File: public/index.php (entry point)
require_once __DIR__ . '/../vendor/autoload.php';

use App\Http\Controllers\UserController;
use App\Services\UserService;

$controller = new UserController(new UserService());
$user = $controller->show(42);
```

---

## 14.3 Exercises

1. Create a new project with Composer and configure PSR-4 autoloading
2. Install Monolog and use it to log messages
3. Add PHPStan at max level and fix all reported issues
4. Create a custom script in composer.json that runs tests and analysis

---

## Further Reading

- **Doc:** [Composer Documentation](https://getcomposer.org/doc/)
- **Doc:** [PSR-4 Autoloading](https://www.php-fig.org/psr/psr-4/)
