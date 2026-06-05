<?php

declare(strict_types=1);

/**
 * CLI Bank Demo — CQRS + Event Sourcing
 *
 * Interactive command-line demonstration of the event-sourced bank account.
 * Commands are executed via the command bus; queries go through the query bus.
 * Read models are updated via projectors as events are published.
 *
 * Usage: php examples/cli-bank.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use CQRSES\Domain\ValueObject\AccountId;
use CQRSES\Domain\ValueObject\Amount;
use CQRSES\Domain\ValueObject\Currency;
use CQRSES\Domain\Command\OpenAccountCommand;
use CQRSES\Domain\Command\DepositMoneyCommand;
use CQRSES\Domain\Command\WithdrawMoneyCommand;
use CQRSES\Domain\Command\CloseAccountCommand;
use CQRSES\Domain\Event\AccountOpenedEvent;
use CQRSES\Domain\Event\MoneyDepositedEvent;
use CQRSES\Domain\Event\MoneyWithdrawnEvent;
use CQRSES\Domain\Event\AccountClosedEvent;
use CQRSES\Infrastructure\EventStore\FileEventStore;
use CQRSES\Infrastructure\Bus\SimpleCommandBus;
use CQRSES\Infrastructure\Bus\SimpleEventBus;
use CQRSES\Infrastructure\Bus\SimpleQueryBus;
use CQRSES\Infrastructure\Projection\AccountBalanceProjector;
use CQRSES\Infrastructure\Projection\TransactionHistoryProjector;
use CQRSES\Application\AccountService;

// --- Bootstrap ---

$eventStore = new FileEventStore();
$eventBus = new SimpleEventBus();
$commandBus = new SimpleCommandBus();
$queryBus = new SimpleQueryBus();

$accountService = new AccountService($eventStore, $eventBus);

// Projectors (read models)
$balanceProjector = new AccountBalanceProjector();
$historyProjector = new TransactionHistoryProjector();

// Wire event subscribers
$eventBus->subscribe(AccountOpenedEvent::class, fn($e) => $balanceProjector->projectAccountOpened($e));
$eventBus->subscribe(MoneyDepositedEvent::class, fn($e) => $balanceProjector->projectMoneyDeposited($e));
$eventBus->subscribe(MoneyWithdrawnEvent::class, fn($e) => $balanceProjector->projectMoneyWithdrawn($e));
$eventBus->subscribe(AccountClosedEvent::class, fn($e) => $balanceProjector->projectAccountClosed($e));

$eventBus->subscribe(AccountOpenedEvent::class, fn($e) => $historyProjector->projectAccountOpened($e));
$eventBus->subscribe(MoneyDepositedEvent::class, fn($e) => $historyProjector->projectMoneyDeposited($e));
$eventBus->subscribe(MoneyWithdrawnEvent::class, fn($e) => $historyProjector->projectMoneyWithdrawn($e));
$eventBus->subscribe(AccountClosedEvent::class, fn($e) => $historyProjector->projectAccountClosed($e));

// Register command handlers
$commandBus->register(OpenAccountCommand::class, function (OpenAccountCommand $cmd) use ($accountService) {
    $accountService->openAccount($cmd->accountId, $cmd->ownerName, $cmd->currency);
});

$commandBus->register(DepositMoneyCommand::class, function (DepositMoneyCommand $cmd) use ($accountService) {
    $accountService->depositMoney($cmd->accountId, $cmd->amount, $cmd->description);
});

$commandBus->register(WithdrawMoneyCommand::class, function (WithdrawMoneyCommand $cmd) use ($accountService) {
    $accountService->withdrawMoney($cmd->accountId, $cmd->amount, $cmd->description);
});

$commandBus->register(CloseAccountCommand::class, function (CloseAccountCommand $cmd) use ($accountService) {
    $accountService->closeAccount($cmd->accountId, $cmd->reason);
});

// Register query handlers
$queryBus->register('GetBalance', function (AccountId $id) use ($balanceProjector) {
    return $balanceProjector->getBalance($id);
});

$queryBus->register('GetHistory', function (AccountId $id) use ($historyProjector) {
    return $historyProjector->getHistory($id);
});

// --- CLI ---

function printHelp(): void
{
    echo "\n╔══════════════════════════════════════════════╗\n";
    echo "║  CQRS/ES Bank — Interactive CLI Demo        ║\n";
    echo "╚══════════════════════════════════════════════╝\n";
    echo "Commands:\n";
    echo "  open <owner> <currency>    Open new account (USD, EUR, GBP)\n";
    echo "  deposit <id> <amount>      Deposit money\n";
    echo "  withdraw <id> <amount>     Withdraw money\n";
    echo "  balance <id>               Check balance (query)\n";
    echo "  history <id>               Transaction history (query)\n";
    echo "  close <id> <reason>        Close account\n";
    echo "  list                       List all accounts\n";
    echo "  rebuild                    Rebuild projections from event store\n";
    echo "  help                       Show this help\n";
    echo "  quit                       Exit\n\n";
}

echo "Initializing event store...\n";
echo "Storage: data/events/\n";

// Rebuild projections from event store on startup
$accounts = $eventStore->listAccounts();
foreach ($accounts as $accId) {
    $events = $eventStore->loadStream($accId);
    foreach ($events as $event) {
        match (true) {
            $event instanceof AccountOpenedEvent => $balanceProjector->projectAccountOpened($event) + $historyProjector->projectAccountOpened($event),
            $event instanceof MoneyDepositedEvent => $balanceProjector->projectMoneyDeposited($event) + $historyProjector->projectMoneyDeposited($event),
            $event instanceof MoneyWithdrawnEvent => $balanceProjector->projectMoneyWithdrawn($event) + $historyProjector->projectMoneyWithdrawn($event),
            $event instanceof AccountClosedEvent => $balanceProjector->projectAccountClosed($event) + $historyProjector->projectAccountClosed($event),
            default => null,
        };
    }
}
echo "Rebuilt projections for " . count($accounts) . " existing account(s).\n";

printHelp();

while (true) {
    echo "\nbank> ";
    $input = trim(fgets(STDIN));
    $parts = explode(' ', $input);
    $cmd = $parts[0] ?? '';

    try {
        $result = match ($cmd) {
            'open' => handleOpen($commandBus, $parts),
            'deposit' => handleDeposit($commandBus, $parts),
            'withdraw' => handleWithdraw($commandBus, $parts),
            'balance' => handleBalance($queryBus, $parts),
            'history' => handleHistory($queryBus, $parts),
            'close' => handleClose($commandBus, $parts),
            'list' => handleList($balanceProjector),
            'rebuild' => handleRebuild($eventStore, $balanceProjector, $historyProjector),
            'help' => printHelp(),
            'quit' => 'quit',
            '' => null,
            default => 'unknown',
        };

        if ($result === 'quit') {
            echo "Goodbye!\n";
            break;
        }

        if ($result === 'unknown') {
            echo "Unknown command. Type 'help'.\n";
        }
    } catch (\Throwable $e) {
        echo "❌ Error: {$e->getMessage()}\n";
    }
}

// --- Handler functions ---

function handleOpen(SimpleCommandBus $bus, array $parts): void
{
    $owner = $parts[1] ?? readline("Owner name: ");
    $currencyCode = strtoupper($parts[2] ?? readline("Currency (USD/EUR/GBP): "));
    $id = AccountId::generate();
    $bus->dispatch(new OpenAccountCommand($id, $owner, new Currency($currencyCode)));
    echo "✅ Account {$id} opened for {$owner}\n";
}

function handleDeposit(SimpleCommandBus $bus, array $parts): void
{
    $id = new AccountId($parts[1] ?? readline("Account ID: "));
    $amount = Amount::fromFloat((float) ($parts[2] ?? readline("Amount: ")));
    $desc = $parts[3] ?? 'CLI deposit';
    $bus->dispatch(new DepositMoneyCommand($id, $amount, $desc));
    echo "✅ Deposited {$amount} into {$id}\n";
}

function handleWithdraw(SimpleCommandBus $bus, array $parts): void
{
    $id = new AccountId($parts[1] ?? readline("Account ID: "));
    $amount = Amount::fromFloat((float) ($parts[2] ?? readline("Amount: ")));
    $desc = $parts[3] ?? 'CLI withdrawal';
    $bus->dispatch(new WithdrawMoneyCommand($id, $amount, $desc));
    echo "✅ Withdrew {$amount} from {$id}\n";
}

function handleBalance(SimpleQueryBus $bus, array $parts): void
{
    $id = new AccountId($parts[1] ?? readline("Account ID: "));
    $balance = $bus->dispatch('GetBalance', $id);
    if ($balance === null) {
        echo "Account not found.\n";
        return;
    }
    echo "💰 Balance: {$balance->balanceFormatted()} ({$balance->status})\n";
}

function handleHistory(SimpleQueryBus $bus, array $parts): void
{
    $id = new AccountId($parts[1] ?? readline("Account ID: "));
    $history = $bus->dispatch('GetHistory', $id);
    echo "\n── Transaction History for {$id} ──\n";
    foreach ($history->transactions as $txn) {
        $sign = $txn['amount'] >= 0 ? '+' : '';
        echo "  {$txn['type']}: {$sign}\${$txn['amount']} (balance: \${$txn['balance']}) — {$txn['description']}\n";
    }
}

function handleClose(SimpleCommandBus $bus, array $parts): void
{
    $id = new AccountId($parts[1] ?? readline("Account ID: "));
    $reason = $parts[2] ?? 'Customer request';
    $bus->dispatch(new CloseAccountCommand($id, $reason));
    echo "✅ Account {$id} closed.\n";
}

function handleList(AccountBalanceProjector $projector): void
{
    $balances = $projector->allBalances();
    if (empty($balances)) {
        echo "No accounts found.\n";
        return;
    }
    echo "\n── All Accounts ──\n";
    foreach ($balances as $b) {
        echo "  {$b->accountId} | {$b->ownerName} | {$b->balanceFormatted()} | {$b->status}\n";
    }
}

function handleRebuild(FileEventStore $eventStore, AccountBalanceProjector $balanceProjector, TransactionHistoryProjector $historyProjector): void
{
    $accounts = $eventStore->listAccounts();
    foreach ($accounts as $accId) {
        $events = $eventStore->loadStream($accId);
        foreach ($events as $event) {
            match (true) {
                $event instanceof AccountOpenedEvent => $balanceProjector->projectAccountOpened($event) + $historyProjector->projectAccountOpened($event),
                $event instanceof MoneyDepositedEvent => $balanceProjector->projectMoneyDeposited($event) + $historyProjector->projectMoneyDeposited($event),
                $event instanceof MoneyWithdrawnEvent => $balanceProjector->projectMoneyWithdrawn($event) + $historyProjector->projectMoneyWithdrawn($event),
                $event instanceof AccountClosedEvent => $balanceProjector->projectAccountClosed($event) + $historyProjector->projectAccountClosed($event),
                default => null,
            };
        }
    }
    echo "✅ Rebuilt projections for " . count($accounts) . " accounts.\n";
}
