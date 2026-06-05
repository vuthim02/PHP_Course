# Chapter 4: Hexagonal Architecture

## Learning Objectives

- Implement ports and adapters
- Isolate domain from infrastructure
- Build testable application cores
- Apply dependency inversion

---

```mermaid
flowchart TD
    subgraph Inbound Adapters
        HTTP[HTTP Controller]
        CLI[CLI Command]
        GRPC[gRPC Listener]
        AMQP[AMQP Consumer]
    end

    subgraph Application Core [Hexagonal Core]
        subgraph Ports
            IP[Inbound Ports:<br/>Use Case Interfaces]
            OP[Outbound Ports:<br/>Repository Interfaces]
        end
        subgraph Domain
            ENT[Entities]
            VO[Value Objects]
            AGG[Aggregates]
            SRV[Domain Services]
        end
    end

    subgraph Outbound Adapters
        DB[MySQL Repository]
        CACHE[Redis Cache]
        HTTP_OUT[HTTP Client]
        MAIL[Email Service]
    end

    HTTP -->|Calls| IP
    CLI -->|Calls| IP
    GRPC -->|Calls| IP
    AMQP -->|Calls| IP
    IP -->|Implemented by| Domain
    Domain -->|Uses| OP
    OP -->|Implemented by| DB
    OP -->|Implemented by| CACHE
    OP -->|Implemented by| HTTP_OUT
    OP -->|Implemented by| MAIL

    note[Core depends on interfaces (ports).\nAdapters implement those interfaces.\nNo framework/DB code in core.]
```

## 4.1 Hexagonal Architecture

```php
<?php
// Domain (core, no external dependencies)
namespace App\Domain;

interface OrderRepository
{
    // Port
    public function save(Order $order): void;
    public function find(OrderId $id): ?Order;
}

class OrderService
{
    public function __construct(
        private OrderRepository $orders,  // Port (interface)
        private PaymentGateway $payments,  // Port (interface)
    ) {}

    public function placeOrder(Cart $cart, PaymentDetails $payment): Order
    {
        $order = Order::create($cart);
        $this->orders->save($order);
        $this->payments->charge($order->total(), $payment);
        return $order;
    }
}

// Infrastructure (adapters)
namespace App\Infrastructure\Persistence;

use App\Domain\OrderRepository;
use App\Domain\Order;

class MySQLOrderRepository implements OrderRepository  // Adapter
{
    public function __construct(private \PDO $pdo) {}

    public function save(Order $order): void
    {
        // SQL implementation
    }

    public function find(OrderId $id): ?Order
    {
        // SQL implementation
    }
}

namespace App\Infrastructure\Payment;

use App\Domain\PaymentGateway;

class StripePaymentGateway implements PaymentGateway  // Adapter
{
    public function __construct(private string $apiKey) {}

    public function charge(float $amount, PaymentDetails $details): void
    {
        // Stripe API implementation
    }
}

// Application (configuration/wiring)
namespace App\Application;

class Kernel
{
    public static function bootstrap(): OrderService
    {
        $pdo = new \PDO($_ENV['DATABASE_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        
        return new OrderService(
            new MySQLOrderRepository($pdo),
            new StripePaymentGateway($_ENV['STRIPE_KEY']),
        );
    }
}
```

---

## 4.2 Exercises

1. Refactor a legacy controller to hexagonal architecture
2. Create ports for notification, logging, and caching
3. Swap infrastructure adapters (MySQL → PostgreSQL, Stripe → PayPal)
4. Test domain logic with mocked adapters

---

## Further Reading

- **Book:** "Hexagonal Architecture" by Alistair Cockburn
- **Resource:** [Ports & Adapters](https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together/)
