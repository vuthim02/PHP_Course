# Chapter 7: Message Queues (RabbitMQ)

## Learning Objectives

- Set up RabbitMQ for PHP
- Implement producer/consumer patterns
- Handle message routing
- Ensure message reliability

---

## 7.1 RabbitMQ Integration

```php
<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    private AMQPStreamConnection $connection;
    private \PhpAmqpLib\Channel\AMQPChannel $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            $_ENV['RABBITMQ_HOST'],
            (int)$_ENV['RABBITMQ_PORT'],
            $_ENV['RABBITMQ_USER'],
            $_ENV['RABBITMQ_PASS'],
        );
        $this->channel = $this->connection->channel();
    }

    public function declareQueue(string $name, array $options = []): void
    {
        $this->channel->queue_declare(
            $name,
            false,
            true,   // durable
            false,
            false,
            false,
            $options
        );
    }

    public function publish(string $queue, array $message, string $routingKey = ''): void
    {
        $msg = new AMQPMessage(json_encode($message), [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'content_type' => 'application/json',
            'timestamp' => time(),
        ]);

        $this->channel->basic_publish($msg, '', $queue);
    }

    public function consume(string $queue, callable $callback, string $consumerTag = ''): void
    {
        $this->channel->basic_qos(null, 1, null);

        $this->channel->basic_consume(
            $queue,
            $consumerTag,
            false,
            false,  // no_ack — we'll ack manually
            false,
            false,
            function (AMQPMessage $msg) use ($callback) {
                try {
                    $data = json_decode($msg->body, true);
                    $callback($data);
                    $msg->ack();
                } catch (\Exception $e) {
                    error_log("Failed processing message: {$e->getMessage()}");
                    $msg->nack(false, true); // Re-queue
                }
            }
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}

// Producer
$rabbit = new RabbitMQService();
$rabbit->declareQueue('order_processing');
$rabbit->publish('order_processing', [
    'order_id' => 'ORD-123',
    'customer_email' => 'customer@example.com',
    'total' => 99.99,
]);
```

---

## 7.2 Exercises

1. Set up RabbitMQ and create a producer/consumer pair
2. Implement delayed retry with dead letter queues
3. Use exchange types (direct, topic, fanout) for routing
4. Build a reliable job queue with manual acknowledgments

---

## Further Reading

- **Doc:** [RabbitMQ Tutorial](https://www.rabbitmq.com/tutorials/tutorial-one-php.html)
- **Doc:** [php-amqplib](https://github.com/php-amqplib/php-amqplib)
