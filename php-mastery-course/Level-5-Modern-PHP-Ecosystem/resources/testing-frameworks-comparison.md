# PHP Testing Frameworks — PHPUnit & Pest

## PHPUnit vs Pest — Overview

| Aspect | PHPUnit | Pest |
|--------|---------|------|
| **Style** | Class-based (xUnit) | Function-based (RSpec/Mocha) |
| **Learning curve** | Moderate — OOP concepts needed | Gentle — reads like sentences |
| **Setup verbosity** | More boilerplate | Minimal boilerplate |
| **Output** | Standard TAP/JUnit | Colorful, expressive |
| **Under the hood** | Native testing framework | Wraps PHPUnit |
| **Test speed** | Same (identical engine) | Same |
| **Architect support** | No | Yes (architectural testing) |
| **Custom assertions** | `assert*` methods | `expect()->` chain |

## PHPUnit Example

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

    /** @dataProvider additionProvider */
    public function testAddition(int $a, int $b, int $expected): void
    {
        $this->assertEquals($expected, $this->calculator->add($a, $b));
    }

    public static function additionProvider(): array
    {
        return [
            [1, 2, 3],
            [0, 0, 0],
            [-1, 1, 0],
        ];
    }

    public function testDivisionByZeroThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->calculator->divide(10, 0);
    }

    protected function tearDown(): void
    {
        // Cleanup
    }
}
```

## Pest Example

```php
<?php

use App\Calculator;

// No class needed — just functions

test('adds two numbers', function () {
    $result = (new Calculator())->add(2, 3);

    expect($result)->toBe(5);
});

// More expressive
it('can add positive numbers', function () {
    expect((new Calculator())->add(1, 2))->toBe(3);
});

it('can add negative numbers', function () {
    expect((new Calculator())->add(-1, -2))->toBe(-3);
});

// Data provider
dataset('addition', [
    [1, 2, 3],
    [0, 0, 0],
    [-1, 1, 0],
]);

it('performs addition correctly', function (int $a, int $b, int $expected) {
    expect((new Calculator())->add($a, $b))->toBe($expected);
})->with('addition');

// Exception testing
it('throws exception when dividing by zero', function () {
    (new Calculator())->divide(10, 0);
})->throws(\InvalidArgumentException::class);

// Setup (runs before each test)
beforeEach(function () {
    $this->calculator = new Calculator();
});

// Teardown
afterEach(function () {
    // Cleanup
});
```

## Assertions Comparison

| PHPUnit | Pest |
|---------|------|
| `$this->assertEquals($a, $b)` | `expect($b)->toBe($a)` |
| `$this->assertSame($a, $b)` | `expect($b)->toBe($a)` (strict by default) |
| `$this->assertNull($v)` | `expect($v)->toBeNull()` |
| `$this->assertTrue($v)` | `expect($v)->toBeTrue()` |
| `$this->assertFalse($v)` | `expect($v)->toBeFalse()` |
| `$this->assertCount(3, $arr)` | `expect($arr)->toHaveCount(3)` |
| `$this->assertContains($v, $arr)` | `expect($arr)->toContain($v)` |
| `$this->assertStringContainsString($s, $str)` | `expect($str)->toContain($s)` |
| `$this->assertMatchesRegularExpression('/p/', $s)` | `expect($s)->toMatch('/p/')` |
| `$this->assertGreaterThan(5, $v)` | `expect($v)->toBeGreaterThan(5)` |
| `$this->assertLessThan(10, $v)` | `expect($v)->toBeLessThan(10)` |
| `$this->assertInstanceOf(Class::class, $v)` | `expect($v)->toBeInstanceOf(Class::class)` |

## Pest-Specific Features

### Higher-Order Tests
```php
// Test without explicit closure
test('numbers can be checked')
    ->expect(42)
    ->toBeInt()
    ->toBeGreaterThan(40)
    ->toBeLessThan(50);

// Higher-order collection testing
expect([1, 2, 3])
    ->each->toBeInt()
    ->sequence(
        fn ($n) => $n->toBe(1),
        fn ($n) => $n->toBe(2),
        fn ($n) => $n->toBe(3),
    );
```

### Architectural Testing
```php
test('controllers')
    ->expect('App\Http\Controllers')
    ->toUseStrictTypes()
    ->toExtendNothing()
    ->toBeFinal()
    ->toHaveMethods(['index', 'store', 'show', 'update', 'destroy']);

test('application uses no debug functions')
    ->expect(['dd', 'dump', 'var_dump'])
    ->not->toBeUsed();

test('services are final')
    ->expect('App\Services')
    ->classes()
    ->toBeFinal();
```

### Helpers
```php
// Custom helpers
function createUser(array $attributes = []): User
{
    return User::factory()->create($attributes);
}

// Use in tests
it('creates a user', function () {
    $user = createUser(['name' => 'Alice']);

    expect($user->name)->toBe('Alice');
});
```

## TDD Workflow

### Red-Green-Refactor with PHPUnit

```php
// 1. RED — Write failing test
class FizzBuzzTest extends TestCase
{
    public function testReturnsFizzForMultiplesOfThree(): void
    {
        $this->assertEquals('Fizz', fizzbuzz(3));
    }
}

// 2. GREEN — Write minimal code
function fizzbuzz(int $n): string
{
    if ($n % 3 === 0) {
        return 'Fizz';
    }
    return (string) $n;
}

// 3. REFACTOR — Clean up
// Add more tests, generalize, extract
```

### Red-Green-Refactor with Pest

```php
// RED
it('returns Fizz for multiples of three', function () {
    expect(fizzbuzz(3))->toBe('Fizz');
});

// GREEN
function fizzbuzz(int $n): string
{
    if ($n % 3 === 0) return 'Fizz';
    return (string) $n;
}

// REFACTOR — add more cases
dataset('fizzbuzz', [
    [1, '1'],
    [2, '2'],
    [3, 'Fizz'],
    [5, 'Buzz'],
    [15, 'FizzBuzz'],
]);

it('produces correct FizzBuzz output', function ($input, $expected) {
    expect(fizzbuzz($input))->toBe($expected);
})->with('fizzbuzz');
```

## Mocking Comparison

### PHPUnit
```php
$repository = $this->createMock(UserRepository::class);
$repository->expects($this->once())
    ->method('find')
    ->with(5)
    ->willReturn(new User('Alice'));

$repository->expects($this->never())
    ->method('delete');
```

### Pest (same PHPUnit engine)
```php
$repository = mock(UserRepository::class)
    ->shouldReceive('find')
    ->with(5)
    ->once()
    ->andReturn(new User('Alice'))
    ->getMock();
```

## Configuration Files

### phpunit.xml
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    bootstrap="vendor/autoload.php"
    colors="true"
    stopOnFailure="false"
    cacheDirectory=".phpunit.cache"
>
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
            <directory>app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
    </php>
</phpunit>
```

### Pest.php
```php
<?php

uses(Tests\TestCase::class)->in('Feature');

expect()->extend('toBeWithinRange', function (int $min, int $max) {
    return $this->toBeGreaterThanOrEqual($min)
        ->toBeLessThanOrEqual($max);
});
```

## CI Integration

```yaml
# GitHub Actions — test step
- name: Run tests
  run: |
    vendor/bin/phpunit --coverage-clover coverage.xml

- name: Run Pest
  run: |
    ./vendor/bin/pest --coverage --min=80
```

## Coverage Reports

```bash
# PHPUnit
vendor/bin/phpunit --coverage-html coverage/
vendor/bin/phpunit --coverage-clover coverage.xml
vendor/bin/phpunit --coverage-text --min-coverate=80

# Pest
./vendor/bin/pest --coverage
./vendor/bin/pest --coverage --min=80
./vendor/bin/pest --coverage-html coverage/
```

## Which to Choose?

**Choose PHPUnit when:**
- Working with legacy codebases
- Need maximum control over test structure
- Working on libraries/packages (PHPUnit is standard)
- Team is experienced with xUnit patterns
- Performance matters for thousands of tests

**Choose Pest when:**
- Starting a new project
- Team prefers expressive, English-like syntax
- Want architectural testing built-in
- Writing many similar tests (higher-order help)
- Teaching/testing beginners

**Pro tip:** They're compatible! You can mix PHPUnit and Pest in the same project. Start with Pest for new tests, keep existing PHPUnit tests as-is.
