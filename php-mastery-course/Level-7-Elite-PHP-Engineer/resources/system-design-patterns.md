# System Design Patterns for PHP Engineers

## Scalability Patterns

### 1. Load Balancing

```
         ┌─────────┐
         │  LB     │
         ├─────────┤
         │ Round   │
         │ Robin   │
         └────┬────┘
         ┌────┼────┐
         │    │    │
    ┌────┴┐ ┌┴────┐ ┌┴────┐
    │ App1│ │App2 │ │App3 │
    └─────┘ └─────┘ └─────┘
```

**PHP Implementation:**
```php
// Application must be stateless for horizontal scaling
class UserService
{
    public function __construct(
        private UserRepositoryInterface $repo,
        private CacheInterface $cache,
    ) {}

    // No local state — all data from DB/cache
    public function getUser(int $id): User
    {
        return $this->cache->remember("user.$id", 3600, function () use ($id) {
            return $this->repo->findById($id);
        });
    }
}

// Session storage must be external (Redis, not files)
// config/session.php
'driver' => env('SESSION_DRIVER', 'redis'),
```

### 2. Database Read Replicas

```
        ┌──────────┐
        │   App    │
        └────┬─────┘
        ┌────┼─────┐
        │    │     │
   ┌────┴┐ ┌┴─────┴┐
   │Write│ │ Read  │
   │Primary││Replica1│
   └─────┘ └───────┘
           ┌───────┐
           │Replica2│
           └───────┘
```

```php
class DatabaseRouter
{
    private PDO $writeConnection;
    private array $readConnections;

    public function __construct()
    {
        $this->writeConnection = new PDO($_ENV['DB_WRITE_DSN']);
        $this->readConnections = [
            new PDO($_ENV['DB_READ_1_DSN']),
            new PDO($_ENV['DB_READ_2_DSN']),
        ];
    }

    public function write(): PDO
    {
        return $this->writeConnection;
    }

    public function read(): PDO
    {
        // Round-robin for read replicas
        $index = array_rand($this->readConnections);
        return $this->readConnections[$index];
    }
}

// Usage in repository
class UserRepository
{
    public function findById(int $id): ?User
    {
        $stmt = $this->db->read()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetchObject(User::class) ?: null;
    }

    public function save(User $user): void
    {
        $stmt = $this->db->write()->prepare('UPDATE users SET name = ? WHERE id = ?');
        $stmt->execute([$user->name, $user->id]);
    }
}
```

### 3. Caching Strategy

```
App
├── L1: In-memory (per request)
│   $cache = ['key' => 'value'];
│
├── L2: Local (APCu, shared memory)
│   apcu_fetch('key');
│
├── L3: Distributed (Redis, Memcached)
│   $redis->get('key');
│
├── L4: Database (SQL)
│   SELECT * FROM cache WHERE key = ?
│
└── L5: Origin (API, file, compute)
    // Expensive operation
```

```php
class MultiLevelCache
{
    private array $local = [];

    public function remember(string $key, int $ttl, callable $loader): mixed
    {
        // L1: Local cache (per request)
        if (isset($this->local[$key])) {
            return $this->local[$key];
        }

        // L2: APCu (shared across processes)
        $value = apcu_fetch($key, $success);
        if ($success) {
            $this->local[$key] = $value;
            return $value;
        }

        // L3: Redis (distributed)
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $cached = $redis->get($key);
        if ($cached !== false) {
            $value = unserialize($cached);
            $this->local[$key] = $value;
            apcu_store($key, $value, $ttl);
            return $value;
        }

        // Miss — load from origin
        $value = $loader();

        // Store in all cache layers
        $this->local[$key] = $value;
        apcu_store($key, $value, $ttl);
        $redis->setex($key, $ttl, serialize($value));

        return $value;
    }

    public function invalidate(string $key): void
    {
        unset($this->local[$key]);
        apcu_delete($key);

        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->del($key);
    }
}
```

### 4. Circuit Breaker Pattern

Prevents cascading failures when a downstream service is down.

```php
class CircuitBreaker
{
    private const STATE_CLOSED = 'closed';    // Normal operation
    private const STATE_OPEN = 'open';        // Failing — fast fail
    private const STATE_HALF_OPEN = 'half_open'; // Testing recovery

    private string $state = self::STATE_CLOSED;
    private int $failureCount = 0;
    private int $successCount = 0;
    private ?int $lastFailureTime = null;

    public function __construct(
        private int $threshold = 5,
        private int $timeout = 30,     // seconds before half-open
        private int $successThreshold = 2,
    ) {}

    public function call(callable $operation): mixed
    {
        if ($this->state === self::STATE_OPEN) {
            if ($this->isTimeoutExpired()) {
                $this->state = self::STATE_HALF_OPEN;
            } else {
                throw new CircuitOpenException('Circuit breaker is open');
            }
        }

        try {
            $result = $operation();
            $this->onSuccess();
            return $result;
        } catch (\Throwable $e) {
            $this->onFailure();
            throw $e;
        }
    }

    private function onSuccess(): void
    {
        $this->failureCount = 0;

        if ($this->state === self::STATE_HALF_OPEN) {
            $this->successCount++;
            if ($this->successCount >= $this->successThreshold) {
                $this->state = self::STATE_CLOSED;
                $this->successCount = 0;
            }
        }
    }

    private function onFailure(): void
    {
        $this->failureCount++;
        $this->lastFailureTime = time();

        if ($this->failureCount >= $this->threshold) {
            $this->state = self::STATE_OPEN;
        }
    }

    private function isTimeoutExpired(): bool
    {
        return $this->lastFailureTime !== null
            && (time() - $this->lastFailureTime) >= $this->timeout;
    }
}

// Usage
class ExternalPaymentService
{
    private CircuitBreaker $circuitBreaker;

    public function charge(float $amount): PaymentResult
    {
        return $this->circuitBreaker->call(function () use ($amount) {
            return $this->httpClient->post('https://payment-gateway.com/charge', [
                'amount' => $amount,
            ]);
        });
    }
}
```

### 5. Bulkhead Pattern

Isolate resources to prevent one failing component from taking down the whole system.

```php
class BulkheadPool
{
    private array $connections = [];
    private int $maxConnections;
    private int $currentConnections = 0;

    public function __construct(
        private string $name,
        int $max
    ) {
        $this->maxConnections = $max;
    }

    public function acquire(): mixed
    {
        if ($this->currentConnections >= $this->maxConnections) {
            throw new BulkheadFullException("$this->name pool is full");
        }

        $this->currentConnections++;
        // Return connection
    }

    public function release(): void
    {
        $this->currentConnections--;
    }
}

// Isolate different external services
class ServicePool
{
    public function __construct(
        public BulkheadPool $paymentPool = new BulkheadPool('payment', 5),
        public BulkheadPool $emailPool = new BulkheadPool('email', 10),
        public BulkheadPool $notificationPool = new BulkheadPool('notification', 8),
    ) {}
}
```

### 6. Queue-Based Load Leveling

```php
// Producer
class OrderController
{
    public function place(Request $request): JsonResponse
    {
        $order = $this->orderService->create($request->validated());

        // Don't process synchronously — queue it
        Queue::push(new SendOrderConfirmation($order->id));
        Queue::push(new UpdateInventory($order->id));
        Queue::push(new ProcessPayment($order->id));

        return response()->json(['order_id' => $order->id], 202);
    }
}

// Consumer (queue worker)
class SendOrderConfirmation
{
    public function __construct(private int $orderId) {}

    public function handle(): void
    {
        $order = Order::find($this->orderId);
        $email = new OrderConfirmationEmail($order);
        $email->send();  // May take 1-2 seconds — doesn't block the user
    }
}
```

## Database Patterns

### 7. Database Sharding

```php
class ShardManager
{
    /** @var PDO[] */
    private array $shards;

    public function __construct(array $shardConfigs)
    {
        foreach ($shardConfigs as $i => $config) {
            $this->shards[$i] = new PDO($config['dsn'], $config['user'], $config['pass']);
        }
    }

    public function getShard(int $userId): PDO
    {
        // Simple modulo-based sharding
        $shardIndex = $userId % count($this->shards);
        return $this->shards[$shardIndex];
    }

    public function saveUser(User $user): void
    {
        $shard = $this->getShard($user->id);
        // CRUD on the shard
    }

    public function findUser(int $id): ?User
    {
        $shard = $this->getShard($id);
        // Query the shard
    }
}

// Consistent hashing (better scaling than modulo)
class ConsistentHash
{
    private int $replicas = 64;
    private array $ring = [];
    private array $nodes = [];

    public function addNode(string $node): void
    {
        for ($i = 0; $i < $this->replicas; $i++) {
            $hash = crc32("$node:$i");
            $this->ring[$hash] = $node;
        }
        ksort($this->ring);
        $this->nodes[] = $node;
    }

    public function getNode(string $key): string
    {
        if (empty($this->ring)) {
            throw new \RuntimeException('No nodes available');
        }

        $hash = crc32($key);
        foreach ($this->ring as $nodeHash => $node) {
            if ($hash <= $nodeHash) {
                return $node;
            }
        }

        // Wrap around
        return reset($this->ring);
    }
}
```

## Distributed Systems Patterns

### 8. Saga Pattern (for distributed transactions)

```php
// Each step publishes an event, compensating actions roll back
class CreateOrderSaga
{
    public function __construct(
        private EventBus $eventBus,
        private OrderRepository $orderRepo,
        private InventoryService $inventory,
        private PaymentService $payment,
    ) {}

    public function execute(array $orderData): void
    {
        // Step 1: Create order
        $order = Order::create($orderData);
        $this->orderRepo->save($order);

        try {
            // Step 2: Reserve inventory
            $this->inventory->reserve($order->items);
        } catch (\Exception $e) {
            // Compensate step 1
            $this->orderRepo->delete($order->id);
            throw $e;
        }

        try {
            // Step 3: Process payment
            $this->payment->charge($order->total, $order->customerId);
        } catch (\Exception $e) {
            // Compensate step 2 and step 1
            $this->inventory->release($order->items);
            $this->orderRepo->delete($order->id);
            throw $e;
        }

        $this->eventBus->dispatch(new OrderCreated($order->id));
    }
}
```

### 9. Event Sourcing

```php
// Store all events, current state is derived by replaying events
class EventStore
{
    public function __construct(private PDO $pdo) {}

    public function append(string $aggregateType, string $aggregateId, array $events): void
    {
        foreach ($events as $event) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO events (aggregate_type, aggregate_id, event_type, data, occurred_at)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $aggregateType,
                $aggregateId,
                get_class($event),
                json_encode($event),
                (new \DateTimeImmutable())->format('Y-m-d H:i:s.u'),
            ]);
        }
    }

    public function getEvents(string $aggregateType, string $aggregateId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM events
             WHERE aggregate_type = ? AND aggregate_id = ?
             ORDER BY id ASC'
        );
        $stmt->execute([$aggregateType, $aggregateId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Rebuild aggregate from events
class OrderProjector
{
    public function rebuild(string $orderId, EventStore $store): Order
    {
        $events = $store->getEvents('order', $orderId);
        $order = new Order();

        foreach ($events as $eventData) {
            $event = unserialize($eventData['data']);
            $order->apply($event); // Each event mutates state
        }

        return $order;
    }
}
```

## Scalability Antipatterns to Avoid

| Antipattern | Problem | Solution |
|-------------|---------|----------|
| Sticky sessions | Prevents horizontal scaling | Store session in Redis |
| Database as queue | Polling overloads DB | Use RabbitMQ, SQS, Redis |
| Synchronous everything | Coupling, slow responses | Use async queues, events |
| God object | Single point of failure | Split into microservices |
| Chatty microservices | High latency | Aggregate calls, CQRS |
| Premature optimization | Complex without data | Profile first, optimize second |
| No caching | Repeated expensive calls | Multi-level caching |
| Single database | Scale ceiling | Sharding, read replicas |
