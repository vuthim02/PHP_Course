# Level 3 — Advanced PHP Quick Reference

## Reflection API

```php
class User {
    public function __construct(
        public string $name,
        private string $password
    ) {}
    public function greet(): string {
        return "Hello, $this->name";
    }
}

// Introspect a class
$ref = new ReflectionClass(User::class);
$ref->getProperties();        // All properties
$ref->getMethods();           // All methods
$ref->getConstructor();       // Constructor reflection
$ref->newInstance('Alice', 'secret');  // Instantiate

// Introspect a method
$method = $ref->getMethod('greet');
$method->getParameters();     // Parameter list
$method->getReturnType();     // Return type

// Invoke method on instance
$user = $ref->newInstance('Alice', 'secret');
$method->invoke($user);       // "Hello, Alice!"

// Access private property
$prop = $ref->getProperty('password');
$prop->setAccessible(true);
$prop->getValue($user);       // "secret"
```

## Attributes (PHP 8+)

```php
// Define attribute
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class Validate
{
    public function __construct(
        public string $rule,
        public ?int $min = null,
        public ?int $max = null
    ) {}
}

// Use attribute
#[Validate('string', min: 3, max: 50)]
class User
{
    #[Validate('email')]
    public string $email;

    #[Validate('required')]
    public string $name;
}

// Read attributes at runtime
$ref = new ReflectionClass(User::class);
$classAttrs = $ref->getAttributes(Validate::class);
foreach ($classAttrs as $attr) {
    $instance = $attr->newInstance();
    echo $instance->rule; // "string"
}

foreach ($ref->getProperties() as $prop) {
    $propAttrs = $prop->getAttributes(Validate::class);
    foreach ($propAttrs as $attr) {
        $instance = $attr->newInstance();
        echo "{$prop->name}: {$instance->rule}";
    }
}
```

## Anonymous Classes

```php
$logger = new class('/tmp/log.txt') implements LoggerInterface {
    public function __construct(
        private string $path
    ) {}

    public function log(string $msg): void
    {
        file_put_contents($this->path, $msg . "\n", FILE_APPEND);
    }
};

// Useful for one-off implementations in tests
$mock = new class extends AbstractService {
    public function process(): void
    {
        $this->processed = true;
    }
};
```

## Closures & Callables

```php
// Closure — anonymous function that captures scope
$multiplier = 3;
$double = function (int $n) use ($multiplier): int {
    return $n * $multiplier;
};
echo $double(5); // 15

// By-reference capture
$count = 0;
$counter = function () use (&$count): void {
    $count++;
};
$counter();
$counter();
echo $count; // 2

// Arrow functions — auto-capture by-value, single expression
$triple = fn(int $n): int => $n * 3;

// Callable types
function process(callable $callback, int $value): mixed {
    return $callback($value);
}

echo process(fn($n) => $n ** 2, 5); // 25
echo process('strtoupper', 'hello'); // "HELLO"

// Closure binding
class App {
    private string $name = 'MyApp';
}

$closure = function (): string {
    return $this->name;
};
$bound = $closure->bindTo(new App(), App::class);
echo $bound(); // "MyApp"

// Or use Closure::call (PHP 7+)
echo $closure->call(new App()); // "MyApp"
```

## Generators

```php
// Memory-efficient iteration over large datasets
function rangeGenerator(int $start, int $end): Generator
{
    for ($i = $start; $i <= $end; $i++) {
        yield $i;  // Pause and return $i
    }
}

foreach (rangeGenerator(1, 1000000) as $number) {
    if ($number > 10) break;  // Only generates what's needed
    echo $number;
}

// Key-value yields
function fileLines(string $path): Generator
{
    $handle = fopen($path, 'r');
    $lineNum = 0;
    while (($line = fgets($handle)) !== false) {
        yield ++$lineNum => trim($line);
    }
    fclose($handle);
}

foreach (fileLines('large.csv') as $num => $line) {
    echo "$num: $line\n";
}

// Generator delegation (yield from)
function append($gen1, $gen2): Generator
{
    yield from $gen1;
    yield from $gen2;
}

// Sending values to generators
function accumulator(): Generator
{
    $sum = 0;
    while (true) {
        $value = yield $sum;
        $sum += $value;
    }
}

$gen = accumulator();
$gen->send(10);  // 10 (initial sum after first send)
echo $gen->send(5);   // 15
echo $gen->send(3);   // 18
```

## Iterators

```php
// Built-in iterators
$files = new DirectoryIterator(__DIR__);
foreach ($files as $file) {
    if ($file->isFile()) {
        echo $file->getFilename() . "\n";
    }
}

// FilterIterator
class LargeFileFilter extends FilterIterator
{
    public function accept(): bool
    {
        return $this->current()->getSize() > 1024 * 1024;
    }
}

// IteratorIterator — wraps any Traversable
$iterator = new IteratorIterator(new ArrayIterator([1, 2, 3]));

// RecursiveDirectoryIterator
$dir = new RecursiveDirectoryIterator(__DIR__);
$it = new RecursiveIteratorIterator($dir);
foreach ($it as $file) {
    echo $file->getPathname() . "\n";
}

// AppendIterator
$combined = new AppendIterator();
$combined->append(new ArrayIterator(['a', 'b']));
$combined->append(new ArrayIterator(['c', 'd']));
```

## Streams

```php
// Stream wrappers
file_get_contents('php://input');           // HTTP request body
file_put_contents('php://output', $data);   // HTTP response
file_get_contents('php://memory');         // In-memory stream

// Custom stream context
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => json_encode(['key' => 'value']),
    ],
]);
$result = file_get_contents('https://api.example.com', false, $context);

// Stream filters
stream_filter_append($handle, 'string.toupper');

// php://filter
$content = file_get_contents(
    'php://filter/read=convert.base64-encode/resource=file.txt'
);
```

## SPL Data Structures

```php
// SplStack — LIFO
$stack = new SplStack();
$stack->push('first');
$stack->push('second');
echo $stack->pop(); // "second"

// SplQueue — FIFO
$queue = new SplQueue();
$queue->enqueue('first');
$queue->enqueue('second');
echo $queue->dequeue(); // "first"

// SplHeap
class MaxHeap extends SplMaxHeap {
    public function compare(mixed $a, mixed $b): int {
        return $a <=> $b;
    }
}

// SplFixedArray — faster than array for fixed-size
$arr = new SplFixedArray(100);
$arr[0] = 'value';

// SplObjectStorage — map objects to data
$storage = new SplObjectStorage();
$user = new User('Alice');
$storage[$user] = ['visits' => 42];
```

## Performance Optimization

```php
// Avoid expensive operations in loops
$count = count($items);  // Cache the count
for ($i = 0; $i < $count; $i++) { ... }

// Use foreach instead of for
foreach ($items as $item) { ... }

// Prefer single-quoted strings
$str = 'literal string';  // Faster — no interpolation

// Use === instead of == (faster + safer)
if ($a === $b) { ... }

// String building
$result = implode('', $parts);      // Fast
// vs
$result = '';
foreach ($parts as $p) {
    $result .= $p;                  // Slow — reallocates
}

// Use array functions over loops
$squared = array_map(fn($n) => $n ** 2, $numbers);

// Cache repetitive computations
$hash = md5($value);  // Compute once, reuse

// Opcode caching — enable OPcache in php.ini
opcache.enable=1
opcache.memory_consumption=256
```

## Memory Management

```php
// Free large variables
$largeDataset = loadHugeData();
processData($largeDataset);
unset($largeDataset);  // Free memory

// Memory usage tracking
echo memory_get_usage();
echo memory_get_peak_usage();

// Reference counting basics
$a = new stdClass();  // refcount = 1
$b = $a;              // refcount = 2
unset($a);            // refcount = 1
unset($b);            // refcount = 0 → destroyed

// Circular reference cleanup
class Node {
    public ?Node $next = null;
}
$a = new Node();
$b = new Node();
$a->next = $b;
$b->next = $a;  // Circular — refcount never reaches 0
// PHP's garbage collector handles this in cycles
```

## Late Static Binding

```php
class Base
{
    public static string $name = 'Base';

    public static function whoAmI(): string
    {
        return __CLASS__;    // Resolves to Base
    }

    public static function whoAreWe(): string
    {
        return static::class;  // Resolves at runtime
    }

    public static function create(): static
    {
        return new static();  // Returns calling class
    }
}

class Child extends Base
{
    public static string $name = 'Child';
}

echo Base::whoAmI();     // "Base"
echo Child::whoAmI();    // "Base"
echo Child::whoAreWe();  // "Child"
echo Child::create()::class; // "Child"
```

## Type System

```php
declare(strict_types=1);

// Union types
function process(int|string $value): int|string { ... }

// Intersection types
function log(LoggerInterface&Countable $obj): void { ... }

// Mixed type
function handle(mixed $data): void { ... }

// Never return type
function abort(string $msg): never {
    throw new \RuntimeException($msg);
}

// Void return
function logMessage(string $msg): void { ... }

// Nullable types
function find(?int $id): ?User { ... }
function greet(?string $name = null): string { ... }

// False type (rarely needed)
function findUser(int $id): User|false { ... }

// True type (PHP 8.2+)
function verify(): true { return true; }

// Readonly properties
readonly class Config  // PHP 8.2+ — all properties readonly
{
    public function __construct(
        public string $dbHost,
        public int $dbPort
    ) {}
}
```
