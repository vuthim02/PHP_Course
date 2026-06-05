# Chapter 21: Testing with PHPUnit — Unit Tests

## Learning Objectives

- Install and configure PHPUnit
- Write unit tests with assertions
- Use data providers
- Test exceptions

---

## 21.1 Getting Started

```php
<?php
// tests/Unit/CalculatorTest.php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Calculator;

class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    /** @test */
    public function it_can_add_two_numbers(): void
    {
        $result = $this->calculator->add(2, 3);
        $this->assertEquals(5, $result);
    }

    /** @test */
    public function it_can_subtract_numbers(): void
    {
        $result = $this->calculator->subtract(10, 4);
        $this->assertEquals(6, $result);
    }

    /** @test */
    public function it_can_multiply_numbers(): void
    {
        $result = $this->calculator->multiply(4, 5);
        $this->assertEquals(20, $result);
    }

    /** @test */
    public function it_can_divide(): void
    {
        $result = $this->calculator->divide(10, 2);
        $this->assertEquals(5.0, $result);
    }
}
```

---

## 21.2 Data Providers

```php
<?php
class CalculatorTest extends TestCase
{
    /** @dataProvider additionProvider */
    public function test_addition(int $a, int $b, int $expected): void
    {
        $this->assertEquals($expected, $this->calculator->add($a, $b));
    }

    public static function additionProvider(): array
    {
        return [
            'positive numbers' => [2, 3, 5],
            'negative numbers' => [-1, -2, -3],
            'zero' => [0, 0, 0],
            'large numbers' => [1000000, 2000000, 3000000],
        ];
    }

    /** @dataProvider divisionProvider */
    public function test_division(int $a, int $b, float $expected): void
    {
        $this->assertEquals($expected, $this->calculator->divide($a, $b));
    }

    public static function divisionProvider(): array
    {
        return [
            'simple division' => [10, 2, 5.0],
            'fraction result' => [1, 3, 0.3333],
            'one' => [5, 1, 5.0],
        ];
    }
}
```

---

## 21.3 Testing Exceptions

```php
<?php
class CalculatorTest extends TestCase
{
    public function test_division_by_zero_throws_exception(): void
    {
        $this->expectException(\DivisionByZeroError::class);
        $this->expectExceptionMessage('Division by zero');
        
        $this->calculator->divide(10, 0);
    }

    public function test_square_root_of_negative_number(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot calculate square root of negative number');
        
        $this->calculator->sqrt(-1);
    }
}
```

---

## 21.4 Mocking

```php
<?php
use PHPUnit\Framework\TestCase;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Notifications\WelcomeEmail;

class UserServiceTest extends TestCase
{
    /** @test */
    public function it_sends_welcome_email_on_registration(): void
    {
        $repository = $this->createMock(UserRepository::class);
        $notifier = $this->createMock(WelcomeEmail::class);

        $userData = ['name' => 'John', 'email' => 'john@example.com'];

        $repository->method('create')->willReturn(new User(1, ...$userData));
        
        $notifier->expects($this->once())
            ->method('send')
            ->with($this->callback(fn($user) => $user->email === 'john@example.com'));

        $service = new UserService($repository, $notifier);
        $result = $service->register($userData);

        $this->assertEquals('john@example.com', $result->email);
    }
}
```

---

## 21.5 Exercises

1. Write unit tests for a `StringHelper` class with `reverse()`, `slugify()`, `truncate()`
2. Use data providers to test with multiple inputs
3. Test that exceptions are thrown for invalid inputs
4. Mock a database repository and test service logic

---

## Further Reading

- **Doc:** [PHPUnit Documentation](https://phpunit.de/documentation.html)
- **Doc:** [PHPUnit Assertions](https://docs.phpunit.de/en/11.0/assertions.html)
