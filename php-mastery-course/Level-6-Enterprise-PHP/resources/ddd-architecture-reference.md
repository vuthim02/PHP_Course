# Domain-Driven Design & Enterprise Architecture — Reference

## DDD Concepts

| Concept | Definition | Code Example |
|---------|------------|--------------|
| **Entity** | Object with identity (ID) that persists over time | `User`, `Order`, `Product` |
| **Value Object** | Immutable, no identity, compared by value | `Email`, `Money`, `Address` |
| **Aggregate** | Cluster of entities with one root | `Order` (root) + `OrderLine` entities |
| **Repository** | Collection-like interface for aggregates | `UserRepository`, `OrderRepository` |
| **Domain Service** | Stateless logic that doesn't fit an entity | `PriceCalculator`, `PaymentProcessor` |
| **Domain Event** | Something that happened in the domain | `OrderPlaced`, `UserRegistered` |
| **Specification** | Business rule predicate | `CustomerIsEligibleForDiscount` |

## Hexagonal Architecture (Ports & Adapters)

```
[ Inbound Adapters ]         [ Application Core ]         [ Outbound Adapters ]
                          
  HTTP Controller -------->                        --------> MySQL Repository
  CLI Command     -------->  Use Cases     <-------- --------> Redis Cache
  AMQP Consumer   -------->  Domain Model   <-------- --------> Email Service
  GraphQL         -------->                        --------> HTTP Client
```

```php
// Core — no dependencies on framework, database, or web
namespace App\Domain\User\UseCase;

use App\Domain\User\Entity\User;
use App\Domain\User\Port\PasswordHasherInterface;
use App\Domain\User\Port\UserRepositoryInterface;

class RegisterUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepo,
        private PasswordHasherInterface $passwordHasher,
    ) {}

    public function execute(RegisterUserRequest $request): User
    {
        $this->ensureEmailIsUnique($request->email);

        $user = User::register(
            $request->email,
            $this->passwordHasher->hash($request->password),
            $request->name
        );

        $this->userRepo->save($user);

        return $user;
    }

    private function ensureEmailIsUnique(string $email): void
    {
        if ($this->userRepo->findByEmail($email) !== null) {
            throw new \DomainException('Email already registered');
        }
    }
}

// Port (interface) — defined in core
namespace App\Domain\User\Port;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findByEmail(string $email): ?User;
    public function findById(int $id): ?User;
}

// Adapter (implementation) — outside core
namespace App\Infrastructure\Persistence;

use App\Domain\User\Entity\User;
use App\Domain\User\Port\UserRepositoryInterface;

class MySQLUserRepository implements UserRepositoryInterface
{
    public function __construct(private \PDO $pdo) {}

    public function save(User $user): void
    {
        // MySQL-specific implementation
    }

    public function findByEmail(string $email): ?User
    {
        // MySQL-specific implementation
    }
}
```

## Value Objects

```php
// Immutable, self-validating, no identity
final class Email
{
    private function __construct(
        private readonly string $value
    ) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
    }

    public static function fromString(string $value): self
    {
        return new self(strtolower(trim($value)));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

final class Money
{
    public function __construct(
        private readonly int $amount,  // Stored as cents
        private readonly string $currency
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount must be non-negative');
        }
        if (strlen($currency) !== 3) {
            throw new \InvalidArgumentException('Invalid currency code');
        }
    }

    public static function fromFloat(float $amount, string $currency): self
    {
        return new self((int) round($amount * 100), strtoupper($currency));
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException('Cannot add different currencies');
        }
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function multiply(int $factor): self
    {
        return new self($this->amount * $factor, $this->currency);
    }

    public function toFloat(): float
    {
        return $this->amount / 100;
    }
}
```

## Aggregates

```php
// Aggregate Root — entry point for all operations on the aggregate
class Order
{
    /** @var OrderLine[] */
    private array $lines;
    private OrderStatus $status;
    private Money $total;

    private function __construct(
        private readonly string $orderId,
        private readonly string $customerId,
        array $lines
    ) {
        $this->status = OrderStatus::PENDING;
        $this->lines = [];
        foreach ($lines as $line) {
            $this->addLine($line);
        }
    }

    public static function create(
        string $customerId,
        array $lines
    ): self {
        return new self(uniqid('ord_', true), $customerId, $lines);
    }

    public function addLine(OrderLine $line): void
    {
        foreach ($this->lines as $existing) {
            if ($existing->productId() === $line->productId()) {
                throw new \DomainException('Product already in order');
            }
        }
        $this->lines[] = $line;
        $this->recalculateTotal();
    }

    public function removeLine(string $productId): void
    {
        $this->lines = array_values(array_filter(
            $this->lines,
            fn(OrderLine $l) => $l->productId() !== $productId
        ));
        $this->recalculateTotal();
    }

    public function submit(): void
    {
        if (empty($this->lines)) {
            throw new \DomainException('Cannot submit empty order');
        }
        $this->status = OrderStatus::SUBMITTED;
        // Record domain event
        DomainEventRecorder::record(new OrderSubmitted($this->orderId));
    }

    private function recalculateTotal(): void
    {
        $total = Money::fromFloat(0, 'USD');
        foreach ($this->lines as $line) {
            $total = $total->add($line->subtotal());
        }
        $this->total = $total;
    }

    // Getters (no setters — behavior controls state)
    public function id(): string { return $this->orderId; }
    public function status(): OrderStatus { return $this->status; }
}

// Entity within the aggregate
class OrderLine
{
    public function __construct(
        private readonly string $productId,
        private readonly string $productName,
        private readonly Money $unitPrice,
        private int $quantity
    ) {}

    public function changeQuantity(int $quantity): void
    {
        if ($quantity < 1) {
            throw new \DomainException('Quantity must be at least 1');
        }
        $this->quantity = $quantity;
    }

    public function subtotal(): Money
    {
        return $this->unitPrice->multiply($this->quantity);
    }

    public function productId(): string { return $this->productId; }
}

// Enum for status (PHP 8.1+)
enum OrderStatus: string
{
    case PENDING = 'pending';
    case SUBMITTED = 'submitted';
    case CONFIRMED = 'confirmed';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
}
```

## Repository Pattern

```php
// Repository interface — defined in domain layer
interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function findById(string $orderId): ?Order;
    public function findByCustomer(string $customerId): array;
    public function delete(string $orderId): void;
}

// Implementation — infrastructure layer
class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function save(Order $order): void
    {
        $this->em->persist($order);
        $this->em->flush();
    }

    public function findById(string $orderId): ?Order
    {
        return $this->em->find(Order::class, $orderId);
    }

    public function findByCustomer(string $customerId): array
    {
        return $this->em
            ->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o')
            ->where('o.customerId = :customerId')
            ->setParameter('customerId', $customerId)
            ->getQuery()
            ->getResult();
    }

    public function delete(string $orderId): void
    {
        $order = $this->findById($orderId);
        if ($order) {
            $this->em->remove($order);
            $this->em->flush();
        }
    }
}
```

## Domain Events

```php
// Event class — immutable data object
class OrderSubmitted
{
    public readonly \DateTimeImmutable $occurredAt;

    public function __construct(
        public readonly string $orderId,
        public readonly string $customerId,
        public readonly Money $total
    ) {
        $this->occurredAt = new \DateTimeImmutable();
    }
}

// Event recorder — collects events during a transaction
class DomainEventRecorder
{
    private static array $events = [];

    public static function record(object $event): void
    {
        self::$events[] = $event;
    }

    /** @return object[] */
    public static function release(): array
    {
        $events = self::$events;
        self::$events = [];
        return $events;
    }
}

// Event dispatcher — publishes events to subscribers
class DomainEventDispatcher
{
    /** @var array<string, callable[]> */
    private array $handlers = [];

    public function register(string $eventClass, callable $handler): void
    {
        $this->handlers[$eventClass][] = $handler;
    }

    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            $class = get_class($event);
            foreach ($this->handlers[$class] ?? [] as $handler) {
                $handler($event);
            }
        }
    }
}

// Usage in application service
class ApplicationService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepo,
        private DomainEventDispatcher $eventDispatcher,
    ) {}

    public function submitOrder(string $orderId): void
    {
        $order = $this->orderRepo->findById($orderId);
        if (!$order) {
            throw new \DomainException('Order not found');
        }

        $order->submit();
        $this->orderRepo->save($order);

        // Dispatch collected events
        $events = DomainEventRecorder::release();
        $this->eventDispatcher->dispatch($events);
    }
}
```

## CQRS — Command Query Responsibility Segregation

```php
// Command — changes state (write)
class CreateOrderCommand
{
    public function __construct(
        public readonly string $customerId,
        public readonly array $items, // ['productId' => quantity, ...]
    ) {}
}

// Query — reads state (read)
class GetOrderQuery
{
    public function __construct(
        public readonly string $orderId,
    ) {}
}

// Command Handler
class CreateOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $writeRepo,
    ) {}

    public function handle(CreateOrderCommand $command): void
    {
        $lines = [];
        foreach ($command->items as $productId => $quantity) {
            $product = $this->productRepo->findById($productId);
            $lines[] = new OrderLine(
                $productId,
                $product->name(),
                $product->price(),
                $quantity
            );
        }

        $order = Order::create($command->customerId, $lines);
        $this->writeRepo->save($order);
    }
}

// Query Handler (separate read model, possibly denormalized)
class GetOrderHandler
{
    public function __construct(
        private OrderReadModelRepositoryInterface $readRepo,
    ) {}

    public function handle(GetOrderQuery $query): ?OrderReadModel
    {
        return $this->readRepo->findById($query->orderId);
    }
}

// Separate read model — denormalized for fast queries
class OrderReadModel
{
    public function __construct(
        public readonly string $id,
        public readonly string $customerName,
        public readonly string $status,
        public readonly float $total,
        public readonly array $items, // Pre-joined, flat data
        public readonly \DateTimeImmutable $createdAt,
    ) {}
}
```

## Clean Architecture Dependency Rule

```
Dependencies point INWARD (toward domain)

[ Framework / Web / DB / External ]
            ↓ depends on
[ Application Services / Use Cases ]
            ↓ depends on
[ Domain Model (Entities, Value Objects) ]
```

```php
// ✅ CORRECT: Domain layer knows nothing about framework
namespace App\Domain\User\Entity;

class User
{
    public function __construct(
        public readonly UserId $id,
        public readonly Email $email,
        public readonly HashedPassword $password,
    ) {}
}

// ✅ CORRECT: Application service depends on interfaces (ports)
namespace App\Application\User\UseCase;

class RegisterUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $repo,   // Port
        private PasswordHasherInterface $hasher, // Port
    ) {}
}

// ✅ CORRECT: Infrastructure implements interfaces
namespace App\Infrastructure\User\Persistence;

class EloquentUserRepository implements UserRepositoryInterface
{
    // Eloquent-specific implementation
}
```

## Directory Structure

```
src/
├── Domain/                      # Inner circle — pure PHP
│   ├── User/
│   │   ├── Entity/
│   │   │   └── User.php
│   │   ├── ValueObject/
│   │   │   ├── Email.php
│   │   │   ├── UserId.php
│   │   │   └── HashedPassword.php
│   │   ├── Event/
│   │   │   └── UserRegistered.php
│   │   ├── Port/
│   │   │   ├── UserRepositoryInterface.php
│   │   │   └── PasswordHasherInterface.php
│   │   └── Specification/
│   │       └── CustomerIsActive.php
│   └── Order/
│       ├── Entity/
│       │   ├── Order.php
│       │   └── OrderLine.php
│       └── ...
├── Application/                 # Use cases / application services
│   ├── User/
│   │   ├── UseCase/
│   │   │   └── RegisterUserUseCase.php
│   │   ├── Command/
│   │   │   └── RegisterUserCommand.php
│   │   └── DTO/
│   │       └── UserResponse.php
│   └── Order/
│       └── ...
└── Infrastructure/              # Outer circle — framework, DB, etc.
    ├── User/
    │   ├── Persistence/
    │   │   └── MySQLUserRepository.php
    │   └── Auth/
    │       └── BcryptPasswordHasher.php
    ├── Event/
    │   └── RabbitMQEventBus.php
    └── Framework/
        ├── Laravel/
        │   └── UserController.php
        └── Symfony/
            └── UserController.php
```
