# PHPUnit — Testing Quick Reference

## Setup

```bash
composer require --dev phpunit/phpunit
```

## Basic Test Class

```php
<?php
declare(strict_types=1);

namespace App\Tests;

use App\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    public function testAdd(): void
    {
        $result = $this->calculator->add(2, 3);
        $this->assertEquals(5, $result);
    }
}
```

## Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test file
vendor/bin/phpunit tests/CalculatorTest.php

# Run specific test method
vendor/bin/phpunit --filter testAdd

# Run tests with coverage
vendor/bin/phpunit --coverage-html coverage/

# Verbose output
vendor/bin/phpunit --verbose

# Stop on first failure
vendor/bin/phpunit --stop-on-failure
```

## PHPUnit Configuration

```xml
<!-- phpunit.xml -->
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/11.0/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         failOnRisky="true"
         failOnWarning="true"
         cacheDirectory=".phpunit.cache">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>src</directory>
        </include>
    </source>
</phpunit>
```

## Assertions

```php
// Equality
$this->assertEquals($expected, $actual);
$this->assertNotEquals($expected, $actual);
$this->assertSame($expected, $actual);       // === (strict)
$this->assertNotSame($expected, $actual);

// Truthiness
$this->assertTrue($condition);
$this->assertFalse($condition);
$this->assertNull($value);
$this->assertNotNull($value);

// Types
$this->assertIsArray($value);
$this->assertIsString($value);
$this->assertIsInt($value);
$this->assertIsFloat($value);
$this->assertIsObject($value);
$this->assertInstanceOf(MyClass::class, $object);

// Arrays / Iterables
$this->assertCount($expectedCount, $array);
$this->assertContains($needle, $haystack);
$this->assertArrayHasKey($key, $array);
$this->assertEmpty($array);
$this->assertNotEmpty($array);

// Strings
$this->assertStringContainsString($needle, $haystack);
$this->assertStringStartsWith($prefix, $string);
$this->assertStringEndsWith($suffix, $string);
$this->assertMatchesRegularExpression('/pattern/', $string);

// Exceptions
$this->expectException(InvalidArgumentException::class);
$this->expectExceptionMessage('Invalid value');
$this->expectExceptionCode(400);

// Numbers
$this->assertGreaterThan($expected, $actual);
$this->assertGreaterThanOrEqual($expected, $actual);
$this->assertLessThan($expected, $actual);
$this->assertLessThanOrEqual($expected, $actual);

// Floating point
$this->assertEqualsWithDelta(3.14, $computed, 0.001);
```

## Data Providers

```php
/**
 * @dataProvider additionProvider
 */
public function testAdd(int $a, int $b, int $expected): void
{
    $result = $this->calculator->add($a, $b);
    $this->assertEquals($expected, $result);
}

public static function additionProvider(): array
{
    return [
        'positive numbers' => [1, 2, 3],
        'negative numbers' => [-1, -2, -3],
        'zero'            => [0, 0, 0],
        'mixed'           => [-1, 1, 0],
    ];
}

// With named keys (PHP 8.1+)
public static function additionProvider(): iterable
{
    yield 'positive numbers' => [1, 2, 3];
    yield 'negative numbers' => [-1, -2, -3];
}
```

## Test Doubles (Mocks)

```php
// Create a mock
$logger = $this->createMock(LoggerInterface::class);

// Expect a method to be called
$logger->expects($this->once())
    ->method('log')
    ->with('User created');

// Return a value
$repository = $this->createMock(UserRepository::class);
$repository->method('find')
    ->with(5)
    ->willReturn(new User('Alice'));

// Different return values for multiple calls
$repository->method('find')
    ->willReturnMap([
        [1, new User('Alice')],
        [2, new User('Bob')],
        [3, null],
    ]);

// Consecutive calls
$repository->method('nextId')
    ->willReturnOnConsecutiveCalls(1, 2, 3);

// Throw exception
$repository->method('save')
    ->willThrowException(new DatabaseException('Connection lost'));

// Spy (verify after the fact)
$logger = $this->createMock(LoggerInterface::class);
$service = new UserService($logger);
$service->registerUser('Alice');
$this->assertEquals(1, $logger->count);
```

## Stubs vs Mocks

```php
// Stub — provides pre-defined answers, no expectations about calls
$stub = $this->createStub(Calculator::class);
$stub->method('add')->willReturn(5);

// Mock — has expectations about how it's called
$mock = $this->createMock(Calculator::class);
$mock->expects($this->once())->method('add')->with(2, 3);
```

## Partial Mocks

```php
// Mock only specific methods
$partial = $this->createPartialMock(UserService::class, ['sendEmail']);
$partial->method('sendEmail')->willReturn(true);
// All other methods work as usual
```

## Test Fixtures with Prophecy

```php
use Prophecy\PhpUnit\ProphecyTrait;

class OrderProcessorTest extends TestCase
{
    use ProphecyTrait;

    public function testProcessOrder(): void
    {
        $mailer = $this->prophesize(MailerInterface::class);
        $mailer->send('order@example.com', 'Order confirmed')
            ->shouldBeCalledOnce();

        $processor = new OrderProcessor($mailer->reveal());
        $processor->process(new Order(123));
    }
}
```

## Database Testing

```php
// Use transactions to roll back after each test
trait DatabaseTransactions
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->pdo->rollBack();
        parent::tearDown();
    }
}

// Or use a fresh SQLite in-memory database
class UserRepositoryTest extends TestCase
{
    private PDO $pdo;
    private UserRepository $repository;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec('CREATE TABLE users (id INTEGER, name TEXT)');
        $this->repository = new UserRepository($this->pdo);
    }
}
```

## Testing Exceptions

```php
public function testInvalidEmailThrowsException(): void
{
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('Invalid email format');
    $this->expectExceptionCode(422);

    $validator = new EmailValidator();
    $validator->validate('not-an-email');
}
```

## Testing Output

```php
public function testGreetingOutput(): void
{
    $greeter = new Greeter();

    $this->expectOutputString('Hello, World!');
    $greeter->greet();
}

public function testMatchesPattern(): void
{
    $this->expectOutputRegex('/Hello.*World/');
    echo 'Hello, beautiful World!';
}
```

## Risky Tests

```php
// Useless test — no assertions
public function testNothing(): void
{
    $this->assertTrue(true);  // Not useful!
}

// Better — test real behavior
public function testCalculatorExists(): void
{
    $this->assertInstanceOf(Calculator::class, new Calculator());
}
```

## Best Practices

1. **One assertion per logical test** — But one test can have multiple related assertions.
2. **Test behavior, not implementation** — Don't test private methods; test through public API.
3. **Arrange-Act-Assert** pattern:
   ```php
   public function testSomething(): void
   {
       // Arrange
       $service = new UserService();

       // Act
       $result = $service->doSomething();

       // Assert
       $this->assertEquals($expected, $result);
   }
   ```
4. **Use meaningful test names** — `testUserCannotRegisterWithInvalidEmail()`.
5. **Don't test PHP features** — You don't need to test that `array_merge` works.
6. **Write tests before code (TDD)** — Red → Green → Refactor.
7. **Keep tests fast** — Milliseconds per test. Slow tests don't get run.
8. **Use dependency injection** — Makes mocking trivial.
