<?php

declare(strict_types=1);

namespace CQRSES\Domain\Aggregate;

use CQRSES\Domain\ValueObject\AccountId;
use CQRSES\Domain\ValueObject\Amount;
use CQRSES\Domain\ValueObject\Currency;
use CQRSES\Domain\Event\AccountOpenedEvent;
use CQRSES\Domain\Event\MoneyDepositedEvent;
use CQRSES\Domain\Event\MoneyWithdrawnEvent;
use CQRSES\Domain\Event\AccountClosedEvent;

/**
 * Account Aggregate Root (Event Sourced)
 *
 * In Event Sourcing, the aggregate state is rebuilt by replaying
 * all previously stored events. The aggregate handles commands and
 * produces new events — it never directly modifies state.
 *
 * Key pattern: applyXxxEvent() methods mutate state; handleXxxCommand()
 * methods validate and produce events.
 */
final class Account
{
    private AccountId $accountId;
    private string $ownerName;
    private Currency $currency;
    private int $balance;  // stored in cents
    private bool $isActive;
    private int $version = 0;

    /** @var object[] */
    private array $newEvents = [];

    // -- Private constructor: use static factory or rebuild from stream --
    private function __construct()
    {
    }

    /**
     * Open a new account — factory method on the aggregate.
     */
    public static function open(AccountId $id, string $ownerName, Currency $currency): self
    {
        $account = new self();
        $account->applyEvent(new AccountOpenedEvent($id, $ownerName, $currency, microtime(true)));
        return $account;
    }

    /**
     * Rebuild aggregate from an event stream.
     * This is the "reconstitution" pattern in Event Sourcing.
     *
     * @param object[] $events
     */
    public static function fromStream(array $events): self
    {
        $account = new self();
        foreach ($events as $event) {
            $account->applyEvent($event);
        }
        return $account;
    }

    // -- Command handlers (validation + event production) --

    public function deposit(Amount $amount, string $description = ''): void
    {
        if (!$this->isActive) {
            throw new \DomainException('Cannot deposit to a closed account');
        }

        $newBalance = $this->balance + $amount->cents();
        $this->applyEvent(new MoneyDepositedEvent(
            $this->accountId,
            $amount,
            $newBalance,
            $description,
            microtime(true)
        ));
    }

    public function withdraw(Amount $amount, string $description = ''): void
    {
        if (!$this->isActive) {
            throw new \DomainException('Cannot withdraw from a closed account');
        }

        if ($amount->cents() > $this->balance) {
            throw new \DomainException(
                "Insufficient funds. Balance: {$this->balance}c, Requested: {$amount->cents()}c"
            );
        }

        $newBalance = $this->balance - $amount->cents();
        $this->applyEvent(new MoneyWithdrawnEvent(
            $this->accountId,
            $amount,
            $newBalance,
            $description,
            microtime(true)
        ));
    }

    public function close(string $reason = ''): void
    {
        if (!$this->isActive) {
            throw new \DomainException('Account is already closed');
        }

        $this->applyEvent(new AccountClosedEvent(
            $this->accountId,
            $reason,
            $this->balance,
            microtime(true)
        ));
    }

    // -- Event application (state mutation) --

    /**
     * Apply an event: both mutates state AND records as new event.
     */
    private function applyEvent(object $event): void
    {
        $this->mutateState($event);
        $this->newEvents[] = $event;
        $this->version++;
    }

    /**
     * Mutate aggregate state based on event type.
     * This is the core Event Sourcing pattern: state = fold(events).
     */
    private function mutateState(object $event): void
    {
        match (true) {
            $event instanceof AccountOpenedEvent => $this->applyAccountOpened($event),
            $event instanceof MoneyDepositedEvent => $this->applyMoneyDeposited($event),
            $event instanceof MoneyWithdrawnEvent => $this->applyMoneyWithdrawn($event),
            $event instanceof AccountClosedEvent => $this->applyAccountClosed($event),
            default => throw new \RuntimeException('Unknown event: ' . get_class($event)),
        };
    }

    private function applyAccountOpened(AccountOpenedEvent $event): void
    {
        $this->accountId = $event->accountId;
        $this->ownerName = $event->ownerName;
        $this->currency = $event->currency;
        $this->balance = 0;
        $this->isActive = true;
    }

    private function applyMoneyDeposited(MoneyDepositedEvent $event): void
    {
        $this->balance = $event->newBalance;
    }

    private function applyMoneyWithdrawn(MoneyWithdrawnEvent $event): void
    {
        $this->balance = $event->newBalance;
    }

    private function applyAccountClosed(AccountClosedEvent $event): void
    {
        $this->isActive = false;
    }

    // -- Getters --

    public function accountId(): AccountId { return $this->accountId; }
    public function ownerName(): string { return $this->ownerName; }
    public function currency(): Currency { return $this->currency; }
    public function balance(): int { return $this->balance; }
    public function isActive(): bool { return $this->isActive; }
    public function version(): int { return $this->version; }

    /**
     * @return object[] New events generated by command handlers.
     */
    public function newEvents(): array
    {
        return $this->newEvents;
    }

    public function clearNewEvents(): void
    {
        $this->newEvents = [];
    }
}
