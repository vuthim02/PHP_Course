# Chapter 12: Database Sharding

## Learning Objectives

- Implement horizontal sharding
- Choose sharding strategies
- Handle cross-shard queries
- Manage shard rebalancing

---

## 12.1 Sharding Implementation

```php
<?php
class ShardManager
{
    private array $connections = [];

    public function __construct(array $shardConfigs)
    {
        foreach ($shardConfigs as $shardId => $config) {
            $this->connections[$shardId] = new PDO(
                $config['dsn'],
                $config['user'],
                $config['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
    }

    public function getShard(string $shardKey): int
    {
        // Consistent hashing
        $hash = crc32($shardKey);
        return abs($hash) % count($this->connections);
    }

    public function getConnection(string $shardKey): PDO
    {
        $shardId = $this->getShard($shardKey);
        return $this->connections[$shardId];
    }

    public function getShardById(int $shardId): PDO
    {
        if (!isset($this->connections[$shardId])) {
            throw new \InvalidArgumentException("Invalid shard ID: {$shardId}");
        }
        return $this->connections[$shardId];
    }

    public function getAllConnections(): array
    {
        return $this->connections;
    }

    public function queryOnAllShards(callable $query): array
    {
        $results = [];
        foreach ($this->connections as $shardId => $connection) {
            $results[$shardId] = $query($connection);
        }
        return $results;
    }
}

// Sharded repository
class ShardedUserRepository
{
    public function __construct(private ShardManager $shards) {}

    public function find(string $userId): ?User
    {
        $pdo = $this->shards->getConnection($userId);
        
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        
        return $stmt->fetchObject(User::class) ?: null;
    }

    public function save(User $user): void
    {
        $pdo = $this->shards->getConnection($user->id);
        
        $stmt = $pdo->prepare(
            'INSERT INTO users (id, name, email) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE name = VALUES(name), email = VALUES(email)'
        );
        $stmt->execute([$user->id, $user->name, $user->email]);
    }

    public function findByEmail(string $email): ?User
    {
        // Must query all shards since email is not the shard key
        foreach ($this->shards->getAllConnections() as $pdo) {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetchObject(User::class);
            if ($user) {
                return $user;
            }
        }
        return null;
    }

    public function countAll(): int
    {
        $total = 0;
        foreach ($this->shards->getAllConnections() as $pdo) {
            $total += (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        }
        return $total;
    }
}
```

---

## 12.2 Exercises

1. Implement consistent hashing for shard key distribution
2. Build a sharded repository for orders (shard by customer_id)
3. Handle cross-shard queries (user lookup by email)
4. Implement shard rebalancing when adding new shards

---

## Further Reading

- **Doc:** [MySQL Sharding](https://dev.mysql.com/doc/refman/8.0/en/sharding.html)
- **Doc:** [Vitess](https://vitess.io/)
