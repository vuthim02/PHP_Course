# REST API Design & Implementation — Quick Reference

## HTTP Methods & Status Codes

### Standard Endpoints

| Method | Path | Action | Status |
|--------|------|--------|--------|
| GET | `/api/users` | List all users | 200 |
| GET | `/api/users/{id}` | Get one user | 200 / 404 |
| POST | `/api/users` | Create user | 201 / 422 |
| PUT | `/api/users/{id}` | Replace user | 200 / 404 / 422 |
| PATCH | `/api/users/{id}` | Partial update | 200 / 404 / 422 |
| DELETE | `/api/users/{id}` | Delete user | 204 / 404 |

### Status Code Reference

| Code | Meaning | When |
|------|---------|------|
| 200 | OK | Successful GET, PUT, PATCH |
| 201 | Created | Successful POST |
| 204 | No Content | Successful DELETE |
| 400 | Bad Request | Malformed syntax, missing fields |
| 401 | Unauthorized | Missing/invalid authentication |
| 403 | Forbidden | Authenticated but not allowed |
| 404 | Not Found | Resource doesn't exist |
| 409 | Conflict | Duplicate, version conflict |
| 422 | Unprocessable Entity | Validation errors |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Unexpected server failure |

## Response Format (JSON:API Convention)

```json
// Success — single resource
{
    "data": {
        "id": 1,
        "type": "users",
        "attributes": {
            "name": "Alice",
            "email": "alice@example.com"
        }
    }
}

// Success — collection
{
    "data": [
        { "id": 1, "type": "users", "attributes": { "name": "Alice" } },
        { "id": 2, "type": "users", "attributes": { "name": "Bob" } }
    ],
    "meta": {
        "total": 100,
        "page": 1,
        "per_page": 20
    }
}

// Error
{
    "error": {
        "code": 422,
        "message": "Validation failed",
        "errors": [
            { "field": "email", "message": "Email is required" },
            { "field": "email", "message": "Email must be valid" }
        ]
    }
}
```

## Basic Router Implementation

```php
// Simple router
class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function put(string $path, callable $handler): void
    {
        $this->routes['PUT'][$path] = $handler;
    }

    public function delete(string $path, callable $handler): void
    {
        $this->routes['DELETE'][$path] = $handler;
    }

    public function resolve(string $method, string $uri): mixed
    {
        // Strip query string
        $path = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            // Convert route pattern to regex
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return $handler($params);
            }
        }

        http_response_code(404);
        return json_encode(['error' => 'Not found']);
    }
}

// Usage
$router = new Router();

$router->get('/api/users', function () {
    return json_encode(['data' => $userRepository->findAll()]);
});

$router->get('/api/users/{id}', function (array $params) {
    $user = $userRepository->find((int) $params['id']);
    if (!$user) {
        http_response_code(404);
        return json_encode(['error' => 'User not found']);
    }
    return json_encode(['data' => $user]);
});
```

## JSON Request/Response Helpers

```php
function jsonResponse(mixed $data, int $status = 200, array $headers = []): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    foreach ($headers as $key => $value) {
        header("$key: $value");
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

function getJsonBody(): array
{
    $raw = file_get_contents('php://input');
    return json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
}

function errorResponse(string $message, int $status, array $errors = []): void
{
    $body = ['error' => ['code' => $status, 'message' => $message]];
    if ($errors) {
        $body['error']['errors'] = $errors;
    }
    jsonResponse($body, $status);
}
```

## Validation

```php
class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label = null): self
    {
        if (empty($this->data[$field])) {
            $this->errors[] = [
                'field' => $field,
                'message' => ($label ?? $field) . ' is required',
            ];
        }
        return $this;
    }

    public function email(string $field): self
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = [
                'field' => $field,
                'message' => 'Must be a valid email',
            ];
        }
        return $this;
    }

    public function min(string $field, int $length): self
    {
        if (!empty($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[] = [
                'field' => $field,
                'message' => "Must be at least $length characters",
            ];
        }
        return $this;
    }

    public function max(string $field, int $length): self
    {
        if (!empty($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[] = [
                'field' => $field,
                'message' => "Must be at most $length characters",
            ];
        }
        return $this;
    }

    public function inList(string $field, array $allowed): self
    {
        if (!empty($this->data[$field]) && !in_array($this->data[$field], $allowed, true)) {
            $this->errors[] = [
                'field' => $field,
                'message' => 'Must be one of: ' . implode(', ', $allowed),
            ];
        }
        return $this;
    }

    public function validate(): ?array
    {
        return $this->errors ?: null;
    }
}

// Usage
$validator = new Validator(getJsonBody());
$errors = $validator
    ->required('email')
    ->email('email')
    ->required('password')
    ->min('password', 8)
    ->validate();

if ($errors) {
    errorResponse('Validation failed', 422, $errors);
}
```

## Pagination

```php
function paginatedResponse(
    PDO $pdo,
    string $baseQuery,
    int $page = 1,
    int $perPage = 20
): array {
    $page = max(1, $page);
    $perPage = min(100, max(1, $perPage));
    $offset = ($page - 1) * $perPage;

    // Get total count
    $countQuery = "SELECT COUNT(*) FROM ($baseQuery) AS count";
    $total = (int) $pdo->query($countQuery)->fetchColumn();

    // Get page data
    $dataQuery = "$baseQuery LIMIT $perPage OFFSET $offset";
    $data = $pdo->query($dataQuery)->fetchAll(PDO::FETCH_ASSOC);

    return [
        'data' => $data,
        'meta' => [
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ],
        'links' => [
            'first' => "/api/users?page=1&per_page=$perPage",
            'prev' => $page > 1 ? "/api/users?page=" . ($page - 1) . "&per_page=$perPage" : null,
            'next' => $page < ceil($total / $perPage) ? "/api/users?page=" . ($page + 1) . "&per_page=$perPage" : null,
            'last' => "/api/users?page=" . ceil($total / $perPage) . "&per_page=$perPage",
        ],
    ];
}
```

## CORS Headers

```php
function setCorsHeaders(): void
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Max-Age: 86400');

    // Handle preflight
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}
```

## JWT Authentication

```php
// JWT helper (minimal, no external lib)
function generateJWT(array $payload, string $secret, int $ttl = 3600): string
{
    $header = base64url_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload['iat'] = time();
    $payload['exp'] = time() + $ttl;
    $payloadEncoded = base64url_encode(json_encode($payload));
    $signature = base64url_encode(
        hash_hmac('sha256', "$header.$payloadEncoded", $secret, true)
    );
    return "$header.$payloadEncoded.$signature";
}

function validateJWT(string $token, string $secret): ?array
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    [$header, $payload, $signature] = $parts;

    $expectedSig = base64url_encode(
        hash_hmac('sha256', "$header.$payload", $secret, true)
    );

    if (!hash_equals($expectedSig, $signature)) return null;

    $data = json_decode(base64url_decode($payload), true);
    if ($data['exp'] < time()) return null;

    return $data;
}

function base64url_encode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode(string $data): string
{
    return base64_decode(strtr($data, '-_', '+/'));
}
```

## Rate Limiting (Simple In-Memory)

```php
class RateLimiter
{
    private int $maxRequests;
    private int $windowSeconds;
    private array $storage = [];

    public function __construct(int $maxRequests = 60, int $windowSeconds = 60)
    {
        $this->maxRequests = $maxRequests;
        $this->windowSeconds = $windowSeconds;
    }

    public function isAllowed(string $key): bool
    {
        $now = time();
        $windowStart = $now - $this->windowSeconds;

        // Clean old entries
        if (!isset($this->storage[$key])) {
            $this->storage[$key] = [];
        }

        $this->storage[$key] = array_filter(
            $this->storage[$key],
            fn(int $ts) => $ts > $windowStart
        );

        if (count($this->storage[$key]) >= $this->maxRequests) {
            return false;
        }

        $this->storage[$key][] = $now;
        return true;
    }
}
```

## OpenAPI (Swagger) Spec Template

```yaml
openapi: 3.0.3
info:
  title: Users API
  version: 1.0.0
paths:
  /api/users:
    get:
      summary: List all users
      parameters:
        - name: page
          in: query
          schema: { type: integer, default: 1 }
        - name: per_page
          in: query
          schema: { type: integer, default: 20 }
      responses:
        '200':
          description: Paginated list of users
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items:
                      $ref: '#/components/schemas/User'
                  meta:
                    type: object
                    properties:
                      total: { type: integer }
                      page: { type: integer }
                      per_page: { type: integer }
    post:
      summary: Create a user
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required: [name, email]
              properties:
                name: { type: string }
                email: { type: string, format: email }
      responses:
        '201': { description: User created }
        '422': { description: Validation error }
components:
  schemas:
    User:
      type: object
      properties:
        id: { type: integer }
        name: { type: string }
        email: { type: string }
        created_at: { type: string, format: date-time }
```
