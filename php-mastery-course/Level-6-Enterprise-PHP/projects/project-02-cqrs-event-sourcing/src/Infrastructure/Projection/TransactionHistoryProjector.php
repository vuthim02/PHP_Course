<?php

declare(strict_types=1);

namespace CQRSES\Infrastructure\Projection;

use CQRSES\Domain\ValueObject\AccountId;
use CQRSES\ReadModel\TransactionHistory;
use CQRSES\Domain\Event\AccountOpenedEvent;
use CQRSES\Domain\Event\MoneyDepositedEvent;
use CQRSES\Domain\Event\MoneyWithdrawnEvent;
use CQRSES\Domain\Event\AccountClosedEvent;

/**
 * TransactionHistoryProjector
 *
 * Builds a transaction history read model from events.
 * Multiple projections can subscribe to the same events — each
 * maintains a view optimized for different query needs.
 */
final class TransactionHistoryProjector
{
    /** @var array<string, array> */
    private array $transactions = [];

    public function projectAccountOpened(AccountOpenedEvent $event): void
    {
        $id = (string) $event->accountId;
        $this->transactions[$id][] = [
            'type' => 'account_opened',
            'amount' => 0,
            'balance' => 0,
            'description' => "Account opened for {$event->ownerName}",
            'date' => date('c', (int) $event->occurredOn),
        ];
    }

    public function projectMoneyDeposited(MoneyDepositedEvent $event): void
    {
        $id = (string) $event->accountId;
        $this->transactions[$id][] = [
            'type' => 'deposit',
            'amount' => $event->amount->toFloat(),
            'balance' => $event->newBalance / 100,
            'description' => $event->description ?: 'Deposit',
            'date' => date('c', (int) $event->occurredOn),
        ];
    }

    public function projectMoneyWithdrawn(MoneyWithdrawnEvent $event): void
    {
        $id = (string) $event->accountId;
        $this->transactions[$id][] = [
            'type' => 'withdrawal',
            'amount' => -$event->amount->toFloat(),
            'balance' => $event->newBalance / 100,
            'description' => $event->description ?: 'Withdrawal',
            'date' => date('c', (int) $event->occurredOn),
        ];
    }

    public function projectAccountClosed(AccountClosedEvent $event): void
    {
        $id = (string) $event->accountId;
        $this->transactions[$id][] = [
            'type' => 'account_closed',
            'amount' => 0,
            'balance' => $event->finalBalance / 100,
            'description' => $event->reason ?: 'Account closed',
            'date' => date('c', (int) $event->occurredOn),
        ];
    }

    public function getHistory(AccountId $id): TransactionHistory
    {
        $idStr = (string) $id;
        return new TransactionHistory(
            $idStr,
            $this->transactions[$idStr] ?? []
        );
    }
}
