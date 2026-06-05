# Chapter 8: Apache Kafka

## Learning Objectives

- Set up Kafka and integrate with PHP
- Produce and consume events
- Handle event streaming
- Implement Kafka Connect

---

## 8.1 Kafka Integration

```php
<?php
// Using rdKafka PHP extension
class KafkaProducer
{
    private \RdKafka\Producer $producer;

    public function __construct()
    {
        $conf = new \RdKafka\Conf();
        $conf->set('bootstrap.servers', $_ENV['KAFKA_BROKERS'] ?? 'localhost:9092');
        $conf->set('acks', 'all');
        $conf->set('compression.type', 'snappy');

        $this->producer = new \RdKafka\Producer($conf);
    }

    public function produce(string $topic, string $key, array $message): void
    {
        $topicObj = $this->producer->newTopic($topic);
        
        $topicObj->produce(
            RD_KAFKA_PARTITION_UA, // Automatic partition
            0,
            json_encode($message),
            $key,
        );

        $this->producer->poll(0);
    }

    public function flush(int $timeoutMs = 10000): void
    {
        $this->producer->flush($timeoutMs);
    }
}

class KafkaConsumer
{
    private \RdKafka\KafkaConsumer $consumer;

    public function __construct(string $groupId, array $topics)
    {
        $conf = new \RdKafka\Conf();
        $conf->set('bootstrap.servers', $_ENV['KAFKA_BROKERS'] ?? 'localhost:9092');
        $conf->set('group.id', $groupId);
        $conf->set('auto.offset.reset', 'earliest');
        $conf->set('enable.auto.commit', 'false');

        $this->consumer = new \RdKafka\KafkaConsumer($conf);
        $this->consumer->subscribe($topics);
    }

    public function consume(int $timeoutMs = 120000): ?\RdKafka\Message
    {
        return $this->consumer->consume($timeoutMs);
    }

    public function commit(\RdKafka\Message $message): void
    {
        $this->consumer->commit($message);
    }
}

// Event streaming for order processing
class OrderEventProcessor
{
    public function __construct(
        private KafkaProducer $producer,
        private OrderService $orders,
    ) {}

    public function processOrder(Order $order): void
    {
        // Publish order events
        $this->producer->produce('orders', $order->id, [
            'event' => 'order.created',
            'order_id' => $order->id,
            'customer_id' => $order->customerId,
            'total' => $order->total,
            'timestamp' => time(),
        ]);
    }
}
```

---

## 8.2 Exercises

1. Install Kafka and set up topics for an event stream
2. Produce events from a PHP application
3. Consume and process events in real-time
4. Implement exactly-once processing semantics

---

## Further Reading

- **Doc:** [Apache Kafka](https://kafka.apache.org/documentation/)
- **Doc:** [RdKafka](https://github.com/arnaud-lb/php-rdkafka)
