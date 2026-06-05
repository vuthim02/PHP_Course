<?php

declare(strict_types=1);

namespace DDD\Domain\Shared;

/**
 * Domain Event Interface
 *
 * Domain events capture something that happened in the domain.
 * They are immutable and named in the past tense.
 * Other bounded contexts or application services react to these events.
 */
interface DomainEventInterface
{
    /**
     * When the event occurred (Unix timestamp with microseconds).
     */
    public function occurredOn(): float;
}
