# Chapter 3: Event Sourcing

## Learning Objectives

- Store events as source of truth
- Rebuild state from events
- Implement projections
- Handle event versioning

---

## 3.1 Event Store

```php
<?php
namespace App\EventSourcing;

// Domain Event
class OrderPlaced
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $customerId,
        public readonly array $items,
        public readonly float $total,
        public readonly \DateTimeImmutable $occurredAt,
    ) {}
}

// Event Store
class EventStore
{
    public function __construct(private \PDO $pdo) {}

    public function append(string $aggregateId, DomainEvent $event, int $expectedVersion): void
    {
        $this->pdo->beginTransaction();

        try {
            $currentVersion = $this->getCurrentVersion($aggregateId);
            
            if ($currentVersion !== $expectedVersion) {
                throw new ConcurrencyException(
                    "Expected version {$expectedVersion}, got {$currentVersion}"
                );
            }

            $stmt = $this->pdo->prepare(
                'INSERT INTO events (aggregate_id, aggregate_type, event_type, version, data, occurred_at)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            
            $stmt->execute([
                $aggregateId,
                $this->getAggregateType($event),
                get_class($event),
                $expectedVersion + 1,
                serialize($event),
                $event->occurredAt->format('Y-m-d H:i:s.u'),
            ]);

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getEvents(string $aggregateId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM events WHERE aggregate_id = ? ORDER BY version ASC'
        );
        $stmt->execute([$aggregateId]);
        
        return array_map(
            fn($row) => unserialize($row['data']),
            $stmt->fetchAll()
        );
    }

    public function replayAll(): void
    {
        $stmt = $this->pdo->query(
            'SELECT * FROM events ORDER BY aggregate_id, version ASC'
        );
        
        $currentId = null;
        $aggregate = null;

        foreach ($stmt->fetchAll() as $row) {
            $event = unserialize($row['data']);
            
            if ($row['aggregate_id'] !== $currentId) {
                if ($aggregate) {
                    $this->saveProjection($aggregate);
                }
                $aggregate = $this->createAggregate($row['aggregate_type']);
                $currentId = $row['aggregate_id'];
            }

            $aggregate->apply($event);
        }

        if ($aggregate) {
            $this->saveProjection($aggregate);
        }
    }

    // Schema: CREATE TABLE events (
    //     id BIGINT AUTO_INCREMENT PRIMARY KEY,
    //     aggregate_id VARCHAR(36) NOT NULL,
    //     aggregate_type VARCHAR(100) NOT NULL,
    //     event_type VARCHAR(255) NOT NULL,
    //     version INT NOT NULL,
    //     data LONGBLOB NOT NULL,
    //     occurred_at DATETIME(6) NOT NULL,
    //     INDEX idx_aggregate (aggregate_id, version)
    // );
}
```

---

## 3.2 Exercises

1. Implement an event store with MySQL
2. Create projections for query models
3. Rebuild state from events on demand
4. Handle event versioning and migrations

---

## Further Reading

- **Book:** "Implementing Event Sourcing" by Vaughn Vernon
- **Resource:** [Event Sourcing PHP](https://event-sourcing-php.com/)
