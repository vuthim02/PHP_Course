<?php

declare(strict_types=1);

namespace DDD\Interface\Cli;

use DDD\Application\OrderService;
use DDD\Application\PaymentService;
use DDD\Application\Query\OrderQuery;
use DDD\Domain\Order\OrderId;
use DDD\Domain\Shared\ValueObjects\Address;
use DDD\Infrastructure\Event\InMemoryEventBus;
use DDD\Infrastructure\Persistence\DoctrineOrderRepository;
use DDD\Infrastructure\Services\MockShippingService;
use DDD\Infrastructure\Services\MockPaymentGateway;
use DDD\Domain\Payment\PaymentRepositoryInterface;
use DDD\Infrastructure\Persistence\InMemoryPaymentRepository;

/**
 * OrderCommand
 *
 * CLI entry point demonstrating the DDD order management system.
 * This is part of the Interface/Entry-point layer in hexagonal architecture.
 * It wires up all dependencies (composition root) and executes use cases.
 */

// --- Bootstrap / Composition Root ---
// In a real app this would be a DI container configuration.

require_once __DIR__ . '/../../../../vendor/autoload.php';

// Infrastructure
$eventBus = new InMemoryEventBus();
$orderRepository = new DoctrineOrderRepository();
$paymentRepository = new InMemoryPaymentRepository();
$paymentGateway = new MockPaymentGateway();
$shippingService = new MockShippingService();

// Application Services
$orderService = new OrderService($orderRepository, $eventBus);
$paymentService = new PaymentService($paymentRepository, $paymentGateway);
$orderQuery = new OrderQuery($orderRepository);

// --- Wire up Domain Event subscribers ---
// Decoupled: when OrderPlacedEvent fires, payment and shipping react automatically.

$eventBus->subscribe(
    \DDD\Domain\Order\Event\OrderPlacedEvent::class,
    function (\DDD\Domain\Order\Event\OrderPlacedEvent $event) use ($paymentService) {
        echo "[Event Bus] OrderPlacedEvent received for order {$event->orderId()}\n";
        $paymentService->processPayment(
            $event->orderId(),
            $event->total(),
            'credit_card'
        );
    }
);

$eventBus->subscribe(
    \DDD\Domain\Order\Event\OrderPlacedEvent::class,
    function (\DDD\Domain\Order\Event\OrderPlacedEvent $event) use ($shippingService) {
        echo "[Event Bus] OrderPlacedEvent received — initiating shipping for order {$event->orderId()}\n";
        $address = new Address('123 Main St', 'Springfield', 'IL', '62701', 'US');
        $shippingAddr = new \DDD\Domain\Shipping\ShippingAddress($address);
        $shippingService->createShipment($event->orderId(), $shippingAddr);
    }
);

$eventBus->subscribe(
    \DDD\Domain\Order\Event\OrderCancelledEvent::class,
    function (\DDD\Domain\Order\Event\OrderCancelledEvent $event) use ($paymentService, $paymentRepository, $orderService) {
        echo "[Event Bus] OrderCancelledEvent received for order {$event->orderId()}\n";
        // Find payment and refund it
        $payment = $paymentRepository->findByOrderId($event->orderId());
        if ($payment !== null) {
            $paymentService->refundPayment($payment->id());
        }
    }
);

// --- CLI Commands ---

function printHelp(): void
{
    echo "\nDDD Order Management System — CLI Demo\n";
    echo "─────────────────────────────────────────────\n";
    echo "Commands:\n";
    echo "  place     Place a new order\n";
    echo "  cancel    Cancel an order\n";
    echo "  get       Get order details\n";
    echo "  list      List customer orders\n";
    echo "  help      Show this help\n";
    echo "  quit      Exit\n\n";
}

function runInteractive(\DDD\Application\OrderService $orderService, \DDD\Application\Query\OrderQuery $orderQuery): void
{
    printHelp();

    while (true) {
        echo "\n> ";
        $input = trim(fgets(STDIN));

        $result = match ($input) {
            'place' => handlePlace($orderService),
            'cancel' => handleCancel($orderService),
            'get' => handleGet($orderQuery),
            'list' => handleList($orderQuery),
            'help' => printHelp(),
            'quit' => 'quit',
            default => 'unknown',
        };

        if ($result === 'quit') {
            echo "Goodbye!\n";
            break;
        }

        if ($result === 'unknown') {
            echo "Unknown command. Type 'help' for available commands.\n";
        }
    }
}

function handlePlace(\DDD\Application\OrderService $orderService): void
{
    echo "Customer ID: ";
    $customerId = trim(fgets(STDIN));

    $shippingAddress = new Address(
        "123 Main St",
        "Springfield",
        "IL",
        "62701",
        "US"
    );
    $billingAddress = new Address(
        "456 Oak Ave",
        "Springfield",
        "IL",
        "62702",
        "US"
    );

    $items = [
        [
            'productId' => 'PROD-001',
            'productName' => 'Widget Alpha',
            'price' => 29.99,
            'currency' => 'USD',
            'quantity' => 2,
        ],
        [
            'productId' => 'PROD-002',
            'productName' => 'Gadget Beta',
            'price' => 49.99,
            'currency' => 'USD',
            'quantity' => 1,
        ],
    ];

    try {
        $order = $orderService->placeOrder($customerId, $shippingAddress, $billingAddress, $items);
        echo "\n✅ Order placed: {$order->id()} | Total: {$order->total()}\n";
    } catch (\Throwable $e) {
        echo "\n❌ Error: {$e->getMessage()}\n";
    }
}

function handleCancel(\DDD\Application\OrderService $orderService): void
{
    echo "Order ID: ";
    $id = trim(fgets(STDIN));
    echo "Reason: ";
    $reason = trim(fgets(STDIN));

    try {
        $orderService->cancelOrder(new OrderId($id), $reason);
        echo "\n✅ Order {$id} cancelled.\n";
    } catch (\Throwable $e) {
        echo "\n❌ Error: {$e->getMessage()}\n";
    }
}

function handleGet(\DDD\Application\Query\OrderQuery $orderQuery): void
{
    echo "Order ID: ";
    $id = trim(fgets(STDIN));

    $summary = $orderQuery->getOrderSummary(new OrderId($id));
    if ($summary === null) {
        echo "Order not found.\n";
        return;
    }

    echo "\n── Order Summary ──\n";
    foreach ($summary as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
}

function handleList(\DDD\Application\Query\OrderQuery $orderQuery): void
{
    echo "Customer ID: ";
    $customerId = trim(fgets(STDIN));

    $orders = $orderQuery->getOrdersByCustomer($customerId);
    if (empty($orders)) {
        echo "No orders found for customer {$customerId}.\n";
        return;
    }

    echo "\n── Orders for {$customerId} ──\n";
    foreach ($orders as $order) {
        echo "  {$order->id()} | {$order->status()} | {$order->total()}\n";
    }
}

// Run the CLI
runInteractive($orderService, $orderQuery);
