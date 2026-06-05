<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use EventDispatcher\Event;
use EventDispatcher\EventDispatcher;
use EventDispatcher\ListenerProvider;
use EventDispatcher\PubSub\Message;
use EventDispatcher\PubSub\MessageBus;
use EventDispatcher\SubscriberInterface;

echo "=== Event Dispatcher & Pub-Sub Demo ===\n\n";

echo "--- Event Dispatcher ---\n";

$provider = new ListenerProvider();
$dispatcher = new EventDispatcher($provider);

$provider->addListener('user.registered', function (Event $event): void {
    $data = $event->getData();
    echo "[listener] User registered: {$data['email']}\n";
}, 10);

$provider->addListener('user.registered', function (Event $event): void {
    $data = $event->getData();
    echo "[listener] Sending welcome email to: {$data['email']}\n";
}, 5);

$provider->addListener('user.*', function (Event $event): void {
    echo "[wildcard *] Caught event: {$event->getName()}\n";
}, 0);

$dispatcher->dispatch(new Event('user.registered', ['email' => 'alice@example.com', 'name' => 'Alice']));

echo "\n--- Propagation Stopping ---\n";

$provider->addListener('order.placed', function (Event $event): void {
    echo "[high priority] Processing order...\n";
    $event->stopPropagation();
}, 100);

$provider->addListener('order.placed', function (Event $event): void {
    echo "[low priority] This should NOT run (propagation stopped)\n";
}, 0);

$dispatcher->dispatch(new Event('order.placed', ['order_id' => 123]));

echo "\n--- Subscriber Interface ---\n";

class UserSubscriber implements SubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            'user.registered' => ['onUserRegistered', 15],
            'user.deleted'    => ['onUserDeleted', 10],
        ];
    }

    public function onUserRegistered(Event $event): void
    {
        echo "[subscriber] User registered: " . json_encode($event->getData()) . "\n";
    }

    public function onUserDeleted(Event $event): void
    {
        echo "[subscriber] User deleted: " . json_encode($event->getData()) . "\n";
    }
}

$provider->addSubscriber(new UserSubscriber());
$dispatcher->dispatch(new Event('user.deleted', ['id' => 42]));

echo "\n--- Pub-Sub Message Bus ---\n";

$bus = new MessageBus();

$bus->subscribeCallback('orders.created', function (Message $msg): void {
    $payload = $msg->getPayload();
    echo "[handler] Order #{$payload['id']} created for \${$payload['total']}\n";
});

$bus->subscribeCallback('orders.created', function (Message $msg): void {
    echo "[handler] Sending order confirmation...\n";
});

$bus->addMiddleware(function (Message $msg, callable $next): void {
    echo "[middleware] Before handling: {$msg->getTopic()}\n";
    $next($msg);
    echo "[middleware] After handling\n";
});

$bus->publish(new Message('orders.created', ['id' => 1001, 'total' => 59.99]));

echo "\n--- Functional event map & filter ---\n";

$dispatcher->map('data.transform', ['a' => 1, 'b' => 2], fn(array $d) => array_map(fn($v) => $v * 2, $d));

$provider->addListener('data.transform', function (Event $e): void {
    echo "[map] Transformed data: " . json_encode($e->getData()) . "\n";
});

$dispatcher->filter('data.filter', 42, fn($v) => $v > 10);

$provider->addListener('data.filter', function (Event $e): void {
    echo "[filter] Value passed filter: {$e->getData()}\n";
});

echo "\nDemo completed successfully!\n";
