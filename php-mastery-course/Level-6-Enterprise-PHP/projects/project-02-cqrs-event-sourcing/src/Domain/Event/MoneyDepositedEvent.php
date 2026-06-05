<?php

declare(strict_types=1);

namespace CQRSES\Domain\Event;

use CQRSES\Domain\ValueObject\AccountId;
use CQRSES\Domain\ValueObject\Amount;

/**
 * MoneyDepositedEvent
 */
final class MoneyDepositedEvent
{
    public function __construct(
        public readonly AccountId $accountId,
        public readonly Amount $amount,
        public readonly int $newBalance,
        public readonly string $description,
        public readonly float $occurredOn = 0.0
    ) {
    }
}
