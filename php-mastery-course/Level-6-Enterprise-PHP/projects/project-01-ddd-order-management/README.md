# Project 1: DDD-based Order Management System

## Description
A domain-driven design order management system demonstrating tactical DDD patterns: bounded contexts, aggregates, entities, value objects, domain events, repositories, domain services, and hexagonal architecture.

## Enterprise Architecture Concepts
- **Domain-Driven Design** — Rich domain model with ubiquitous language
- **Bounded Contexts** — Ordering, Payment, Shipping, Billing
- **Hexagonal Architecture** — Domain core isolated from infrastructure
- **Domain Events** — Decoupled side-effect handling via event bus
- **Repository Pattern** — Interface-based persistence abstraction
- **Value Objects** — Immutable types (Money, Address, OrderId)
- **Application Services** — Transactional boundaries and use-case orchestration

## Architecture Diagram
```
┌─────────────────────────────────────────────────────────┐
│                     Interface Layer                      │
│   CLI (OrderCommand)         HTTP (OrderController)     │
└──────────────────────┬──────────────────────────────────┘
                       │
┌──────────────────────▼──────────────────────────────────┐
│                  Application Layer                       │
│      OrderService       PaymentService     Query        │
└──────┬────────────┬────────────┬───────────────────────┘
       │            │            │
┌──────▼────┐ ┌─────▼─────┐ ┌───▼────────────────────┐
│  Domain   │ │  Domain   │ │      Shared Kernel      │
│ Ordering  │ │  Payment  │ │ Money, Address, Events  │
│ Aggregate │ │  Entity   │ │                         │
└──────┬────┘ └─────┬─────┘ └─────────────────────────┘
       │            │
┌──────▼────────────▼─────────────────────────────────┐
│                Infrastructure Layer                   │
│  DoctrineOrderRepository  InMemoryEventBus          │
│  MockShippingService      MockPaymentGateway        │
└─────────────────────────────────────────────────────┘
```

## Setup Instructions
```bash
cd project-01-ddd-order-management
composer install
```

### Run CLI Demo
```bash
php src/Interface/Cli/OrderCommand.php
```

### Run Tests
```bash
php vendor/bin/phpunit tests/
```

## How to Use
- `OrderService::placeOrder()` — Creates order, publishes `OrderPlacedEvent`
- `OrderService::cancelOrder()` — Cancels with payment refund via domain events
- `OrderQuery::getOrder()` — Read-side order projection (future CQRS extension point)
- Event subscribers react: PaymentService charges, ShippingService dispatches

## Testing & Quality
- PHPUnit tests for domain entities, value objects, application services
- In-memory repositories for fast, deterministic tests
- Event bus verification — assert expected events published
- No external dependencies — pure PHP domain logic
