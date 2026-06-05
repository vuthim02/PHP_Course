# API Security — Hardening Guide

## 1. Authentication & Authorization

```php
// JWT middleware
function authMiddleware(): array
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
        errorResponse('Missing authorization header', 401);
    }

    $payload = validateJWT($matches[1], $_ENV['JWT_SECRET']);
    if (!$payload) {
        errorResponse('Invalid or expired token', 401);
    }

    return $payload;  // ['user_id' => 5, 'role' => 'admin', ...]
}

// Role-based access control
function requireRole(string ...$roles): void
{
    $user = $GLOBALS['currentUser'];
    if (!in_array($user['role'], $roles, true)) {
        errorResponse('Insufficient permissions', 403);
    }
}

// Scope-based (OAuth2)
function requireScope(string $scope): void
{
    $tokenScopes = $GLOBALS['currentToken']['scopes'] ?? [];
    if (!in_array($scope, $tokenScopes, true)) {
        errorResponse('Missing required scope', 403);
    }
}
```

## 2. Input Validation & Sanitization

```php
// Never trust user input
function sanitizeInput(mixed $input): mixed
{
    if (is_string($input)) {
        // Strip null bytes
        $input = str_replace("\0", '', $input);
        // Normalize line endings
        $input = str_replace(["\r\n", "\r"], "\n", $input);
        return $input;
    }
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return $input;
}

// Whitelist approach — reject unknown fields
function filterAllowedFields(array $input, array $allowed): array
{
    return array_intersect_key($input, array_flip($allowed));
}

// Numeric IDs
function validateId(mixed $id): int
{
    $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        errorResponse('Invalid ID', 400);
    }
    return $id;
}

// UUID validation
function validateUuid(string $uuid): string
{
    if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid)) {
        errorResponse('Invalid UUID', 400);
    }
    return $uuid;
}
```

## 3. SQL Injection Prevention

```php
// ✅ ALWAYS use prepared statements
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);

// ❌ NEVER interpolate values into SQL
$stmt = $pdo->query("SELECT * FROM users WHERE id = $id");  // DANGER

// Dynamic ORDER BY — must whitelist
function sanitizeOrderBy(string $column, array $allowed): string
{
    if (!in_array($column, $allowed, true)) {
        return $allowed[0];  // Default to safe column
    }
    return $column;
}

// Dynamic table names — whitelist or use enum
enum TableName: string
{
    case Users = 'users';
    case Posts = 'posts';
    case Comments = 'comments';
}
```

## 4. XSS Prevention

```php
// Always escape HTML output
function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
}

// Content-Security-Policy header
function setCspHeader(): void
{
    header(
        "Content-Security-Policy: default-src 'self'; " .
        "script-src 'self'; " .
        "style-src 'self' 'unsafe-inline'; " .
        "img-src 'self' data:; " .
        "font-src 'self'; " .
        "frame-ancestors 'none';"
    );
}

// For APIs returning JSON — Content-Type prevents XSS
header('Content-Type: application/json; charset=utf-8');
```

## 5. CSRF Prevention (for session-based auth)

```php
// Generate CSRF token
function generateCsrfToken(): string
{
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

// Validate CSRF token
function validateCsrfToken(string $token): void
{
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        errorResponse('Invalid CSRF token', 403);
    }
}

// For APIs: use SameSite cookies
header('Set-Cookie: session_id=...; HttpOnly; Secure; SameSite=Strict; Path=/');
```

## 6. Rate Limiting

```php
// Per-IP rate limiting with Redis
function checkRateLimit(string $ip, string $endpoint, int $limit = 60, int $window = 60): void
{
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);

    $key = "ratelimit:$ip:$endpoint";

    $current = $redis->get($key);
    if ($current === false) {
        $redis->setex($key, $window, 1);
        return;
    }

    if ((int) $current >= $limit) {
        $ttl = $redis->ttl($key);
        header('Retry-After: ' . $ttl);
        errorResponse('Too many requests', 429);
    }

    $redis->incr($key);
}
```

## 7. HTTP Security Headers

```php
function setSecurityHeaders(): void
{
    // Prevent MIME-type sniffing
    header('X-Content-Type-Options: nosniff');

    // Enable XSS filter in older browsers
    header('X-XSS-Protection: 1; mode=block');

    // Prevent clickjacking
    header('X-Frame-Options: DENY');

    // HSTS (enforce HTTPS)
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');

    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Permissions policy
    header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

    // Content Security Policy
    header("Content-Security-Policy: default-src 'self'");

    // Remove server info
    header_remove('X-Powered-By');
}
```

## 8. Input Size Limits

```php
// Limit request body size
function checkBodySize(int $maxBytes = 1048576): void
{
    $size = (int) $_SERVER['CONTENT_LENGTH'] ?? 0;
    if ($size > $maxBytes) {
        errorResponse('Request entity too large', 413);
    }
}

// Limit string inputs
function truncateString(string $value, int $maxLength): string
{
    return mb_substr($value, 0, $maxLength);
}

// Limit array sizes
function limitArray(array $data, int $maxItems): array
{
    return array_slice($data, 0, $maxItems);
}
```

## 9. Secure Password Handling

```php
// Hashing (NEVER use md5/sha1 for passwords)
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Verification
if (!password_hash_verify($password, $hash)) {
    errorResponse('Invalid credentials', 401);
}

// Check if rehashing needed
if (password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12])) {
    $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    // Update stored hash
}
```

## 10. File Upload Security

```php
function validateFileUpload(array $file, array $allowedTypes, int $maxSize): array
{
    // Check upload error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed with error ' . $file['error']);
    }

    // Check file size
    if ($file['size'] > $maxSize) {
        throw new RuntimeException('File too large');
    }

    // Check MIME type (validate against actual content, not just extension)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowedTypes, true)) {
        throw new RuntimeException('Invalid file type');
    }

    // Generate safe filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safeName = bin2hex(random_bytes(16)) . '.' . $extension;

    // Store outside web root or use access control
    $destination = __DIR__ . '/../uploads/' . $safeName;
    move_uploaded_file($file['tmp_name'], $destination);

    return [
        'filename' => $safeName,
        'original' => $file['name'],
        'mime' => $mime,
        'size' => $file['size'],
        'path' => $destination,
    ];
}
```

## 11. Environment & Secrets

```php
// NEVER hardcode secrets
// ❌ BAD
define('DB_PASSWORD', 'supersecret123');

// ✅ GOOD — use environment variables
$dbPassword = $_ENV['DB_PASSWORD'] ?? throw new RuntimeException('Missing DB_PASSWORD');
$jwtSecret = $_ENV['JWT_SECRET'] ?? throw new RuntimeException('Missing JWT_SECRET');

// Validate .env file is NOT in web root
// In nginx/apache: deny access to .env
```

## 12. Logging (without leaking secrets)

```php
function safeLog(string $message, array $context = []): void
{
    // Redact sensitive fields
    $sensitiveKeys = ['password', 'token', 'secret', 'authorization', 'credit_card'];
    foreach ($context as $key => $value) {
        if (in_array(strtolower($key), $sensitiveKeys, true)) {
            $context[$key] = '[REDACTED]';
        }
    }

    error_log("$message " . json_encode($context));
}
```

## Security Checklist

- [ ] All inputs validated and sanitized
- [ ] All SQL queries use prepared statements
- [ ] Passwords hashed with `password_hash()` (bcrypt/argon2)
- [ ] JWT tokens have short expiry + refresh tokens
- [ ] `HttpOnly` + `Secure` + `SameSite` cookies
- [ ] CORS configured with explicit origins (not `*` for credentials)
- [ ] Rate limiting on auth endpoints
- [ ] Security headers set on all responses
- [ ] File uploads validated by content, not extension
- [ ] Error messages don't leak implementation details
- [ ] HTTPS enforced (HSTS)
- [ ] `.env` file protected from web access
- [ ] Dependencies updated regularly (`composer audit`)
