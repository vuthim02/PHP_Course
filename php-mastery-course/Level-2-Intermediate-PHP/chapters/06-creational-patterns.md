# Chapter 6: Design Patterns — Creational

## Learning Objectives

- Understand creational design patterns
- Implement Singleton, Factory, Builder, and Prototype
- Apply patterns to real PHP scenarios

---

```mermaid
flowchart TD
    subgraph Singleton
        A[Client Code] --> B[Database::getInstance()]
        B --> C{instance exists?}
        C -->|No| D[Create new PDO connection]
        C -->|Yes| E[Return existing instance]
        D --> E
    end

    subgraph Factory
        F[Client Code] --> G[LoggerFactory::create type]
        G --> H{type}
        H -->|file| I[new FileLogger]
        H -->|db| J[new DatabaseLogger]
        H -->|syslog| K[new SyslogLogger]
    end

    subgraph Builder
        L[Client Code] --> M[new QueryBuilder]
        M --> N[->select columns]
        N --> O[->from table]
        O --> P[->where condition]
        P --> Q[->build]
        Q --> R[Final SQL string]
    end
```

## 6.1 Singleton Pattern

```php
<?php
final class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=app',
            'user',
            'password',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton');
    }
}

$db = Database::getInstance();
$conn = $db->getConnection();
```

---

## 6.2 Factory Pattern

```php
<?php
interface Logger
{
    public function log(string $message): void;
}

class FileLogger implements Logger
{
    public function __construct(private string $filePath) {}

    public function log(string $message): void
    {
        file_put_contents(
            $this->filePath,
            '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n",
            FILE_APPEND
        );
    }
}

class DatabaseLogger implements Logger
{
    public function __construct(private PDO $pdo) {}

    public function log(string $message): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO logs (message, created_at) VALUES (?, NOW())'
        );
        $stmt->execute([$message]);
    }
}

class CloudLogger implements Logger
{
    public function __construct(private string $apiKey) {}

    public function log(string $message): void
    {
        // Send to cloud logging service
    }
}

class LoggerFactory
{
    public static function create(string $type, array $config = []): Logger
    {
        return match ($type) {
            'file' => new FileLogger(
                $config['path'] ?? '/var/log/app.log'
            ),
            'database' => new DatabaseLogger(
                $config['pdo'] ?? throw new \InvalidArgumentException('PDO required')
            ),
            'cloud' => new CloudLogger(
                $config['api_key'] ?? throw new \InvalidArgumentException('API key required')
            ),
            default => throw new \InvalidArgumentException("Unknown logger type: {$type}")
        };
    }
}

$logger = LoggerFactory::create('file', ['path' => 'app.log']);
$logger->log('Application started');
```

---

## 6.3 Builder Pattern

```php
<?php
class QueryBuilder
{
    private array $select = ['*'];
    private string $from = '';
    private array $where = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $params = [];

    public function select(array $columns): self
    {
        $this->select = $columns;
        return $this;
    }

    public function from(string $table): self
    {
        $this->from = $table;
        return $this;
    }

    public function where(string $condition, mixed $value): self
    {
        $this->where[] = $condition;
        $this->params[] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = "{$column} {$direction}";
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function build(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->select);
        $sql .= ' FROM ' . $this->from;

        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->where);
        }

        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}

$query = (new QueryBuilder())
    ->select(['id', 'name', 'email'])
    ->from('users')
    ->where('status = ?', 'active')
    ->where('created_at > ?', '2024-01-01')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->offset(20)
    ->build();
// SELECT id, name, email FROM users
// WHERE status = ? AND created_at > ?
// ORDER BY created_at DESC LIMIT 10 OFFSET 20
```

---

## 6.4 Exercises

1. Implement a Singleton for a configuration manager
2. Create a `NotificationFactory` that creates Email, SMS, and Push notifications
3. Build an `SQLInsertBuilder` using the Builder pattern
4. Add a Prototype pattern for cloning complex report objects

---

## Further Reading

- **Book:** "Design Patterns" by Gang of Four
- **Resource:** [Refactoring Guru — PHP Design Patterns](https://refactoring.guru/design-patterns/php)
