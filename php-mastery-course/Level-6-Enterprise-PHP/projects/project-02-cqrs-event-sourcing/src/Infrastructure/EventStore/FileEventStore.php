<?php

declare(strict_types=1);

namespace CQRSES\Infrastructure\EventStore;

use CQRSES\Domain\ValueObject\AccountId;

/**
 * FileEventStore
 *
 * Append-only event store using JSON files.
 * Each account stream is stored in a separate file: data/events/{accountId}.json
 *
 * This is a simplistic implementation for demonstration.
 * Production event stores use databases (PostgreSQL, EventStoreDB) with
 * optimistic concurrency control via version numbers.
 */
final class FileEventStore
{
    private string $storagePath;

    public function __construct(?string $storagePath = null)
    {
        $this->storagePath = $storagePath ?? __DIR__ . '/../../../data/events';
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
    }

    /**
     * Append events to the stream for the given aggregate.
     */
    public function append(AccountId $id, array $events, int $expectedVersion): void
    {
        $filename = $this->streamFile($id);
        $stream = $this->loadStream($id);

        // Optimistic concurrency check
        if (count($stream) !== $expectedVersion) {
            throw new \RuntimeException(
                "Concurrency conflict for account {$id}. " .
                "Expected version {$expectedVersion}, current version " . count($stream)
            );
        }

        foreach ($events as $event) {
            $stream[] = $this->serializeEvent($event);
        }

        file_put_contents($filename, json_encode($stream, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Load all events for an aggregate, deserializing into event objects.
     *
     * @return object[]
     */
    public function loadStream(AccountId $id): array
    {
        $filename = $this->streamFile($id);

        if (!file_exists($filename)) {
            return [];
        }

        $data = json_decode(file_get_contents($filename), true);
        if (!is_array($data)) {
            return [];
        }

        return array_map(fn(array $item) => $this->deserializeEvent($item), $data);
    }

    /**
     * Get all account IDs that have streams.
     *
     * @return AccountId[]
     */
    public function listAccounts(): array
    {
        $files = glob($this->storagePath . '/ACC-*.json');
        if (!$files) {
            return [];
        }

        return array_map(function (string $file) {
            $id = pathinfo($file, PATHINFO_FILENAME);
            return new AccountId($id);
        }, $files);
    }

    private function streamFile(AccountId $id): string
    {
        return $this->storagePath . '/' . $id->id() . '.json';
    }

    /**
     * Serialize event to array for storage.
     */
    private function serializeEvent(object $event): array
    {
        $data = [
            'event_type' => get_class($event),
            'occurred_on' => microtime(true),
        ];

        foreach (get_object_vars($event) as $prop => $value) {
            $data[$prop] = $this->serializeValue($value);
        }

        return $data;
    }

    private function serializeValue(mixed $value): mixed
    {
        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }
        if (is_object($value) && method_exists($value, 'cents')) {
            return $value->cents();
        }
        if (is_object($value) && method_exists($value, 'code')) {
            return $value->code();
        }
        return $value;
    }

    /**
     * Deserialize a stored event array back into an event object.
     */
    private function deserializeEvent(array $data): object
    {
        $eventClass = $data['event_type'];
        $occurredOn = $data['occurred_on'];

        // Map fields back to constructor arguments based on event type
        return match ($eventClass) {
            \CQRSES\Domain\Event\AccountOpenedEvent::class => new $eventClass(
                new \CQRSES\Domain\ValueObject\AccountId($data['accountId']),
                $data['ownerName'],
                new \CQRSES\Domain\ValueObject\Currency($data['currency']),
                $occurredOn
            ),
            \CQRSES\Domain\Event\MoneyDepositedEvent::class => new $eventClass(
                new \CQRSES\Domain\ValueObject\AccountId($data['accountId']),
                \CQRSES\Domain\ValueObject\Amount::fromFloat($data['amount'] / 100),
                $data['newBalance'],
                $data['description'],
                $occurredOn
            ),
            \CQRSES\Domain\Event\MoneyWithdrawnEvent::class => new $eventClass(
                new \CQRSES\Domain\ValueObject\AccountId($data['accountId']),
                \CQRSES\Domain\ValueObject\Amount::fromFloat($data['amount'] / 100),
                $data['newBalance'],
                $data['description'],
                $occurredOn
            ),
            \CQRSES\Domain\Event\AccountClosedEvent::class => new $eventClass(
                new \CQRSES\Domain\ValueObject\AccountId($data['accountId']),
                $data['reason'],
                $data['finalBalance'],
                $occurredOn
            ),
            default => throw new \RuntimeException("Unknown event type: {$eventClass}"),
        };
    }
}
