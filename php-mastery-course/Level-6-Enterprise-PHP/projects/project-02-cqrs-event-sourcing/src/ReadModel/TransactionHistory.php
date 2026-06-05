<?php

declare(strict_types=1);

namespace CQRSES\ReadModel;

/**
 * TransactionHistory Read Model
 *
 * Optimized view for transaction history queries.
 * In CQRS, the read model can join, denormalize, or cache data
 * however the query use case demands.
 */
final class TransactionHistory
{
    /**
     * @param string $accountId
     * @param array[] $transactions
     */
    public function __construct(
        public readonly string $accountId,
        public readonly array $transactions
    ) {
    }

    public function count(): int
    {
        return count($this->transactions);
    }

    public function lastTransaction(): ?array
    {
        return !empty($this->transactions)
            ? $this->transactions[count($this->transactions) - 1]
            : null;
    }
}
