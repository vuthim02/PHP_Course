<?php

declare(strict_types=1);

namespace CQRSES\Infrastructure\Projection;

use CQRSES\Domain\ValueObject\AccountId;
use CQRSES\ReadModel\AccountBalance;
use CQRSES\Domain\Event\AccountOpenedEvent;
use CQRSES\Domain\Event\MoneyDepositedEvent;
use CQRSES\Domain\Event\MoneyWithdrawnEvent;
use CQRSES\Domain\Event\AccountClosedEvent;

/**
 * AccountBalanceProjector
 *
 * Projection that builds and maintains the AccountBalance read model.
 * In Event Sourcing, projections read events and produce denormalized
 * views optimized for specific query use cases.
 *
 * This projector could be rebuilt from scratch by replaying all events,
 * making it resilient to data corruption.
 */
final class AccountBalanceProjector
{
    /** @var array<string, AccountBalance> */
    private array $balances = [];

    public function projectAccountOpened(AccountOpenedEvent $event): void
    {
        $id = (string) $event->accountId;
        $this->balances[$id] = new AccountBalance(
            $id,
            $event->ownerName,
            $event->currency,
            0,
            'active'
        );
    }

    public function projectMoneyDeposited(MoneyDepositedEvent $event): void
    {
        $id = (string) $event->accountId;
        if (isset($this->balances[$id])) {
            $balance = $this->balances[$id];
            $this->balances[$id] = new AccountBalance(
                $balance->accountId,
                $balance->ownerName,
                $balance->currency,
                $event->newBalance,
                $balance->status
            );
        }
    }

    public function projectMoneyWithdrawn(MoneyWithdrawnEvent $event): void
    {
        $id = (string) $event->accountId;
        if (isset($this->balances[$id])) {
            $balance = $this->balances[$id];
            $this->balances[$id] = new AccountBalance(
                $balance->accountId,
                $balance->ownerName,
                $balance->currency,
                $event->newBalance,
                $balance->status
            );
        }
    }

    public function projectAccountClosed(AccountClosedEvent $event): void
    {
        $id = (string) $event->accountId;
        if (isset($this->balances[$id])) {
            $balance = $this->balances[$id];
            $this->balances[$id] = new AccountBalance(
                $balance->accountId,
                $balance->ownerName,
                $balance->currency,
                $balance->balanceCents,
                'closed'
            );
        }
    }

    public function getBalance(AccountId $id): ?AccountBalance
    {
        return $this->balances[(string) $id] ?? null;
    }

    public function allBalances(): array
    {
        return array_values($this->balances);
    }
}
