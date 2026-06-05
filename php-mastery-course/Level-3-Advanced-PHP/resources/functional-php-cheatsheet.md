# Functional PHP — Quick Reference

## First-Class Functions

```php
// Assign function to variable
$greet = function (string $name): string {
    return "Hello, $name!";
};
echo $greet('Alice');

// Pass as argument
function apply(callable $fn, int $value): int {
    return $fn($value);
}
echo apply(fn(int $n): int => $n ** 2, 5); // 25

// Return a function
function multiplier(int $factor): callable {
    return fn(int $n): int => $n * $factor;
}
$double = multiplier(2);
echo $double(5); // 10
```

## Array Functions (Map / Filter / Reduce)

```php
$numbers = [1, 2, 3, 4, 5, 6];

// map — transform each element
$squared = array_map(fn(int $n): int => $n ** 2, $numbers);
// [1, 4, 9, 16, 25, 36]

// filter — keep elements matching predicate
$evens = array_filter($numbers, fn(int $n): bool => $n % 2 === 0);
// [2, 4, 6]  (note: keys preserved!)

// reindex after filter
$evens = array_values(array_filter($numbers, ...));

// reduce — accumulate value
$sum = array_reduce($numbers, fn(int $carry, int $n): int => $carry + $n, 0);
// 21

// Product
$product = array_reduce($numbers, fn(int $carry, int $n): int => $carry * $n, 1);
// 720

// Chain them
$result = array_reduce(
    array_map(fn($n) => $n ** 2,
        array_filter($numbers, fn($n) => $n % 2 === 0)
    ),
    fn($carry, $n) => $carry + $n,
    0
);
// 4² + 6² = 16 + 36 = 52
```

## Closures & Scope

```php
// Closures capture variables from surrounding scope
$items = ['apple', 'banana', 'cherry'];
$prefix = 'fruit: ';
$prefixed = array_map(
    function (string $item) use ($prefix): string {
        return $prefix . $item;
    },
    $items
);

// By-reference capture
$count = 0;
$counter = function () use (&$count): void {
    $count++;
};
$counter();
echo $count; // 1

// Early binding — use captures value at closure creation
$messages = [];
for ($i = 0; $i < 3; $i++) {
    $messages[] = function () use ($i): int {
        return $i;  // Captures $i by value AT CREATION
    };
}
echo $messages[0](); // 0
echo $messages[1](); // 1

// Late binding analogy — use reference
$funcs = [];
for ($i = 0; $i < 3; $i++) {
    $funcs[] = function () use (&$i): int {
        return $i;  // Captures reference — all see final $i
    };
}
// After loop, $i = 3
echo $funcs[0](); // 3 (all return 3!)
```

## Arrow Functions (PHP 7.4+)

```php
// Arrow functions are closures with implicit by-value capture
$numbers = [1, 2, 3, 4, 5];
$factor = 10;
$multiplied = array_map(fn($n) => $n * $factor, $numbers);
// [10, 20, 30, 40, 50]

// Multiple statements? Use regular closure (arrow = single expression)
$processed = array_map(function ($n) use ($factor) {
    $temp = $n * $factor;
    return $temp - 1;  // Can't do this with arrow
}, $numbers);

// Nested arrow functions
$result = array_map(
    fn($row) => array_map(fn($col) => $col * 2, $row),
    [[1, 2], [3, 4]]
);
// [[2, 4], [6, 8]]
```

## Higher-Order Functions

```php
// Currying — transform multi-arg function into chain of single-arg
function add(int $a): callable {
    return fn(int $b): int => $a + $b;
}
$addFive = add(5);
echo $addFive(3); // 8
echo add(5)(3);   // 8

// Partial application — fix some arguments
function greet(string $greeting, string $name): string {
    return "$greeting, $name!";
}
$sayHello = fn(string $name) => greet('Hello', $name);
echo $sayHello('Alice'); // "Hello, Alice!"

// Composition — chain functions
function compose(callable $f, callable $g): callable {
    return fn(mixed $x): mixed => $f($g($x));
}
$addOne = fn(int $n): int => $n + 1;
$double = fn(int $n): int => $n * 2;
$addOneThenDouble = compose($double, $addOne);
echo $addOneThenDouble(5); // (5+1)*2 = 12
```

## Immutability Patterns

```php
// Don't mutate — return new values
// ❌ Mutable approach
$user = ['name' => 'Alice', 'role' => 'user'];
$user['role'] = 'admin';  // Mutation!

// ✅ Immutable approach
$user = ['name' => 'Alice', 'role' => 'user'];
$promoted = array_merge($user, ['role' => 'admin']);
// Original $user unchanged

// With readonly objects (PHP 8.2+)
readonly class User
{
    public function __construct(
        public string $name,
        public string $role = 'user'
    ) {}

    public function withRole(string $role): self
    {
        return new self($this->name, $role);
    }
}

$user = new User('Alice');
$admin = $user->withRole('admin');
echo $user->role;   // "user" — original unchanged
echo $admin->role;  // "admin"

// Immutable collections
$collection = [1, 2, 3];
$newCollection = [...$collection, 4]; // [1,2,3,4] — original intact
```

## Pure Functions

```php
// ❌ Impure — modifies external state
$total = 0;
function addToTotal(int $n): void {
    global $total;
    $total += $n;  // Side effect!
}

// ✅ Pure — no side effects, same input → same output
function add(int $a, int $b): int {
    return $a + $b;
}

// ❌ Impure — reads external state (time)
function isMorning(): bool {
    return (int)date('H') < 12;  // Output depends on when called
}

// ✅ Pure
function isMorningAt(int $hour): bool {
    return $hour < 12;
}

// ❌ Impure — file I/O
function logMessage(string $msg): void {
    file_put_contents('log.txt', $msg, FILE_APPEND);
}

// ✅ Pure — return data, leave I/O to caller
function formatLogMessage(string $msg): string {
    return '[' . date('Y-m-d H:i:s') . '] ' . $msg;
}
```

## Recursion

```php
// Classic recursion
function factorial(int $n): int {
    if ($n <= 1) return 1;
    return $n * factorial($n - 1);
}

// Tail recursion (PHP doesn't optimize tail calls, but pattern is useful)
function factorialTail(int $n, int $acc = 1): int {
    if ($n <= 1) return $acc;
    return factorialTail($n - 1, $n * $acc);
}

// Recursive directory traversal
function findPhpFiles(string $dir): array {
    $files = [];
    foreach (new DirectoryIterator($dir) as $item) {
        if ($item->isDot()) continue;
        if ($item->isDir()) {
            $files = array_merge($files, findPhpFiles($item->getPathname()));
        } elseif ($item->getExtension() === 'php') {
            $files[] = $item->getPathname();
        }
    }
    return $files;
}
```

## Monad-like Patterns (Optional)

```php
// Maybe monad — handle null without if statements
final class Maybe
{
    private function __construct(
        private readonly mixed $value
    ) {}

    public static function of(mixed $value): self
    {
        return new self($value);
    }

    public function bind(callable $fn): self
    {
        if ($this->value === null) {
            return new self(null);
        }
        return $fn($this->value);
    }

    public function getOrElse(mixed $default): mixed
    {
        return $this->value ?? $default;
    }
}

// Usage — no if statements for null checks
$user = Maybe::of(findUser(5))
    ->bind(fn($user) => Maybe::of($user->getAddress()))
    ->bind(fn($addr) => Maybe::of($addr->getCity()))
    ->getOrElse('Unknown City');

// Result monad — handle errors without exceptions
final class Result
{
    private function __construct(
        private readonly mixed $value,
        private readonly ?\Throwable $error = null
    ) {}

    public static function ok(mixed $value): self
    {
        return new self($value, null);
    }

    public static function fail(\Throwable $error): self
    {
        return new self(null, $error);
    }

    public function bind(callable $fn): self
    {
        if ($this->error !== null) {
            return $this;
        }
        try {
            return Result::ok($fn($this->value));
        } catch (\Throwable $e) {
            return Result::fail($e);
        }
    }

    public function getOrElse(mixed $default): mixed
    {
        return $this->error ? $default : $this->value;
    }
}
```

## Functional Pipeline Pattern

```php
// Create a pipeline of transformations
class Pipeline
{
    private array $stages = [];

    public static function make(mixed $initial): self
    {
        return (new self())->pipe(fn() => $initial);
    }

    public function pipe(callable $stage): self
    {
        $clone = clone $this;
        $clone->stages[] = $stage;
        return $clone;
    }

    public function process(): mixed
    {
        $result = null;
        foreach ($this->stages as $stage) {
            $result = $stage($result);
        }
        return $result;
    }
}

// Usage
$result = Pipeline::make([1, 2, 3, 4, 5, 6])
    ->pipe(fn($data) => array_filter($data, fn($n) => $n % 2 === 0))
    ->pipe(fn($data) => array_map(fn($n) => $n ** 2, $data))
    ->pipe(fn($data) => array_reduce($data, fn($c, $n) => $c + $n, 0))
    ->process();

echo $result; // 2² + 4² + 6² = 56
```

## Best Practices

1. **Prefer `array_map`/`array_filter`/`array_reduce` over loops** for data transformation
2. **Avoid global state** — pass values explicitly
3. **Don't mutate** — return new values instead
4. **Use `readonly` classes** for immutable data objects
5. **Keep functions small** and focused on one thing
6. **Pure functions are easier to test** — no setup needed
7. **Use closures for callbacks**, arrow functions for simple transformations
8. **Avoid deep recursion** in PHP — use iteration or generators for large data
