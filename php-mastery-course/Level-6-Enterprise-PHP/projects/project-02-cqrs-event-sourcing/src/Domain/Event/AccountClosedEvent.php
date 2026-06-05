<?php

declare(strict_types=1);

namespace CQRSES\Domain\Event;

use CQRSES\Domain\ValueObject\AccountId;

/**
 * AccountClosedEvent
 */
final class AccountClosedEvent
{
    public function __construct(
        public readonly AccountId $accountId,
        public readonly string $reason,
        public readonly int $finalBalance,
        public readonly float $occurredOn = 0.0
    ) {
    }
}
