<?php

declare(strict_types=1);

namespace EventDispatcher\PubSub;

class MessageBus
{
    private array $handlers = [];
    private array $middleware = [];

    public function subscribe(string $topic, HandlerInterface $handler, int $priority = 0): void
    {
        if (!isset($this->handlers[$topic])) {
            $this->handlers[$topic] = new \SplPriorityQueue();
        }

        $this->handlers[$topic]->insert($handler, $priority);
    }

    public function subscribeCallback(string $topic, callable $callback, int $priority = 0): void
    {
        $handler = new class($callback) implements HandlerInterface {
            private $callback;
            public function __construct(callable $callback)
            {
                $this->callback = $callback;
            }
            public function __invoke(Message $message): void
            {
                ($this->callback)($message);
            }
        };

        $this->subscribe($topic, $handler, $priority);
    }

    public function publish(Message $message): void
    {
        $topic = $message->getTopic();

        $pipeline = $this->buildPipeline(function (Message $msg): void {
            $this->handleMessage($msg);
        });

        $pipeline($message);
    }

    public function publishAsync(Message $message): void
    {
        register_shutdown_function(fn() => $this->publish($message));
    }

    public function addMiddleware(callable $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    public function getTopics(): array
    {
        return array_keys($this->handlers);
    }

    public function hasSubscribers(string $topic): bool
    {
        return isset($this->handlers[$topic]) && $this->handlers[$topic]->count() > 0;
    }

    public function countSubscribers(string $topic): int
    {
        return isset($this->handlers[$topic]) ? $this->handlers[$topic]->count() : 0;
    }

    private function handleMessage(Message $message): void
    {
        $topic = $message->getTopic();

        if (!isset($this->handlers[$topic])) {
            return;
        }

        $handlers = clone $this->handlers[$topic];
        foreach ($handlers as $handler) {
            if ($message->isHandled()) {
                break;
            }
            $handler($message);
        }
    }

    private function buildPipeline(callable $core): callable
    {
        $pipeline = $core;

        foreach (array_reverse($this->middleware) as $mw) {
            $next = $pipeline;
            $pipeline = function (Message $message) use ($mw, $next): void {
                $mw($message, $next);
            };
        }

        return $pipeline;
    }
}
