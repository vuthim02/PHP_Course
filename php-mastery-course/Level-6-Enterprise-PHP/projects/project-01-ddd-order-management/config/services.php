<?php

declare(strict_types=1);

/**
 * Service container configuration for the DDD Order Management System.
 *
 * In a production application, this would use a DI container
 * (PHP-DI, Symfony DI, or similar). For this demonstration,
 * we manually wire dependencies — making the dependency graph explicit.
 *
 * This file serves as the composition root where all infrastructure
 * adapters are wired to domain ports (interfaces).
 */

return [
    // Repositories
    \DDD\Domain\Order\OrderRepositoryInterface::class => fn() => new \DDD\Infrastructure\Persistence\DoctrineOrderRepository(),
    \DDD\Domain\Payment\PaymentRepositoryInterface::class => fn() => new \DDD\Infrastructure\Persistence\InMemoryPaymentRepository(),

    // Infrastructure Services
    \DDD\Infrastructure\Services\MockPaymentGateway::class => fn() => new \DDD\Infrastructure\Services\MockPaymentGateway(),
    \DDD\Domain\Shipping\ShippingServiceInterface::class => fn() => new \DDD\Infrastructure\Services\MockShippingService(),

    // Event Bus
    \DDD\Infrastructure\Event\InMemoryEventBus::class => fn() => new \DDD\Infrastructure\Event\InMemoryEventBus(),

    // Application Services
    \DDD\Application\OrderService::class => function ($c) {
        return new \DDD\Application\OrderService(
            $c[\DDD\Domain\Order\OrderRepositoryInterface::class](),
            $c[\DDD\Infrastructure\Event\InMemoryEventBus::class]()
        );
    },
    \DDD\Application\PaymentService::class => function ($c) {
        return new \DDD\Application\PaymentService(
            $c[\DDD\Domain\Payment\PaymentRepositoryInterface::class](),
            $c[\DDD\Infrastructure\Services\MockPaymentGateway::class]()
        );
    },
    \DDD\Application\Query\OrderQuery::class => function ($c) {
        return new \DDD\Application\Query\OrderQuery(
            $c[\DDD\Domain\Order\OrderRepositoryInterface::class]()
        );
    },
];
