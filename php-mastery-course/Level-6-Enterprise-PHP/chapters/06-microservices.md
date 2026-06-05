# Chapter 6: Microservices

## Learning Objectives

- Design microservice architecture
- Implement service communication
- Handle service discovery
- Apply distributed patterns

---

## 6.1 Microservice Patterns

```php
<?php
// Service communication via HTTP
class OrderServiceClient
{
    public function __construct(
        private HttpClient $http,
        private string $baseUrl,
    ) {}

    public function getOrder(string $orderId): OrderDTO
    {
        $response = $this->http->get("{$this->baseUrl}/orders/{$orderId}");
        
        if ($response->getStatusCode() !== 200) {
            throw new ServiceException('Order service unavailable');
        }

        return OrderDTO::fromArray(json_decode($response->getBody(), true));
    }

    public function createOrder(array $data): OrderDTO
    {
        $response = $this->http->post("{$this->baseUrl}/orders", [
            'json' => $data,
        ]);

        return OrderDTO::fromArray(json_decode($response->getBody(), true));
    }
}

// Service boundaries
// ┌─────────────────────────────────────────────────────┐
// │                   API Gateway                        │
// │  /api/users → User Service                          │
// │  /api/orders → Order Service                        │
// │  /api/products → Product Service                    │
// │  /api/payments → Payment Service                    │
// └─────────────────────────────────────────────────────┘
//         │           │            │           │
//    ┌────┴────┐ ┌───┴───┐  ┌────┴────┐ ┌───┴───┐
//    │  User   │ │ Order │  │ Product │ │Payment│
//    │ Service │ │Service│  │ Service │ │Service│
//    └────┬────┘ └───┬───┘  └────┬────┘ └───┬───┘
//         │          │           │          │
//    ┌────┴────┐ ┌───┴───┐  ┌────┴────┐ ┌───┴───┐
//    │  MySQL  │ │Postgre│  │ Mongo   │ │ Stripe│
//    └─────────┘ └───────┘  └─────────┘ └───────┘

// Asynchronous communication via events
class OrderCreatedEvent
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $customerId,
        public readonly float $total,
    ) {}
}

// Publisher
class OrderService
{
    public function __construct(
        private EventPublisher $events,
        private OrderRepository $orders,
    ) {}

    public function placeOrder(array $items, string $customerId): Order
    {
        $order = Order::create($items, $customerId);
        $this->orders->save($order);
        
        $this->events->publish(new OrderCreatedEvent(
            orderId: $order->id,
            customerId: $customerId,
            total: $order->total,
        ));

        return $order;
    }
}

// Consumer in another service
class OrderHandler
{
    public function handle(OrderCreatedEvent $event): void
    {
        // This runs in the Inventory Service
        InventoryService::reserveItems($event->orderId);
    }
}
```

---

## 6.2 Exercises

1. Design microservices for an e-commerce platform
2. Implement synchronous (HTTP) communication between services
3. Use message queues (RabbitMQ) for async communication
4. Handle service failures with circuit breaker pattern

---

## Further Reading

- **Book:** "Building Microservices" by Sam Newman
- **Doc:** [Microservices.io](https://microservices.io/)
