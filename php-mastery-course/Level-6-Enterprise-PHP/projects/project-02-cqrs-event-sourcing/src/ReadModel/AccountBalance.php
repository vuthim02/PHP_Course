<?php

declare(strict_types=1);

namespace CQRSES\ReadModel;

use CQRSES\Domain\ValueObject\Currency;

/**
 * AccountBalance Read Model
 *
 * A denormalized view optimized for balance queries.
 * In CQRS, read models are separate from the domain model and can
 * be structured differently to optimize specific query patterns.
 */
final class AccountBalance
{
    public function __construct(
        public readonly string $accountId,
        public readonly string $ownerName,
        public readonly Currency $currency,
        public readonly int $balanceCents,
        public readonly string $status
    ) {
    }

    public function balanceFormatted(): string
    {
        return sprintf('%s %01.2f', $this->currency, $this->balanceCents / 100);
    }
}
