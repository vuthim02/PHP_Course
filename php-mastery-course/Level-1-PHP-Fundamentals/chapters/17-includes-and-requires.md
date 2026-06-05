# Chapter 17: Includes and Requires

## Learning Objectives

- Organize code across multiple files
- Use include, require, include_once, require_once
- Understand include paths
- Build a simple autoloader

---

## 17.1 include vs require

```php
<?php
// include - emits warning if file not found, continues
include 'header.php';

// require - emits fatal error if file not found, stops
require 'config.php';

// *_once versions - only includes file once
include_once 'functions.php';
require_once 'vendor/autoload.php';
```

### When to Use Each

```php
<?php
// require_once: Essential files (config, database)
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';

// include: Optional presentation files
include 'templates/header.php';
include 'templates/sidebar.php';

// require: Critical files
require 'vendor/autoload.php';
```

---

## 17.2 File Organization

```php
<?php
// project structure
// project/
// ├── public/
// │   └── index.php          # Entry point
// ├── src/
// │   ├── Config/
// │   │   └── database.php
// │   ├── Controllers/
// │   │   └── UserController.php
// │   └── Helpers/
// │       └── functions.php
// ├── templates/
// │   ├── layout.php
// │   └── user/
// │       └── profile.php
// ├── vendor/
// └── index.php

// index.php (entry point)
require_once __DIR__ . '/../src/Config/database.php';
require_once __DIR__ . '/../src/Helpers/functions.php';

$db = Database::connect();
$users = $db->query('SELECT * FROM users');

include __DIR__ . '/../templates/layout.php';
```

---

## 17.3 Exercises

1. Create a project with config, functions, and controllers directories
2. Use require_once for essential files in the correct order
3. Build a simple autoloader that maps classes to files
4. Use __DIR__ for reliable path resolution

---

## Further Reading

- **Doc:** [include](https://www.php.net/manual/en/function.include.php)
- **Doc:** [Autoloading](https://www.php.net/manual/en/language.oop5.autoload.php)
