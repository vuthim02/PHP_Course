# Chapter 14: Forms and User Input

## Learning Objectives

- Handle GET and POST requests
- Validate and sanitize user input
- Prevent XSS attacks
- Build secure forms

---

## 14.1 GET vs POST

```php
<?php
// GET: Data in URL (query string)
// example.com/search?q=php&page=2
$query = $_GET['q'] ?? '';
$page = (int)($_GET['page'] ?? 1);

// POST: Data in request body
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
```

### Form Example

```php
<!-- form.php -->
<form method="POST" action="process.php">
    <label>Name:
        <input type="text" name="name" required minlength="2">
    </label>
    <label>Email:
        <input type="email" name="email" required>
    </label>
    <label>Message:
        <textarea name="message" required></textarea>
    </label>
    <button type="submit">Submit</button>
</form>
```

```php
<?php
// process.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$message = $_POST['message'] ?? '';

// Validate
$errors = [];
if (strlen(trim($name)) < 2) {
    $errors['name'] = 'Name must be at least 2 characters';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Invalid email address';
}
if (strlen(trim($message)) < 10) {
    $errors['message'] = 'Message must be at least 10 characters';
}

if (!empty($errors)) {
    // Show errors
    http_response_code(422);
    echo json_encode(['errors' => $errors]);
    exit;
}

// Process the valid data
// ...
```

---

## 14.2 Input Sanitization

```php
<?php
// Sanitize for HTML output
function h(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Sanitize for database (use prepared statements - never sanitize for SQL)
// Sanitize for email
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

// Sanitize for URL
$url = filter_var($_POST['url'], FILTER_SANITIZE_URL);

// Sanitize integers
$age = filter_var($_POST['age'], FILTER_SANITIZE_NUMBER_INT);

// Remove all tags
$clean = strip_tags($_POST['content'], '<p><a><br>');  // Allow some tags

// Trim whitespace
$input = trim($_POST['name']);
```

---

## 14.3 Input Validation

```php
<?php
class Validator {
    private array $errors = [];

    public function required(string $field, string $value): self {
        if (empty(trim($value))) {
            $this->errors[$field][] = 'This field is required';
        }
        return $this;
    }

    public function email(string $field, string $value): self {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = 'Invalid email address';
        }
        return $this;
    }

    public function minLength(string $field, string $value, int $min): self {
        if (strlen(trim($value)) < $min) {
            $this->errors[$field][] = "Minimum {$min} characters";
        }
        return $this;
    }

    public function maxLength(string $field, string $value, int $max): self {
        if (strlen($value) > $max) {
            $this->errors[$field][] = "Maximum {$max} characters";
        }
        return $this;
    }

    public function numeric(string $field, mixed $value): self {
        if (!is_numeric($value)) {
            $this->errors[$field][] = 'Must be a number';
        }
        return $this;
    }

    public function inArray(string $field, mixed $value, array $allowed): self {
        if (!in_array($value, $allowed, true)) {
            $this->errors[$field][] = 'Invalid value selected';
        }
        return $this;
    }

    public function passes(): bool {
        return empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }
}

// Usage
$validator = new Validator();
$validator
    ->required('name', $_POST['name'] ?? '')
    ->minLength('name', $_POST['name'] ?? '', 2)
    ->email('email', $_POST['email'] ?? '')
    ->numeric('age', $_POST['age'] ?? '');

if (!$validator->passes()) {
    http_response_code(422);
    echo json_encode(['errors' => $validator->errors()]);
    exit;
}
```

---

## 14.4 CSRF Protection

```php
<?php
session_start();

// Generate CSRF token
function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCsrfToken(string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}
```

```php
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    <!-- form fields -->
</form>
```

```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        http_response_code(419);
        exit('Invalid or expired CSRF token');
    }
}
```

---

## 14.5 Exercises

1. Build a contact form with validation
2. Implement CSRF protection
3. Sanitize output for HTML display
4. Validate email, phone, URL, and date inputs
5. Build a registration form with password confirmation

---

## Further Reading

- **Doc:** [$_GET](https://www.php.net/manual/en/reserved.variables.get.php)
- **Doc:** [$_POST](https://www.php.net/manual/en/reserved.variables.post.php)
- **Doc:** [Filter Functions](https://www.php.net/manual/en/ref.filter.php)
- **Doc:** [CSRF Protection](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)
