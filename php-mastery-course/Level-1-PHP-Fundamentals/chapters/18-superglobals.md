# Chapter 18: Superglobals

## Learning Objectives

- Master PHP superglobal arrays
- Use $_GET, $_POST, $_SERVER, $_SESSION, $_COOKIE
- Access environment variables
- Handle request data securely

---

## 18.1 Superglobal Reference

```php
<?php
// $_GET - URL query parameters
// URL: example.com?page=2&sort=name
$page = $_GET['page'];    // '2'
$sort = $_GET['sort'];    // 'name'

// $_POST - form data (POST requests)
$name = $_POST['name'];
$email = $_POST['email'];

// $_REQUEST - combines GET and POST (avoid using)
// Security risk: cannot tell if data came from GET or POST

// $_SERVER - server/environment info
$_SERVER['REQUEST_METHOD'];     // 'GET', 'POST', etc.
$_SERVER['REQUEST_URI'];        // '/users?page=2'
$_SERVER['HTTP_HOST'];          // 'example.com'
$_SERVER['HTTP_USER_AGENT'];    // Browser string
$_SERVER['REMOTE_ADDR'];        // Client IP
$_SERVER['SERVER_NAME'];        // Server hostname
$_SERVER['SERVER_PORT'];        // 80 or 443

// $_FILES - file uploads
$_FILES['avatar']['name'];      // Original filename
$_FILES['avatar']['type'];      // MIME type
$_FILES['avatar']['tmp_name'];  // Temp file path
$_FILES['avatar']['error'];     // Error code
$_FILES['avatar']['size'];      // File size in bytes

// $_SESSION - session data
session_start();
$_SESSION['user_id'] = 42;
$_SESSION['cart'] = ['item_1', 'item_2'];

// $_COOKIE - cookie data
echo $_COOKIE['theme'];         // 'dark'

// $_ENV - environment variables
$_ENV['DB_HOST'] = 'localhost';

// $GLOBALS - all global variables
$GLOBALS['config'] = ['debug' => true];
```

---

## 18.2 Environment Variables

```php
<?php
// Set in .env file or shell
// DB_HOST=localhost
// DB_NAME=myapp
// DB_USER=root
// DB_PASS=secret

// Using getenv()
$host = getenv('DB_HOST') ?: 'localhost';
$name = getenv('DB_NAME') ?: 'myapp';

// Using $_ENV
$host = $_ENV['DB_HOST'] ?? 'localhost';

// Using dotenv library (recommended)
// composer require vlucas/phpdotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
```

---

## 18.3 Exercises

1. Display all server variables and their values
2. Build a request logger that logs IP, method, URI, user agent
3. Access environment variables using getenv()
4. Implement a session-based page visit counter
5. Parse query string parameters from $_SERVER['QUERY_STRING']

---

## Further Reading

- **Doc:** [Superglobals](https://www.php.net/manual/en/language.variables.superglobals.php)
- **Doc:** [$_SERVER](https://www.php.net/manual/en/reserved.variables.server.php)
- **Doc:** [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv)
