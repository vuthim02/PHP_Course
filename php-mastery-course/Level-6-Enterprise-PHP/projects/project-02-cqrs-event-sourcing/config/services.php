<?php

declare(strict_types=1);

/**
 * Service configuration for CQRS/ES Bank.
 *
 * Defines the wiring between commands, events, handlers, and projectors.
 * This is the composition root where infrastructure dependencies are injected.
 */

return [
    // Event Store
    \CQRSES\Infrastructure\EventStore\FileEventStore::class => fn() => new \CQRSES\Infrastructure\EventStore\FileEventStore(),

    // Buses
    \CQRSES\Infrastructure\Bus\SimpleCommandBus::class => fn() => new \CQRSES\Infrastructure\Bus\SimpleCommandBus(),
    \CQRSES\Infrastructure\Bus\SimpleEventBus::class => fn() => new \CQRSES\Infrastructure\Bus\SimpleEventBus(),
    \CQRSES\Infrastructure\Bus\SimpleQueryBus::class => fn() => new \CQRSES\Infrastructure\Bus\SimpleQueryBus(),

    // Application Service
    \CQRSES\Application\AccountService::class => function ($c) {
        return new \CQRSES\Application\AccountService(
            $c[\CQRSES\Infrastructure\EventStore\FileEventStore::class](),
            $c[\CQRSES\Infrastructure\Bus\SimpleEventBus::class]()
        );
    },

    // Projectors
    \CQRSES\Infrastructure\Projection\AccountBalanceProjector::class => fn() => new \CQRSES\Infrastructure\Projection\AccountBalanceProjector(),
    \CQRSES\Infrastructure\Projection\TransactionHistoryProjector::class => fn() => new \CQRSES\Infrastructure\Projection\TransactionHistoryProjector(),
];
