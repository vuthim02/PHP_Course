<?php

declare(strict_types=1);

namespace EventDispatcher;

use EventDispatcher\PubSub\MessageBus;

class ListenerProvider
{
    private array $listeners = [];
    private array $wildcardCache = [];

    public function addListener(string $eventName, callable $listener, int $priority = 0): void
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = new \SplPriorityQueue();
        }

        $this->listeners[$eventName]->insert($listener, $priority);
        $this->wildcardCache = [];
    }

    public function addSubscriber(SubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $config) {
            if (is_callable($config)) {
                $this->addListener($eventName, $config->bindTo($subscriber, $subscriber));
            } elseif (is_array($config)) {
                [$method, $priority] = $config;
                $this->addListener($eventName, [$subscriber, $method], $priority ?? 0);
            }
        }
    }

    public function removeListener(string $eventName, callable $listener): void
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }

        $newQueue = new \SplPriorityQueue();
        foreach ($this->listeners[$eventName] as $l) {
            if ($l !== $listener) {
                $newQueue->insert($l, $this->listeners[$eventName]->top());
            }
        }
        $this->listeners[$eventName] = $newQueue;
    }

    public function getListeners(string $eventName): array
    {
        $listeners = [];

        if (isset($this->listeners[$eventName])) {
            $temp = clone $this->listeners[$eventName];
            foreach ($temp as $listener) {
                $listeners[] = $listener;
            }
        }

        $wildcard = $this->getWildcardListeners($eventName);
        $listeners = array_merge($listeners, $wildcard);

        return $listeners;
    }

    private function getWildcardListeners(string $eventName): array
    {
        if (isset($this->wildcardCache[$eventName])) {
            return $this->wildcardCache[$eventName];
        }

        $listeners = [];
        $parts = explode('.', $eventName);

        foreach ($this->listeners as $registered => $queue) {
            if ($registered === $eventName || !str_contains($registered, '*')) {
                continue;
            }

            $pattern = '/^' . str_replace(['\\*', '.'], ['[^.]+', '\\.'], preg_quote($registered, '/')) . '$/';
            if (preg_match($pattern, $eventName)) {
                $temp = clone $queue;
                foreach ($temp as $listener) {
                    $listeners[] = $listener;
                }
            }
        }

        $this->wildcardCache[$eventName] = $listeners;
        return $listeners;
    }

    public function hasListeners(string $eventName): bool
    {
        return isset($this->listeners[$eventName]) && $this->listeners[$eventName]->count() > 0;
    }
}
