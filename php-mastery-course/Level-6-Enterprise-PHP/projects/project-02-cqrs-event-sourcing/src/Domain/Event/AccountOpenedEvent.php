<?php

declare(strict_types=1);

namespace CQRSES\Domain\Event;

use CQRSES\Domain\ValueObject\AccountId;
use CQRSES\Domain\ValueObject\Currency;

/**
 * AccountOpenedEvent
 *
 * Events are named in past tense — something that already happened.
 * They are immutable records of fact.
 */
final class AccountOpenedEvent
{
    public function __construct(
        public readonly AccountId $accountId,
        public readonly string $ownerName,
        public readonly Currency $currency,
        public readonly float $occurredOn = 0.0
    ) {
    }
}
