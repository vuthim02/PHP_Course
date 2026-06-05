<?php

declare(strict_types=1);

namespace DDD\Infrastructure\Persistence;

use DDD\Domain\Order\Order;
use DDD\Domain\Order\OrderId;
use DDD\Domain\Order\OrderRepositoryInterface;

/**
 * DoctrineOrderRepository
 *
 * In-memory implementation of OrderRepositoryInterface for development/testing.
 * In production this would use Doctrine ORM with MySQL/PostgreSQL.
 * The repository abstracts all storage concerns behind a domain interface,
 * keeping the domain layer completely infrastructure-agnostic.
 */
final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    /** @var array<string, Order> */
    private array $orders = [];

    public function save(Order $order): void
    {
        // In production: $this->entityManager->persist($order);
        $this->orders[(string) $order->id()] = $order;
    }

    public function getById(OrderId $id): ?Order
    {
        // In production: $this->entityManager->find(Order::class, $id);
        return $this->orders[(string) $id] ?? null;
    }

    public function findByCustomerId(string $customerId): array
    {
        // In production: $this->entityManager->createQuery(...)
        return array_values(
            array_filter(
                $this->orders,
                fn(Order $o) => $o->customerId() === $customerId
            )
        );
    }

    public function delete(OrderId $id): void
    {
        // In production: $this->entityManager->remove(...)
        unset($this->orders[(string) $id]);
    }
}
