# Chapter 22: Authentication Systems

## Learning Objectives

- Implement password hashing and verification
- Build session-based authentication
- Implement JWT authentication
- Create middleware for protected routes

---

```mermaid
sequenceDiagram
    participant Client as Browser/Client
    participant App as PHP App
    participant DB as Database
    participant Token as JWT Token

    Note over Client,Token: Registration Flow
    Client->>App: POST /register (email, password)
    App->>App: password_hash(password, PASSWORD_BCRYPT)
    App->>DB: INSERT user with hashed password
    DB-->>App: User created
    App-->>Client: 201 Created

    Note over Client,Token: Login Flow
    Client->>App: POST /login (email, password)
    App->>DB: SELECT user by email
    DB-->>App: User row with hash
    App->>App: password_verify(password, hash)
    alt Valid password
        App->>Token: Generate JWT (HS256, payload, expiry)
        Token-->>App: eyJhbGciOi...
        App-->>Client: 200 + JWT token
    else Invalid password
        App-->>Client: 401 Unauthorized
    end

    Note over Client,Token: Authenticated Request
    Client->>App: GET /profile (Authorization: Bearer <token>)
    App->>App: Validate JWT signature + expiry
    alt Valid token
        App->>DB: SELECT user profile
        DB-->>App: Profile data
        App-->>Client: 200 + Profile JSON
    else Invalid/expired
        App-->>Client: 401 Unauthorized
    end
```

## 22.1 Password Hashing

```php
<?php
class PasswordService
{
    // Hash a password using bcrypt (default)
    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => 12,  // Higher cost = more secure but slower
        ]);
    }

    // Verify password against hash
    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    // Check if rehashing is needed
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    // Validate password strength
    public static function validateStrength(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain an uppercase letter';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain a lowercase letter';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain a number';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain a special character';
        }

        return $errors;
    }
}
```

---

## 22.2 JWT Authentication

```php
<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $secretKey;
    private string $algorithm = 'HS256';

    public function __construct()
    {
        $this->secretKey = $_ENV['JWT_SECRET'] ?? 'your-secret-key-change-in-production';
    }

    public function createToken(array $payload, int $ttl = 3600): string
    {
        $issuedAt = time();
        $payload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $issuedAt + $ttl,
            'jti' => bin2hex(random_bytes(16)),
        ]);

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function validateToken(string $token): object
    {
        try {
            return JWT::decode($token, new Key($this->secretKey, $this->algorithm));
        } catch (\Exception $e) {
            throw new AuthenticationException('Invalid or expired token');
        }
    }

    public function refreshToken(string $token, int $newTtl = 3600): string
    {
        $payload = $this->validateToken($token);
        return $this->createToken((array)$payload, $newTtl);
    }
}

// Auth Middleware
class AuthMiddleware
{
    public function __construct(private JwtService $jwt) {}

    public function handle(callable $next): void
    {
        $token = $this->extractToken();
        
        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'No authentication token provided']);
            exit;
        }

        try {
            $payload = $this->jwt->validateToken($token);
            $_REQUEST['auth_user'] = $payload;
            $next();
        } catch (AuthenticationException $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    private function extractToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s+(.+)$/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
```

---

## 22.3 Exercises

1. Implement password hashing with bcrypt and a cost factor of 12
2. Build session-based authentication with login/logout
3. Implement JWT authentication with token refresh
4. Create middleware that protects routes with authentication

---

## Further Reading

- **Doc:** [PHP password_hash](https://www.php.net/manual/en/function.password-hash.php)
- **Doc:** [Firebase JWT PHP](https://github.com/firebase/php-jwt)
- **Doc:** [JWT.io](https://jwt.io/)
