<?php

declare(strict_types=1);

namespace EventDispatcher\PubSub;

interface HandlerInterface
{
    public function __invoke(Message $message): void;
}
