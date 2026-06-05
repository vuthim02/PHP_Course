<?php

declare(strict_types=1);

namespace CQRSES\Domain\Command;

use CQRSES\Domain\ValueObject\AccountId;
use CQRSES\Domain\ValueObject\Amount;

/**
 * DepositMoneyCommand
 */
final class DepositMoneyCommand
{
    public function __construct(
        public readonly AccountId $accountId,
        public readonly Amount $amount,
        public readonly string $description = ''
    ) {
    }
}
