# Chapter 12: Tools of the Trade

## Learning Objectives

By the end of this chapter you will:
- Master the essential tools for PHP development
- Understand version control with Git
- Use Composer effectively
- Know how to use debugging and profiling tools
- Be productive with PHP development tools

---

## 12.1 Version Control (Git)

### Essential Git Workflow

```bash
# Starting a project
git init
git add .
git commit -m "Initial commit"

# Working with branches
git checkout -b feature/user-authentication
# ... make changes ...
git add src/Controllers/AuthController.php
git commit -m "Add login and registration"
git checkout main
git merge feature/user-authentication

# Collaboration
git clone https://github.com/user/project.git
git pull --rebase  # Rebase instead of merge for clean history
git push origin main

# Undoing things
git restore file.php              # Discard unstaged changes
git restore --staged file.php     # Unstage
git reset --soft HEAD~1           # Undo last commit (keep changes)
git reset --hard HEAD~1           # Undo last commit (destroy changes)
git revert HEAD                   # Safe undo (creates new commit)
```

### .gitignore for PHP

```gitignore
# PHP
vendor/
composer.lock

# Environment
.env
.env.local
.env.*.local

# IDE
.vscode/
.idea/
*.swp
*.swo

# OS
.DS_Store
Thumbs.db

# Logs
/storage/logs/*.log

# Cache
/storage/cache/*
/storage/framework/cache/data/*

# Uploads
/storage/uploads/*
public/storage/

# Build
/node_modules
/public/build
/public/hot

# Tests
/.phpunit.result.cache
```

### Git Commit Convention

```text
type(scope): description

Types:
feat:     New feature
fix:      Bug fix
docs:     Documentation
style:    Formatting (no code change)
refactor: Code restructuring
test:     Adding tests
chore:    Maintenance, dependencies

Examples:
feat(auth): add OAuth2 login provider
fix(db): resolve N+1 query in user listing
docs(api): update endpoint documentation
refactor(controller): extract validation logic
test(user): add unit tests for UserRepository
```

---

## 12.2 Composer

### Basic Usage

```bash
# Create new project
composer init

# Install a package
composer require laravel/framework

# Install dev dependency
composer require --dev phpunit/phpunit

# Install all dependencies
composer install

# Update dependencies
composer update

# Update single package
composer update phpunit/phpunit

# Show outdated packages
composer outdated

# Autoload optimization
composer dump-autoload -o
```

### composer.json

```json
{
    "name": "vendor/my-app",
    "description": "Professional PHP Application",
    "type": "project",
    "require": {
        "php": "^8.2",
        "slim/slim": "^4.0",
        "vlucas/phpdotenv": "^5.5",
        "monolog/monolog": "^3.0",
        "illuminate/database": "^10.0",
        "ramsey/uuid": "^4.7"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "mockery/mockery": "^1.6",
        "phpstan/phpstan": "^1.10",
        "squizlabs/php_codesniffer": "^3.7",
        "friendsofphp/php-cs-fixer": "^3.0"
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
        "test": "phpunit",
        "lint": "phpcs src/",
        "fix": "php-cs-fixer fix src/",
        "analyse": "phpstan analyse src/ --level=max"
    },
    "config": {
        "optimize-autoloader": true,
        "sort-packages": true
    }
}
```

### Creating a Package

```php
<?php
// src/Helpers/StringHelper.php
namespace App\Helpers;

class StringHelper
{
    public static function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        
        return $text ?: 'n-a';
    }
}
```

---

## 12.3 Debugging Tools

### Xdebug Step Debugging

```php
<?php
// Set breakpoint - execution stops here
$user = getUser($id);

xdebug_break();  // Force breakpoint

// Variable inspection
var_dump($user);
xdebug_var_dump($user);  // More detailed

// Stack trace
debug_print_backtrace();
```

### Error Handling

```php
<?php
// Custom error handler
set_error_handler(function (int $level, string $message, string $file, int $line) {
    if (!(error_reporting() & $level)) {
        return false; // Let PHP handle it
    }
    
    throw new ErrorException($message, 0, $level, $file, $line);
});

// Custom exception handler
set_exception_handler(function (Throwable $e) {
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
        echo '<h1>500 Internal Server Error</h1>';
        if (getenv('APP_DEBUG') === 'true') {
            echo '<pre>' . htmlspecialchars($log) . '</pre>';
        }
    }
});
```

### Profiling with Xdebug

```php
<?php
// Enable profiler
xdebug_start_trace('/tmp/trace');  // Function call trace
xdebug_start_profiling();          // CPU profiling

// ... your code ...

xdebug_stop_trace();
xdebug_stop_profiling();

// Results saved to:
// /tmp/trace.1234567890.xt  (trace file)
// /tmp/cachegrind.out.1234567890  (profile data)

// View with:
// qcachegrind cachegrind.out.1234567890  (Linux/KDE)
// or Webgrind (PHP web tool)
```

---

## 12.4 Static Analysis

### PHPStan

```php
<?php
// phpstan.neon
parameters:
    level: max
    paths:
        - src/
    excludePaths:
        - src/Migrations/
    checkMissingIterableValueType: true
    checkGenericClassInNonGenericObjectType: true

// Run: vendor/bin/phpstan analyse
```

### PHP CodeSniffer

```xml
<!-- phpcs.xml -->
<?xml version="1.0"?>
<ruleset name="MyApp">
    <description>PSR-12 Coding Standards</description>
    
    <file>src</file>
    <file>tests</file>
    
    <rule ref="PSR12"/>
    <rule ref="Generic.Arrays.DisallowLongArraySyntax"/>
    <rule ref="Generic.CodeAnalysis.UnusedFunctionParameter"/>
    
    <rule ref="Generic.Commenting.Todo"/>
    
    <!-- Exclude -->
    <exclude-pattern>*/vendor/*</exclude-pattern>
    <exclude-pattern>*/migrations/*</exclude-pattern>
</ruleset>
```

---

## 12.5 Essential Tools List

| Tool | Purpose | Install |
|------|---------|---------|
| Git | Version control | `apt install git` |
| Composer | PHP dependencies | See above |
| PHPUnit | Testing | `composer require --dev phpunit/phpunit` |
| PHPStan | Static analysis | `composer require --dev phpstan/phpstan` |
| PHP-CS-Fixer | Code formatting | `composer require --dev friendsofphp/php-cs-fixer` |
| Xdebug | Debugging/profiling | `apt install php-xdebug` |
| Valet/Herd | Local dev | `composer global require laravel/valet` |
| TablePlus | DB GUI | Download from website |
| Postman | API testing | Download from website |
| Docker | Containers | `apt install docker.io` |
| Tmux | Terminal multiplexer | `apt install tmux` |
| Fish/Zsh | Shell | `apt install fish` |
| Fzf | Fuzzy finder | `apt install fzf` |
| Ripgrep | Fast grep | `apt install ripgrep` |

---

## 12.6 Interview Questions

1. "What is Composer and how does it work?"
2. "Explain the Git workflow you use in production."
3. "How do you debug a PHP application?"
4. "What is PHPStan and why would you use it?"
5. "Explain PSR-4 autoloading."
6. "What tools do you use for PHP development?"

---

## Further Reading

- **Documentation:** [Git SCM](https://git-scm.com/doc)
- **Documentation:** [Composer](https://getcomposer.org/doc/)
- **Documentation:** [PHPStan](https://phpstan.org/user-guide/getting-started)
- **Resource:** [PHP The Right Way](https://phptherightway.com/)

---

*End of Chapter 12. Proceed to Chapter 13: Knowledge Verification.*
