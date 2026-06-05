<?php

declare(strict_types=1);

namespace EventDispatcher;

interface SubscriberInterface
{
    public function getSubscribedEvents(): array;
}
