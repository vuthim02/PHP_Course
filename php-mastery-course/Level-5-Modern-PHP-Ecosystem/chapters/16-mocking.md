# Chapter 16: Mocking and Stubs

## Learning Objectives

- Use Mockery for test doubles
- Create stubs and mocks
- Verify method expectations
- Apply partial mocks

---

## 16.1 Mockery

```php
<?php
use Mockery\Adapter\Phpunit\MockeryTestCase;

class OrderServiceTest extends MockeryTestCase
{
    public function test_places_order_successfully(): void
    {
        $repository = Mockery::mock(OrderRepository::class);
        $gateway = Mockery::mock(PaymentGateway::class);
        $mailer = Mockery::mock(Mailer::class);

        $orderData = ['total' => 100.00, 'items' => []];

        $repository->shouldReceive('create')
            ->once()
            ->with($orderData)
            ->andReturn(new Order(['id' => 1]));

        $gateway->shouldReceive('charge')
            ->once()
            ->with(100.00, Mockery::type('array'))
            ->andReturn(new PaymentResult(true, 'txn_123'));

        $mailer->shouldReceive('send')
            ->once()
            ->with(Mockery::on(function ($email) {
                return str_contains($email, 'Order confirmed');
            }));

        $service = new OrderService($repository, $gateway, $mailer);
        $result = $service->placeOrder($orderData);

        $this->assertTrue($result->success);
    }

    public function test_handles_gateway_failure(): void
    {
        $gateway = Mockery::mock(PaymentGateway::class);
        $gateway->shouldReceive('charge')
            ->once()
            ->andThrow(new GatewayException('Service unavailable'));

        $service = new OrderService(
            Mockery::mock(OrderRepository::class),
            $gateway,
            Mockery::mock(Mailer::class)
        );

        $this->expectException(OrderFailedException::class);
        $service->placeOrder(['total' => 50.00]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
```

---

## 16.2 Exercises

1. Use Mockery to test a service with 3+ dependencies
2. Verify method call order with `shouldReceive()->ordered()`
3. Create partial mocks for testing legacy code
4. Use argument matchers for flexible assertions

---

## Further Reading

- **Doc:** [Mockery](http://docs.mockery.io/)
- **Doc:** [PHPUnit Test Doubles](https://docs.phpunit.de/en/10.5/test-doubles.html)
