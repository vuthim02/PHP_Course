# Chapter 13: Testing with PHPUnit

## Learning Objectives

- Write comprehensive unit tests
- Implement integration tests
- Use test doubles effectively
- Organize test suites

---

## 13.1 Test Structure

```php
<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\OrderService;
use App\Repositories\OrderRepository;
use App\Billing\PaymentGateway;
use App\Models\Order;

class OrderServiceTest extends TestCase
{
    private OrderService $service;
    private OrderRepository $repository;
    private PaymentGateway $gateway;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(OrderRepository::class);
        $this->gateway = $this->createMock(PaymentGateway::class);
        $this->service = new OrderService($this->repository, $this->gateway);
    }

    /** @test */
    public function it_creates_order_and_charges_payment(): void
    {
        $orderData = [
            'user_id' => 1,
            'items' => [['product_id' => 1, 'quantity' => 2]],
            'total' => 50.00,
        ];

        $this->repository->expects($this->once())
            ->method('create')
            ->with($orderData)
            ->willReturn(new Order(['id' => 1]));

        $this->gateway->expects($this->once())
            ->method('charge')
            ->with(50.00, ['order_id' => 1])
            ->willReturn(true);

        $result = $this->service->placeOrder($orderData);

        $this->assertTrue($result->success);
        $this->assertEquals(1, $result->orderId);
    }

    /** @test */
    public function it_handles_payment_failure(): void
    {
        $this->repository->method('create')->willReturn(new Order(['id' => 2]));
        $this->gateway->method('charge')->willReturn(false);

        $this->expectException(PaymentFailedException::class);

        $this->service->placeOrder(['total' => 100.00]);
    }

    /** @test */
    public function it_retries_failed_payment(): void
    {
        $this->repository->method('create')->willReturn(new Order(['id' => 3]));
        
        $this->gateway->expects($this->exactly(3))
            ->method('charge')
            ->willReturnOnConsecutiveCalls(false, false, true);

        $result = $this->service->placeOrder(['total' => 75.00], retries: 3);
        $this->assertTrue($result->success);
    }
}
```

---

## 13.2 Exercises

1. Write unit tests for a service layer with mocked dependencies
2. Test exception handling and error cases
3. Implement data providers for edge cases
4. Create integration tests with a test database

---

## Further Reading

- **Doc:** [PHPUnit](https://phpunit.de/documentation.html)
