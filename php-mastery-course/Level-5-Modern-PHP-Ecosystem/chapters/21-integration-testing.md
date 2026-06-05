# Chapter 21: Integration Testing

## Learning Objectives

By the end of this chapter you will:
- Understand the difference between unit, integration, and e2e tests
- Set up test databases with migrations and seeds
- Write API integration tests with PHPUnit
- Use test containers for isolated service testing
- Implement database transaction rollback between tests
- Build a comprehensive integration test suite

---

## 21.1 Unit vs Integration vs E2E

| Test Type | Scope | Speed | Dependencies | Confidence |
|-----------|-------|-------|-------------|------------|
| **Unit** | Single class/method | Fast (~ms) | None (mocked) | Low (isolated) |
| **Integration** | Multiple classes + DB/API | Moderate (~s) | Real DB, cache | Medium |
| **E2E** | Full system + browser | Slow (~min) | Everything | High |

Think of it like testing a car:
- **Unit test** = Testing the fuel injector in a lab (isolated)
- **Integration test** = Testing the engine with real fuel and spark plugs connected
- **E2E test** = Driving the car on a real road

---

## 21.2 Test Database Setup

### Database Configuration

```php
<?php
// config/testing.php
return [
    'database' => [
        'driver' => 'mysql',
        'host' => getenv('TEST_DB_HOST') ?: '127.0.0.1',
        'port' => getenv('TEST_DB_PORT') ?: '3306',
        'database' => getenv('TEST_DB_NAME') ?: 'myapp_test',
        'username' => getenv('TEST_DB_USER') ?: 'root',
        'password' => getenv('TEST_DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
];
```

### Test Database Trait

```php
<?php
trait DatabaseMigrations
{
    private static ?PDO $pdo = null;

    protected static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../config/testing.php';
            $db = $config['database'];

            self::$pdo = new PDO(
                "{$db['driver']}:host={$db['host']};port={$db['port']};dbname={$db['database']};charset={$db['charset']}",
                $db['username'],
                $db['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }

        return self::$pdo;
    }

    protected function setUpDatabase(): void
    {
        $this->runMigrations();
        $this->seedDatabase();
    }

    protected function tearDownDatabase(): void
    {
        $this->rollbackMigrations();
    }

    protected function runMigrations(): void
    {
        $pdo = $this->getConnection();
        $migrations = glob(__DIR__ . '/../migrations/*.sql');
        sort($migrations);

        foreach ($migrations as $file) {
            $sql = file_get_contents($file);
            $pdo->exec($sql);
        }
    }

    protected function rollbackMigrations(): void
    {
        $pdo = $this->getConnection();
        $migrations = glob(__DIR__ . '/../migrations/*.sql');
        rsort($migrations);

        foreach ($migrations as $file) {
            $basename = basename($file);
            preg_match('/^\d+_(.*?)\./', $basename, $m);
            $table = $m[1] ?? 'migrations';
            $pdo->exec("DROP TABLE IF EXISTS {$table}");
        }
    }

    protected function seedDatabase(): void
    {
        $pdo = $this->getConnection();
        $seeds = glob(__DIR__ . '/../seeds/*.sql');
        sort($seeds);

        foreach ($seeds as $file) {
            $sql = file_get_contents($file);
            $pdo->exec($sql);
        }
    }

    protected function truncateAllTables(): void
    {
        $pdo = $this->getConnection();
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

        $stmt = $pdo->query('SHOW TABLES');
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $pdo->exec("TRUNCATE TABLE {$row[0]}");
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }
}
```

### Transaction Rollback (Faster Alternative)

Instead of dropping and recreating tables, wrap each test in a transaction:

```php
<?php
trait DatabaseTransactions
{
    private static ?PDO $pdo = null;

    protected function setUp(): void
    {
        $this->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->getConnection()->rollBack();
    }

    private function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../config/testing.php';
            $db = $config['database'];
            self::$pdo = new PDO(
                "{$db['driver']}:host={$db['host']};port={$db['port']};dbname={$db['database']};charset={$db['charset']}",
                $db['username'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }

        return self::$pdo;
    }

    protected function seed(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->getConnection()->prepare(
            "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));

        return (int) $this->getConnection()->lastInsertId();
    }

    protected function assertDatabaseHas(string $table, array $criteria): void
    {
        $conditions = implode(' AND ', array_map(fn($col) => "{$col} = ?", array_keys($criteria)));
        $stmt = $this->getConnection()->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE {$conditions}"
        );
        $stmt->execute(array_values($criteria));
        $count = (int) $stmt->fetchColumn();

        $this->assertGreaterThan(0, $count, "Found 0 rows in {$table} matching criteria");
    }

    protected function assertDatabaseMissing(string $table, array $criteria): void
    {
        $conditions = implode(' AND ', array_map(fn($col) => "{$col} = ?", array_keys($criteria)));
        $stmt = $this->getConnection()->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE {$conditions}"
        );
        $stmt->execute(array_values($criteria));
        $count = (int) $stmt->fetchColumn();

        $this->assertEquals(0, $count, "Found {$count} rows in {$table} matching criteria");
    }
}
```

---

## 21.3 API Integration Tests

```php
<?php
use PHPUnit\Framework\TestCase;

class UserApiTest extends TestCase
{
    use DatabaseTransactions;

    private UserRepository $repository;
    private UserController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $pdo = $this->getConnection();
        $this->repository = new UserRepository($pdo);
        $this->controller = new UserController($this->repository);
    }

    public function testCreateUser(): void
    {
        $id = $this->controller->create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'SecurePass123!',
        ]);

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);

        $this->assertDatabaseHas('users', [
            'email' => 'alice@example.com',
            'name' => 'Alice',
        ]);
    }

    public function testCreateUserWithDuplicateEmail(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Email already exists');

        $this->controller->create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'SecurePass123!',
        ]);

        $this->controller->create([
            'name' => 'Bob',
            'email' => 'alice@example.com',
            'password' => 'SecurePass456!',
        ]);
    }

    public function testFindUserById(): void
    {
        $id = $this->seed('users', [
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password_hash' => password_hash('test', PASSWORD_BCRYPT),
        ]);

        $user = $this->repository->findById($id);

        $this->assertNotNull($user);
        $this->assertEquals('Bob', $user['name']);
        $this->assertEquals('bob@example.com', $user['email']);
    }

    public function testFindUserReturnsNullForMissingId(): void
    {
        $user = $this->repository->findById(99999);
        $this->assertNull($user);
    }

    public function testPagination(): void
    {
        // Seed 25 users
        for ($i = 1; $i <= 25; $i++) {
            $this->seed('users', [
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password_hash' => password_hash('test', PASSWORD_BCRYPT),
            ]);
        }

        $page1 = $this->repository->findAll(1, 10);
        $this->assertCount(10, $page1['items']);
        $this->assertEquals(25, $page1['total']);
        $this->assertEquals(3, $page1['total_pages']);

        $page3 = $this->repository->findAll(3, 10);
        $this->assertCount(5, $page3['items']);
    }

    public function testSoftDelete(): void
    {
        $id = $this->seed('users', [
            'name' => 'Charlie',
            'email' => 'charlie@example.com',
            'password_hash' => password_hash('test', PASSWORD_BCRYPT),
        ]);

        $this->repository->delete($id);

        // Should not be found by findById (which excludes deleted)
        $user = $this->repository->findById($id);
        $this->assertNull($user);

        // But still exists in DB (soft delete)
        $this->assertDatabaseHas('users', ['id' => $id]);
    }
}
```

---

## 21.4 HTTP Integration Tests

```php
<?php
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class HttpApiTest extends TestCase
{
    private static ?Client $client = null;
    private static ?string $authToken = null;

    public static function setUpBeforeClass(): void
    {
        self::$client = new Client([
            'base_uri' => getenv('API_BASE_URL') ?: 'http://localhost:8080',
            'http_errors' => false, // Don't throw on non-2xx
            'timeout' => 5.0,
        ]);
    }

    public function testHealthCheck(): void
    {
        $response = self::$client->get('/health');
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('ok', $data['status']);
    }

    public function testCreateUser(): array
    {
        $response = self::$client->post('/api/users', [
            'json' => [
                'name' => 'Test User',
                'email' => 'test-' . uniqid() . '@example.com',
                'password' => 'TestPass123!',
            ],
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $data = json_decode((string) $response->getBody(), true);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertEquals('Test User', $data['name']);

        return $data;
    }

    /**
     * @depends testCreateUser
     */
    public function testGetUser(array $created): void
    {
        $response = self::$client->get("/api/users/{$created['id']}");
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode((string) $response->getBody(), true);
        $this->assertEquals($created['id'], $data['id']);
        $this->assertEquals($created['name'], $data['name']);
    }

    public function testCreateUserValidationFailure(): void
    {
        $response = self::$client->post('/api/users', [
            'json' => [
                'name' => '',
                'email' => 'not-an-email',
            ],
        ]);

        $this->assertEquals(422, $response->getStatusCode());
        $data = json_decode((string) $response->getBody(), true);

        $this->assertArrayHasKey('errors', $data);
        $this->assertArrayHasKey('name', $data['errors']);
        $this->assertArrayHasKey('email', $data['errors']);
    }

    public function testAuthentication(): void
    {
        // Register
        $email = 'auth-' . uniqid() . '@example.com';
        self::$client->post('/api/auth/register', [
            'json' => [
                'name' => 'Auth User',
                'email' => $email,
                'password' => 'TestPass123!',
            ],
        ]);

        // Login
        $response = self::$client->post('/api/auth/login', [
            'json' => [
                'email' => $email,
                'password' => 'TestPass123!',
            ],
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('token', $data);

        self::$authToken = $data['token'];
    }

    /**
     * @depends testAuthentication
     */
    public function testAuthenticatedEndpoint(): void
    {
        $response = self::$client->get('/api/profile', [
            'headers' => [
                'Authorization' => 'Bearer ' . self::$authToken,
            ],
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode((string) $response->getBody(), true);
        $this->assertEquals('Auth User', $data['name']);
    }

    public function testAuthenticatedEndpointFailsWithoutToken(): void
    {
        $response = self::$client->get('/api/profile');
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testNotFound(): void
    {
        $response = self::$client->get('/api/nonexistent');
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testPagination(): void
    {
        $response = self::$client->get('/api/users?page=1&per_page=10');
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode((string) $response->getBody(), true);
        $this->assertArrayHasKey('items', $data);
        $this->assertArrayHasKey('total', $data);
        $this->assertArrayHasKey('page', $data);
        $this->assertLessThanOrEqual(10, count($data['items']));
    }
}
```

---

## 21.5 Test Containers for Service Isolation

Test containers let you spin up real MySQL/Redis/etc. in Docker for testing:

```bash
composer require --dev testcontainers/testcontainers-php
```

```php
<?php
use PHPUnit\Framework\TestCase;
use Testcontainers\Container\GenericContainer;

class DatabaseIntegrationTest extends TestCase
{
    private static $container;
    private static PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        // Start MySQL in Docker container
        self::$container = (new GenericContainer('mysql:8.0'))
            ->withEnv('MYSQL_ROOT_PASSWORD', 'test')
            ->withEnv('MYSQL_DATABASE', 'testdb')
            ->withExposedPorts(3306)
            ->start();

        $host = self::$container->getHost();
        $port = self::$container->getMappedPort(3306);

        // Wait for MySQL to be ready
        $connected = false;
        for ($i = 0; $i < 30; $i++) {
            try {
                self::$pdo = new PDO(
                    "mysql:host={$host};port={$port};dbname=testdb;charset=utf8mb4",
                    'root',
                    'test',
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                $connected = true;
                break;
            } catch (\PDOException) {
                usleep(500000); // 500ms
            }
        }

        if (!$connected) {
            throw new \RuntimeException('Could not connect to MySQL container');
        }

        // Run migrations
        self::$pdo->exec(file_get_contents(__DIR__ . '/../migrations/001_create_users.sql'));
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$container) {
            self::$container->stop();
        }
    }

    public function testInsertAndRetrieve(): void
    {
        self::$pdo->exec("INSERT INTO users (name, email) VALUES ('Container User', 'container@test.com')");
        $stmt = self::$pdo->query("SELECT * FROM users WHERE email = 'container@test.com'");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($user);
        $this->assertEquals('Container User', $user['name']);
    }
}
```

### Redis Test Container

```php
<?php
use PHPUnit\Framework\TestCase;
use Testcontainers\Container\GenericContainer;

class RedisIntegrationTest extends TestCase
{
    private static $container;
    private static \Redis $redis;

    public static function setUpBeforeClass(): void
    {
        self::$container = (new GenericContainer('redis:7-alpine'))
            ->withExposedPorts(6379)
            ->start();

        $host = self::$container->getHost();
        $port = self::$container->getMappedPort(6379);

        self::$redis = new \Redis();
        self::$redis->connect($host, $port);
    }

    public static function tearDownAfterClass(): void
    {
        self::$container?->stop();
    }

    public function testCacheSetGet(): void
    {
        self::$redis->set('test_key', 'test_value', 60);
        $this->assertEquals('test_value', self::$redis->get('test_key'));
    }

    public function testCacheExpiration(): void
    {
        self::$redis->set('temp_key', 'temp_value', 1);
        $this->assertEquals('temp_value', self::$redis->get('temp_key'));
        sleep(2);
        $this->assertFalse(self::$redis->get('temp_key'));
    }

    public function testIncrement(): void
    {
        self::$redis->del('counter');
        self::$redis->incr('counter');
        self::$redis->incr('counter');
        self::$redis->incr('counter');
        $this->assertEquals(3, self::$redis->get('counter'));
    }
}
```

---

## 21.6 Test Fixtures Factory

```php
<?php
/**
 * Factory for creating test data
 */
class UserFactory
{
    private static int $counter = 0;

    public static function create(array $overrides = []): array
    {
        self::$counter++;

        return array_merge([
            'name' => "User " . self::$counter,
            'email' => "user" . self::$counter . "@example.com",
            'password' => 'DefaultPass123!',
            'role' => 'user',
            'status' => 'active',
        ], $overrides);
    }

    public static function createAdmin(array $overrides = []): array
    {
        return self::create(array_merge([
            'role' => 'admin',
            'email' => "admin." . (self::$counter) . "@example.com",
        ], $overrides));
    }

    public static function createWithEmail(string $email): array
    {
        return self::create(['email' => $email]);
    }

    public static function createInactive(): array
    {
        return self::create(['status' => 'inactive']);
    }

    public static function createMany(int $count, array $overrides = []): array
    {
        $users = [];
        for ($i = 0; $i < $count; $i++) {
            $users[] = self::create($overrides);
        }
        return $users;
    }
}

// Usage in tests
$user = UserFactory::create();
$admin = UserFactory::createAdmin();
$inactive = UserFactory::createInactive();
$batch = UserFactory::createMany(50);
```

---

## 21.7 Mocking External Services

```php
<?php
use PHPUnit\Framework\TestCase;

class PaymentIntegrationTest extends TestCase
{
    private PaymentService $paymentService;

    protected function setUp(): void
    {
        // Mock HTTP client
        $mockClient = $this->createMock(HttpClient::class);

        // Simulate successful payment
        $mockClient->method('post')
            ->willReturn(new HttpResponse(200, json_encode([
                'id' => 'pi_123',
                'status' => 'succeeded',
                'amount' => 2999,
            ])));

        $this->paymentService = new PaymentService($mockClient);
    }

    public function testSuccessfulPayment(): void
    {
        $result = $this->paymentService->charge(2999, 'tok_visa');

        $this->assertTrue($result->isSuccess());
        $this->assertEquals('pi_123', $result->getTransactionId());
    }

    public function testFailedPayment(): void
    {
        $mockClient = $this->createMock(HttpClient::class);
        $mockClient->method('post')
            ->willReturn(new HttpResponse(402, json_encode([
                'error' => 'card_declined',
                'message' => 'Your card was declined',
            ])));

        $service = new PaymentService($mockClient);

        $this->expectException(PaymentException::class);
        $service->charge(2999, 'tok_chargeDeclined');
    }

    public function testNetworkTimeout(): void
    {
        $mockClient = $this->createMock(HttpClient::class);
        $mockClient->method('post')
            ->willThrowException(new \RuntimeException('Connection timed out'));

        $service = new PaymentService($mockClient);

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('timed out');
        $service->charge(2999, 'tok_visa');
    }
}
```

---

## 21.8 Running Integration Tests

```xml
<!-- phpunit.xml -->
<phpunit bootstrap="vendor/autoload.php">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>

    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="TEST_DB_HOST" value="127.0.0.1"/>
        <env name="TEST_DB_NAME" value="myapp_test"/>
        <env name="API_BASE_URL" value="http://localhost:8080"/>
    </php>
</phpunit>
```

```bash
# Run only integration tests
vendor/bin/phpunit --testsuite Integration

# Run with database setup
php bin/migrate --env=testing && php bin/seed --env=testing && vendor/bin/phpunit

# Run specific test
vendor/bin/phpunit tests/Integration/UserApiTest.php

# With coverage (excluding integration tests for speed)
vendor/bin/phpunit --testsuite Unit --coverage-html coverage
```

---

## 21.9 Exercises

1. **Database tests:** Write tests for CRUD operations against a real test database
2. **API tests:** Write HTTP tests for your REST API endpoints
3. **Factory pattern:** Create test factories for 3 different entities
4. **Transaction rollback:** Implement the transaction rollback trait
5. **External mock:** Mock a payment gateway and test success + failure paths
6. **Test containers:** Spin up a MySQL container for integration tests

---

## 21.10 Interview Questions

1. "What is the difference between unit and integration tests?"
2. "How do you handle database state between integration tests?"
3. "Why would you use test containers instead of mocking?"
4. "What strategies do you use to make integration tests fast?"
5. "How do you test external API integrations without hitting real endpoints?"
6. "What's the purpose of test factories?"
7. "How would you test database migrations?"

---

## Further Reading

- **Documentation:** [PHPUnit](https://phpunit.de/documentation.html)
- **Library:** [Test Containers for PHP](https://github.com/testcontainers/testcontainers-php)
- **Book:** "PHPUnit Essentials" by Zoran Antonic
- **Article:** [Integration Testing Best Practices](https://www.phpunit.de/manual/current/en/writing-tests-for-phpunit.html)
- **Pattern:** [Object Mother vs Test Data Builder](https://martinfowler.com/bliki/ObjectMother.html)

---

*End of Chapter 21. Proceed to Level 6: Enterprise PHP.*
