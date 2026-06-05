<?php

declare(strict_types=1);

namespace DDD\Domain\Order;

/**
 * OrderRepositoryInterface
 *
 * Repository pattern — abstracts persistence for the Order aggregate.
 * The domain layer defines the interface; infrastructure provides implementation.
 * This allows the domain to remain completely ignorant of storage concerns.
 */
interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function getById(OrderId $id): ?Order;
    public function findByCustomerId(string $customerId): array;
    public function delete(OrderId $id): void;
}
