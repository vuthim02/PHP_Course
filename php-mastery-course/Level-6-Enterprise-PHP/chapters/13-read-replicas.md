# Chapter 13: Read Replicas

## Learning Objectives

- Configure MySQL read replicas
- Implement read/write splitting
- Handle replication lag
- Scale read capacity

---

## 13.1 Read/Write Splitting

```php
<?php
class DatabaseCluster
{
    private PDO $writer;
    private array $readers = [];
    private int $readerIndex = 0;

    public function __construct(array $config)
    {
        $this->writer = new PDO(
            $config['writer']['dsn'],
            $config['writer']['user'],
            $config['writer']['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        foreach ($config['readers'] as $reader) {
            $this->readers[] = new PDO(
                $reader['dsn'],
                $reader['user'],
                $reader['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
    }

    public function writer(): PDO
    {
        return $this->writer;
    }

    public function reader(): PDO
    {
        if (empty($this->readers)) {
            return $this->writer; // Fallback to writer
        }

        // Round-robin load balancing
        $reader = $this->readers[$this->readerIndex % count($this->readers)];
        $this->readerIndex++;
        return $reader;
    }
}

// Repository with read/write splitting
class UserRepository
{
    public function __construct(private DatabaseCluster $db) {}

    public function find(int $id): ?User
    {
        // Read operation — use replica
        $stmt = $this->db->reader()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetchObject(User::class) ?: null;
    }

    public function create(array $data): User
    {
        // Write operation — use primary
        $this->db->writer()
            ->prepare('INSERT INTO users (name, email) VALUES (?, ?)')
            ->execute([$data['name'], $data['email']]);

        $user = $this->find($this->db->writer()->lastInsertId());
        
        // Force read from writer for consistency
        $this->waitForReplication($user->id);
        
        return $user;
    }

    public function update(int $id, array $data): User
    {
        $this->db->writer()
            ->prepare('UPDATE users SET name = ? WHERE id = ?')
            ->execute([$data['name'], $id]);

        return $this->find($id);
    }

    private function waitForReplication(int $userId, int $maxWait = 2): void
    {
        $start = time();
        
        while (time() - $start < $maxWait) {
            $reader = $this->db->reader();
            $stmt = $reader->query('SELECT MAX(gtid_executed) FROM performance_schema.replication_applier_status');
            
            // Check if replica has caught up
            $stmt = $reader->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            
            if ($stmt->fetchObject(User::class)) {
                return; // Replica has the data
            }
            
            usleep(100000); // 100ms
        }
    }
}
```

---

## 13.2 Exercises

1. Set up MySQL primary-replica replication
2. Implement read/write splitting in a repository
3. Handle replication lag with a strategy (wait, fallback to writer, stale ok)
4. Add health checks and failover for replicas

---

## Further Reading

- **Doc:** [MySQL Replication](https://dev.mysql.com/doc/refman/8.0/en/replication.html)
