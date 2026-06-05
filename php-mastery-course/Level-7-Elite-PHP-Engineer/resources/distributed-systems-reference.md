# Distributed Systems — Reference for PHP Engineers

## Core Concepts

### CAP Theorem

```
C — Consistency: Every read gets the most recent write
A — Availability: Every request gets a response (not necessarily latest)
P — Partition Tolerance: System continues despite network split

Pick 2 out of 3:
- CA: Traditional RDBMS (single node)
- CP: Banking systems (consistency over availability during partition)
- AP: Social media (availability over consistency during partition)

In distributed systems, P is mandatory (networks fail).
So it's really: CP vs AP

Trade-off: Accept eventual consistency (AP), or sacrifice availability (CP)
```

### Consistency Models

| Model | Description | Example |
|-------|-------------|---------|
| **Strict** | Read always returns latest write | Ideal but impossible in distributed systems |
| **Linearizable** | Operations appear atomic, in real-time order | Distributed locks, leader election |
| **Sequential** | Operations in order per client, not global | Single-node DB |
| **Causal** | Related operations ordered, unrelated can be out of order | CRDTs |
| **Eventual** | Given enough time, all replicas converge | DNS, CDN, social feeds |
| **Read-your-writes** | Client always reads what it just wrote | Session consistency |
| **Monotonic reads** | Client never reads older data after newer | User profiles |

### Consensus Algorithms

```
Consensus ensures multiple nodes agree on a value despite failures.

- Paxos: Classic, hard to implement correctly
- Raft: Understandable, used in etcd, Consul, TiDB
- Zab: Used in Zookeeper
- Viewstamped Replication: Simplified Paxos
```

#### Raft Simplified

```
1. Leader election
   - Nodes start as followers
   - Timeout → candidate → votes → leader
   - Leader sends heartbeats to maintain authority

2. Log replication
   - Client sends command to leader
   - Leader appends to log, sends to followers
   - Followers acknowledge
   - Leader commits when majority confirms
   - Leader notifies followers

3. Safety
   - Only leader can commit
   - Leader crash: new election, new leader replays committed entries
   - Logs are consistent across servers
```

## Communication Patterns

### Synchronous (Request-Response)

```
Client ──Request──▶ Server
Client ◀─Response── Server

Pros: Simple, intuitive
Cons: Coupling, blocking, cascading failures
```

```php
// Synchronous HTTP call
class SyncPaymentService
{
    public function charge(int $amount): PaymentResult
    {
        // Blocks until response or timeout (30s)
        $response = $this->httpClient->post('https://payments/charge', [
            'amount' => $amount,
        ]);

        return PaymentResult::fromArray($response);
    }
}
```

### Asynchronous (Message Queues)

```
Producer ──Message──▶ Queue ──Message──▶ Consumer
                                            │
                                    (process later)

Pros: Decoupling, resilience, load leveling
Cons: Complexity, eventual consistency, debugging
```

```php
// Asynchronous with RabbitMQ (using php-amqplib)
class OrderProcessor
{
    public function __construct(
        private AMQPConnection $connection,
    ) {}

    public function submitOrder(Order $order): void
    {
        // Publish event — don't wait for processing
        $channel = $this->connection->channel();
        $channel->queue_declare('order_processing', durable: true);

        $msg = new AMQPMessage(json_encode([
            'order_id' => $order->id,
            'total' => $order->total,
            'user_id' => $order->userId,
        ]), [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        ]);

        $channel->basic_publish($msg, '', 'order_processing');
        $channel->close();
    }
}

// Consumer (separate worker process)
class OrderWorker
{
    public function consume(): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare('order_processing', durable: true);

        $callback = function (AMQPMessage $msg) {
            $data = json_decode($msg->body, true);

            try {
                $this->processOrder($data['order_id']);
                $msg->ack();  // Acknowledge — remove from queue
            } catch (\Exception $e) {
                $msg->nack();  // Reject — back to queue or DLQ
            }
        };

        $channel->basic_qos(null, 1, null);  // One at a time per worker
        $channel->basic_consume('order_processing', '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}
```

### Event-Driven

```
Service A ──Event──▶ Event Bus ──Event──▶ Service B
                              ├──Event──▶ Service C
                              └──Event──▶ Service D

Pros: Highly decoupled, scalable, auditable
Cons: Complexity, eventual consistency, debugging
```

```php
// Event-driven architecture
class EventBus
{
    private array $handlers = [];

    public function subscribe(string $eventType, callable $handler): void
    {
        $this->handlers[$eventType][] = $handler;
    }

    public function publish(object $event): void
    {
        $type = get_class($event);

        // Store event (event sourcing)
        $this->storeEvent($type, $event);

        // Dispatch to handlers
        foreach ($this->handlers[$type] ?? [] as $handler) {
            try {
                $handler($event);
            } catch (\Exception $e) {
                // Log failure, send to DLQ
                error_log("Event handler failed: {$e->getMessage()}");
            }
        }
    }

    // Outbox pattern — store events in DB, publish reliably
    private function storeEvent(string $type, object $event): void
    {
        DB::table('event_store')->insert([
            'event_type' => $type,
            'payload' => json_encode($event),
            'created_at' => now(),
        ]);
    }
}
```

## Service Discovery

```php
// Service discovery with Consul
class ServiceDiscovery
{
    public function __construct(
        private string $consulHost = 'http://consul:8500'
    ) {}

    public function discover(string $serviceName): array
    {
        $response = file_get_contents(
            "$this->consulHost/v1/health/service/$serviceName?passing=true"
        );

        $services = json_decode($response, true);
        $instances = [];

        foreach ($services as $service) {
            $instances[] = [
                'host' => $service['Service']['Address'],
                'port' => $service['Service']['Port'],
                'id' => $service['Service']['ID'],
            ];
        }

        return $instances;
    }

    public function pickInstance(string $serviceName): array
    {
        $instances = $this->discover($serviceName);

        if (empty($instances)) {
            throw new \RuntimeException("No healthy instances of $serviceName");
        }

        // Simple round-robin or random
        return $instances[array_rand($instances)];
    }
}

// Usage
$discovery = new ServiceDiscovery();
$instance = $discovery->pickInstance('payment-service');
$response = file_get_contents("http://{$instance['host']}:{$instance['port']}/health");
```

## API Gateway Pattern

```php
// Simple API gateway implementation
class ApiGateway
{
    private array $routes;

    public function __construct()
    {
        $this->routes = [
            'users' => 'http://user-service',
            'orders' => 'http://order-service',
            'payments' => 'http://payment-service',
            'notifications' => 'http://notification-service',
        ];
    }

    public function handle(Request $request): Response
    {
        $path = $request->getPath();
        $service = explode('/', trim($path, '/'))[0];

        if (!isset($this->routes[$service])) {
            return new JsonResponse(['error' => 'Service not found'], 404);
        }

        $backendUrl = $this->routes[$service] . $path;

        // Common gateway features:
        // 1. Authentication
        $this->authenticate($request);

        // 2. Rate limiting
        $this->rateLimit($request);

        // 3. Request transformation
        $transformedRequest = $this->transformRequest($request);

        // 4. Route to backend
        $response = $this->forward($backendUrl, $transformedRequest);

        // 5. Response transformation
        $transformedResponse = $this->transformResponse($response);

        // 6. Aggregation (collapse multiple backend calls)
        if ($service === 'orders') {
            $transformedResponse = $this->enrichWithUserData($transformedResponse);
        }

        return $transformedResponse;
    }

    private function enrichWithUserData(Response $response): Response
    {
        $data = json_decode($response->getContent(), true);

        foreach ($data['data'] ?? [] as &$order) {
            $userResponse = file_get_contents(
                "http://user-service/api/users/{$order['user_id']}"
            );
            $order['user'] = json_decode($userResponse, true);
        }

        return new JsonResponse($data);
    }
}
```

## Distributed Tracing

```php
// Context propagation across services
class TraceContext
{
    private static ?string $traceId = null;
    private static ?string $spanId = null;

    public static function start(): void
    {
        self::$traceId = bin2hex(random_bytes(16));
        self::$spanId = bin2hex(random_bytes(8));
    }

    public static function fromHeaders(array $headers): void
    {
        self::$traceId = $headers['X-Trace-Id'] ?? bin2hex(random_bytes(16));
        self::$spanId = bin2hex(random_bytes(8));
    }

    public static function injectHeaders(): array
    {
        return [
            'X-Trace-Id' => self::$traceId,
            'X-Span-Id' => self::$spanId,
            'X-Parent-Span-Id' => self::$spanId,
        ];
    }

    public static function log(string $message, array $context = []): void
    {
        $entry = [
            'timestamp' => microtime(true),
            'trace_id' => self::$traceId,
            'span_id' => self::$spanId,
            'message' => $message,
            'context' => $context,
        ];

        // Send to centralized logging (Elasticsearch, Datadog, etc.)
        Log::channel('tracing')->info(json_encode($entry));
    }
}

// Usage in middleware
class TraceMiddleware
{
    public function handle(Request $request, callable $next): Response
    {
        TraceContext::fromHeaders($request->headers->all());
        TraceContext::log('incoming_request', [
            'method' => $request->getMethod(),
            'path' => $request->getPath(),
        ]);

        $start = microtime(true);
        $response = $next($request);
        $duration = (microtime(true) - $start) * 1000;

        TraceContext::log('outgoing_response', [
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
        ]);

        return $response;
    }
}
```

## Distributed Locking

```php
// Distributed lock with Redis (Redlock simplified)
class DistributedLock
{
    public function __construct(
        private Redis $redis,
        private string $key,
        private int $ttlMs = 10000  // 10 seconds
    ) {}

    public function acquire(string $ownerId): bool
    {
        // SET NX PX — set if not exists, with expiry in ms
        $result = $this->redis->set(
            "lock:$this->key",
            $ownerId,
            ['NX', 'PX' => $this->ttlMs]
        );
        return $result !== false;
    }

    public function release(string $ownerId): void
    {
        // Lua script — atomic check-and-delete
        $script = '
            if redis.call("get", KEYS[1]) == ARGV[1] then
                return redis.call("del", KEYS[1])
            else
                return 0
            end
        ';

        $this->redis->eval($script, ["lock:$this->key", $ownerId], 1);
    }

    public function synchronize(callable $callback): mixed
    {
        $ownerId = bin2hex(random_bytes(16));

        if (!$this->acquire($ownerId)) {
            throw new \RuntimeException('Could not acquire lock');
        }

        try {
            return $callback();
        } finally {
            $this->release($ownerId);
        }
    }
}
```

## Idempotency

```php
// Ensure operations can be retried without side effects
class IdempotentPaymentProcessor
{
    public function __construct(
        private PaymentGateway $gateway,
        private Redis $cache,
    ) {}

    public function charge(string $idempotencyKey, int $amount): PaymentResult
    {
        // Check if we've already processed this key
        $cached = $this->cache->get("idempotency:$idempotencyKey");
        if ($cached !== false) {
            return unserialize($cached);  // Return previous result
        }

        // Process (will be idempotent on the gateway side too)
        $result = $this->gateway->charge($amount, $idempotencyKey);

        // Cache result for 24 hours
        $this->cache->setex(
            "idempotency:$idempotencyKey",
            86400,
            serialize($result)
        );

        return $result;
    }
}

// Usage
$processor->charge('req_abc123', 100);
// If network times out, retry — same key, same result
$processor->charge('req_abc123', 100);  // Returns cached result
```

## Common Distributed Systems Problems

| Problem | Description | Solution |
|---------|-------------|----------|
| **Network Partition** | Nodes can't talk to each other | Consensus (Raft/Paxos) |
| **Split Brain** | Two nodes both think they're leader | Quorum-based decisions |
| **Clock Skew** | Nodes disagree on time | Use logical clocks (Lamport, Vector clocks) |
| **At-least-once delivery** | Messages delivered multiple times | Idempotent receivers |
| **Exactly-once delivery** | Hard in distributed systems | Idempotency + deduplication |
| **Out-of-order messages** | Messages arrive in wrong order | Sequence numbers, ordering guarantees |
| **Leader Failure** | Current leader crashes | Automatic re-election (Raft) |
| **Transaction across services** | Need atomicity across services | Saga pattern |

## Recommended Reading

| Book | Topic |
|------|-------|
| *Designing Data-Intensive Applications* (Kleppmann) | The definitive distributed systems book |
| *Distributed Systems* (van Steen, Tanenbaum) | Academic fundamentals |
| *Understanding Distributed Systems* (Vitillo) | Practical, hands-on |
| *Database Internals* (Petrov) | Storage engines, consensus |
| *Site Reliability Engineering* (Beyer et al.) | Google's SRE practices |
