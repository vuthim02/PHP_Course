# Chapter 18: API Testing

## Learning Objectives

- Write API tests with PHPUnit
- Test authentication and authorization
- Implement contract testing
- Automate API test suites

---

## 18.1 API Test Setup

```php
<?php
namespace Tests\Api;

use PHPUnit\Framework\TestCase;

class ApiTestCase extends TestCase
{
    protected static string $baseUrl = 'http://localhost:8000/api';
    protected static ?string $authToken = null;
    private static array $clientOptions = [
        'http_errors' => false,
        'timeout' => 5,
        'headers' => [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ],
    ];

    protected function setUp(): void
    {
        // Reset database state for each test
        $this->resetDatabase();
        
        // Authenticate for protected endpoints
        self::$authToken = $this->getAuthToken();
    }

    protected function get(string $uri, array $params = []): array
    {
        $url = self::$baseUrl . $uri;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->getHeaders(),
        ]);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $status,
            'body' => json_decode($response, true),
        ];
    }

    protected function post(string $uri, array $data = []): array
    {
        $ch = curl_init(self::$baseUrl . $uri);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $this->getHeaders(),
        ]);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $status,
            'body' => json_decode($response, true),
        ];
    }

    protected function assertApiSuccess(array $response, int $expectedStatus = 200): void
    {
        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertTrue($response['body']['success'] ?? false);
    }

    protected function assertApiError(array $response, int $expectedStatus): void
    {
        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertFalse($response['body']['success'] ?? true);
        $this->assertArrayHasKey('error', $response['body']);
    }

    private function getHeaders(): array
    {
        $headers = self::$clientOptions['headers'];
        if (self::$authToken) {
            $headers[] = "Authorization: Bearer " . self::$authToken;
        }
        return $headers;
    }

    private function resetDatabase(): void
    {
        // Truncate tables before each test
    }

    private function getAuthToken(): string
    {
        // Authenticate and get token for test user
        return 'test-token';
    }
}

class UserApiTest extends ApiTestCase
{
    public function test_can_list_users(): void
    {
        $response = $this->get('/users');
        $this->assertApiSuccess($response);
        $this->assertArrayHasKey('data', $response['body']);
    }

    public function test_can_create_user(): void
    {
        $response = $this->post('/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'SecurePass123!',
        ]);

        $this->assertApiSuccess($response, 201);
        $this->assertEquals('Test User', $response['body']['data']['name']);
    }

    public function test_validation_error_on_missing_fields(): void
    {
        $response = $this->post('/users', []);
        $this->assertApiError($response, 422);
    }

    public function test_authentication_required(): void
    {
        self::$authToken = null;
        $response = $this->get('/users/admin');
        $this->assertApiError($response, 401);
    }
}
```

---

## 18.2 Exercises

1. Write API tests for all CRUD endpoints
2. Test authentication and authorization scenarios
3. Add contract tests with Pact PHP
4. Automate API tests in CI/CD pipeline

---

## Further Reading

- **Doc:** [PHPUnit](https://phpunit.de/documentation.html)
- **Doc:** [Pact PHP](https://docs.pact.io/implementation_guides/php/)
