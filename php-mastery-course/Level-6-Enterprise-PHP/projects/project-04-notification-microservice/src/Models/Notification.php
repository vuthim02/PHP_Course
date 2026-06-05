<?php

declare(strict_types=1);

namespace NotificationService\Models;

/**
 * Notification Model
 *
 * Represents a notification to be dispatched.
 * Mapped from incoming RabbitMQ messages.
 */
final class Notification
{
    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_SMS = 'sms';
    public const CHANNEL_PUSH = 'push';

    public const TYPE_WELCOME = 'welcome_email';
    public const TYPE_PASSWORD_RESET = 'password_reset';
    public const TYPE_ORDER_CONFIRMATION = 'order_confirmation';
    public const TYPE_WEEKLY_DIGEST = 'weekly_digest';

    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';
    public const STATUS_RETRYING = 'retrying';

    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $channel,
        public readonly string $recipient,
        public readonly array $data,
        public string $status = self::STATUS_PENDING,
        public int $retryCount = 0,
        public ?string $errorMessage = null,
        public ?string $sentAt = null,
        public readonly string $createdAt = ''
    ) {
    }

    /**
     * Create a Notification from a RabbitMQ message payload.
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            $payload['id'] ?? bin2hex(random_bytes(16)),
            $payload['type'],
            $payload['channel'],
            $payload['recipient'],
            $payload['data'] ?? [],
            self::STATUS_PENDING,
            0,
            null,
            null,
            date('c')
        );
    }

    /**
     * Create notification from DB row.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            $row['id'],
            $row['type'],
            $row['channel'],
            $row['recipient'],
            json_decode($row['data'] ?? '{}', true),
            $row['status'],
            (int) ($row['retry_count'] ?? 0),
            $row['error_message'] ?? null,
            $row['sent_at'] ?? null,
            $row['created_at']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'channel' => $this->channel,
            'recipient' => $this->recipient,
            'data' => $this->data,
            'status' => $this->status,
            'retry_count' => $this->retryCount,
            'error_message' => $this->errorMessage,
            'sent_at' => $this->sentAt,
            'created_at' => $this->createdAt,
        ];
    }

    /**
     * Determine if this notification can be retried.
     */
    public function canRetry(int $maxRetries = 3): bool
    {
        return $this->retryCount < $maxRetries;
    }

    /**
     * Calculate backoff delay in seconds (exponential: 2^n * 30).
     */
    public function backoffDelay(): int
    {
        return (int) pow(2, $this->retryCount) * 30;
    }
}
