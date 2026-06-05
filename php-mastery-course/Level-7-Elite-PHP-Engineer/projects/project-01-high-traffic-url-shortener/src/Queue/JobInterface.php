<?php

declare(strict_types=1);

namespace UrlShortener\Queue;

interface JobInterface
{
    public function handle(): void;
    public function getName(): string;
    public function getPayload(): array;
    public function getRetries(): int;
    public function getMaxRetries(): int;
}
