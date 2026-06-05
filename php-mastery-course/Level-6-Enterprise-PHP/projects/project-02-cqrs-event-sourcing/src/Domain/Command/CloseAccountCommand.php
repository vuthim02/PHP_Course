<?php

declare(strict_types=1);

namespace CQRSES\Domain\Command;

use CQRSES\Domain\ValueObject\AccountId;

/**
 * CloseAccountCommand
 */
final class CloseAccountCommand
{
    public function __construct(
        public readonly AccountId $accountId,
        public readonly string $reason = ''
    ) {
    }
}
