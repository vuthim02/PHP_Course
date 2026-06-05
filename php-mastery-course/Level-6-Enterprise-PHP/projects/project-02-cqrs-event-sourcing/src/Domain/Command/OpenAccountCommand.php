<?php

declare(strict_types=1);

namespace CQRSES\Domain\Command;

use CQRSES\Domain\ValueObject\AccountId;
use CQRSES\Domain\ValueObject\Currency;

/**
 * OpenAccountCommand
 *
 * Command to open a new bank account.
 * Commands are imperative (do something) — named with verb + noun.
 */
final class OpenAccountCommand
{
    public function __construct(
        public readonly AccountId $accountId,
        public readonly string $ownerName,
        public readonly Currency $currency
    ) {
    }
}
