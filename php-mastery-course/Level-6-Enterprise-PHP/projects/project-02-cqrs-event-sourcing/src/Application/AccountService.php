<?php

declare(strict_types=1);

namespace CQRSES\Application;

use CQRSES\Domain\Aggregate\Account;
use CQRSES\Domain\ValueObject\AccountId;
use CQRSES\Domain\ValueObject\Amount;
use CQRSES\Domain\ValueObject\Currency;
use CQRSES\Infrastructure\EventStore\FileEventStore;
use CQRSES\Infrastructure\Bus\SimpleEventBus;

/**
 * AccountService
 *
 * Application service orchestrating command execution with Event Sourcing.
 *  1. Load aggregate from event stream
 *  2. Execute command (produces new events)
 *  3. Store new events in event store
 *  4. Publish events to projectors
 *
 * This pattern ensures atomicity: events are only stored if the aggregate
 * command succeeds, and projectors only update after events are persisted.
 */
final class AccountService
{
    public function __construct(
        private FileEventStore $eventStore,
        private SimpleEventBus $eventBus
    ) {
    }

    public function openAccount(AccountId $id, string $ownerName, Currency $currency): Account
    {
        $account = Account::open($id, $ownerName, $currency);
        $this->eventStore->append($id, $account->newEvents(), 0);
        $this->eventBus->publishAll($account->newEvents());
        $account->clearNewEvents();

        echo "[Account] Opened {$currency} account {$id} for {$ownerName}\n";

        return $account;
    }

    public function depositMoney(AccountId $id, Amount $amount, string $description = ''): Account
    {
        $events = $this->eventStore->loadStream($id);
        $account = Account::fromStream($events);

        $account->deposit($amount, $description);
        $this->eventStore->append($id, $account->newEvents(), $account->version() - count($account->newEvents()));
        $this->eventBus->publishAll($account->newEvents());
        $account->clearNewEvents();

        echo "[Account] Deposited {$amount} into account {$id}\n";

        return $account;
    }

    public function withdrawMoney(AccountId $id, Amount $amount, string $description = ''): Account
    {
        $events = $this->eventStore->loadStream($id);
        $account = Account::fromStream($events);

        $account->withdraw($amount, $description);
        $this->eventStore->append($id, $account->newEvents(), $account->version() - count($account->newEvents()));
        $this->eventBus->publishAll($account->newEvents());
        $account->clearNewEvents();

        echo "[Account] Withdrew {$amount} from account {$id}\n";

        return $account;
    }

    public function closeAccount(AccountId $id, string $reason = ''): Account
    {
        $events = $this->eventStore->loadStream($id);
        $account = Account::fromStream($events);

        $account->close($reason);
        $this->eventStore->append($id, $account->newEvents(), $account->version() - count($account->newEvents()));
        $this->eventBus->publishAll($account->newEvents());
        $account->clearNewEvents();

        echo "[Account] Closed account {$id}: {$reason}\n";

        return $account;
    }

    public function getAccount(AccountId $id): ?Account
    {
        $events = $this->eventStore->loadStream($id);
        if (empty($events)) {
            return null;
        }
        return Account::fromStream($events);
    }
}
