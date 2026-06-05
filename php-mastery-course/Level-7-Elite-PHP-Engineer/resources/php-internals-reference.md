# PHP Internals — Reference for Elite Engineers

## Zend Engine Architecture

```
PHP File
    │
    ▼
[ Lexer ] → Token stream
    │
    ▼
[ Parser ] → AST (Abstract Syntax Tree)
    │
    ▼
[ Compiler ] → Opcodes
    │
    ▼
[ Zend Engine ] → Opcode execution
    │
    ▼
[ CPU ]
```

### Opcodes Example

```php
// PHP code
$a = 1;
$b = 2;
$c = $a + $b;

// Corresponding opcodes (roughly):
// ASSIGN $a, 1
// ASSIGN $b, 2
// ADD $c, $a, $b
// RETURN 1  (if this is a script return)
```

## Memory Management

### Reference Counting

```php
// Each zval (PHP value) has a refcount
// refcount = 0 → garbage collected

$a = new stdClass();
// refcount of stdClass object = 1

$b = $a;
// refcount = 2 (both $a and $b point to same zval)

unset($a);
// refcount = 1

unset($b);
// refcount = 0 → object destroyed

// Copy-on-write for arrays
$x = [1, 2, 3];
$y = $x;       // refcount = 2, no copy
$y[] = 4;      // Write → separate copy made (refcount $x = 1, $y = 1)
```

### Circular Reference GC

```php
class Node
{
    public ?Node $parent = null;
    public ?Node $child = null;
}

$a = new Node();
$b = new Node();
$a->child = $b;
$b->parent = $a;

// refcount = 2 for each (variable + property reference)
// unset both:
unset($a);
unset($b);
// Traditional refcounting can't free these — circular!
// PHP's GC detects cycles via "root buffer" + "purple" marking
```

## Zval Structure (Simplified)

```c
// Internal zval struct (PHP 8.x)
struct _zval_struct {
    zend_value value;        // Union: long, double, str*, arr*, obj*, etc.
    union {
        uint32_t type_info;  // IS_STRING, IS_ARRAY, IS_OBJECT, etc.
        struct {
            zend_uchar type;
            zend_uchar type_flags;
            zend_uchar const_flags;
            zend_uchar reserved;
        };
    } u1;
    union {
        uint32_t next;       // HashTable collision chain
        uint32_t cache_slot;
        uint32_t lineno;     // For AST nodes
        uint32_t num_args;
        uint32_t fe_pos;     // foreach position
        uint32_t guard;
    } u2;
};
```

## JIT Compilation (PHP 8.0+)

```
Without JIT:
PHP → Opcodes → Zend VM (interpreter) → CPU

With JIT (tracing):
PHP → Opcodes → Zend VM → Profile hot paths → Machine code → CPU

With JIT (function):
PHP → Opcodes → Compile to machine code → CPU (no VM involved)
```

### JIT Configuration

```ini
; php.ini
opcache.jit = 1255     ; CRTO (CPU-specific, tracing, optimize)
opcache.jit_buffer_size = 100M
opcache.jit_debug = 0
opcache.jit_bisect_limit = 0
opcache.jit_prof_threshold = 0.005

; JIT flags breakdown:
; 1st digit (C): CPU-specific optimization
;   0 = no, 1 = yes, 2 = no + large loop, 3 = yes + large loop
; 2nd digit (R): Register allocation
;   0 = none, 1 = local, 2 = global, 3 = weighted
; 3rd digit (T): Trigger
;   0 = on request, 1 = first exec, 2 = hot (counter), 3 = profiling
; 4th digit (O): Optimization level
;   0 = none, 1 = minimal, 2 = selective, 3 = full, 4 = thorough, 5 = max
```

## Writing a PHP Extension

### Ext Skeleton

```c
// config.m4
PHP_ARG_ENABLE(myext, whether to enable myext support,
[  --enable-myext          Enable myext support])

if test "$PHP_MYEXT" != "no"; then
  PHP_NEW_EXTENSION(myext, myext.c, $ext_shared)
fi

// myext.c
#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "ext/standard/info.h"

// Declare function
PHP_FUNCTION(myext_hello);

// Function table
static const zend_function_entry myext_functions[] = {
    PHP_FE(myext_hello, NULL)
    PHP_FE_END
};

// Module entry
zend_module_entry myext_module_entry = {
    STANDARD_MODULE_HEADER,
    "myext",
    myext_functions,
    NULL, NULL, NULL, NULL,
    PHP_MINFO(myext),
    PHP_MYEXT_VERSION,
    STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_MYEXT
ZEND_GET_MODULE(myext)
#endif

// Function implementation
PHP_FUNCTION(myext_hello)
{
    zend_string *name;

    ZEND_PARSE_PARAMETERS_START(1, 1)
        Z_PARAM_STR(name)
    ZEND_PARSE_PARAMETERS_END();

    php_printf("Hello, %s!", ZSTR_VAL(name));
    RETURN_TRUE;
}

PHP_MINFO_FUNCTION(myext)
{
    php_info_print_table_start();
    php_info_print_table_header(2, "myext support", "enabled");
    php_info_print_table_end();
}
```

### Building the Extension

```bash
# Build
phpize
./configure --enable-myext
make
make install

# Enable in php.ini
extension=myext.so

# Verify
php -m | grep myext
```

## PHP RFC Process

### Lifecycle of an RFC

```
1. Pre-RFC Discussion (internals@lists.php.net)
2. Draft RFC on wiki.php.net/rfc/
3. Vote by PHP internals contributors
   - 2/3 majority required
   - Minimum 50% + 1 of all votes
4. Implementation
5. Merged into php-src
6. Available in next PHP version
```

### Notable PHP 8.x RFCs

| RFC | PHP Version | Impact |
|-----|-------------|--------|
| Named Arguments | 8.0 | Skip default params |
| Attributes | 8.0 | Structured metadata |
| Constructor Property Promotion | 8.0 | Less boilerplate |
| Union Types | 8.0 | Type flexibility |
| Match Expression | 8.0 | Safer switch |
| JIT | 8.0 | Performance 2-5x |
| Enumerations | 8.1 | Native enums |
| Fibers | 8.1 | Cooperative multitasking |
| Readonly Properties | 8.1 | True immutability |
| Deprecate dynamic properties | 8.2 | Stricter classes |
| Readonly Classes | 8.2 | Immutable objects |
| Random Extension | 8.2 | Better RNG |
| JSON Validation | 8.3 | json_validate() |
| Override Attribute | 8.3 | Inheritance safety |

## HashTable Internals

```php
// PHP arrays are ordered hash maps (HashTable)
$arr = ['foo' => 'bar', 'baz' => 'qux'];
```

```c
// Internal HashTable structure
struct _zend_array {
    zend_refcounted_h gc;
    union {
        struct {
            ZEND_ENDIAN_LOHI_4(
                zend_uchar    flags,
                zend_uchar    _unused,
                zend_uchar    nIteratorsCount,
                zend_uchar    _unused)
        } v;
        uint32_t flags;
    } u;
    uint32_t          nTableMask;    // -nTableSize
    Bucket           *arData;        // Array of buckets
    uint32_t          nNumUsed;      // Number of used slots
    uint32_t          nNumOfElements; // Number of elements
    uint32_t          nTableSize;    // Power of 2
    uint32_t          nInternalPointer;
    zend_long         nNextFreeElement;
    dtor_func_t       pDestructor;
};
```

## PHP Internals Tools

### Opcache Inspection

```php
// Show all cached scripts
var_dump(opcache_get_status());

// Reset cache
opcache_reset();

// Invalidate specific file
opcache_invalidate('file.php', true);

// Preloading (PHP 7.4+)
// php.ini:
opcache.preload = config/preload.php
opcache.preload_user = www-data
```

```php
// config/preload.php
// Classes in here are "preloaded" into shared memory
require_once __DIR__ . '/../vendor/autoload.php';

// Preload commonly used classes
$classes = [
    App\Domain\User\Entity\User::class,
    App\Domain\User\ValueObject\Email::class,
    // ...
];

foreach ($classes as $class) {
    if (class_exists($class, false)) {
        echo "Preloaded: $class\n";
    }
}
```

### PHP Configuration Inspection

```php
// See all PHP settings
phpinfo();

// Check specific config
ini_get('memory_limit');
ini_get('max_execution_time');
ini_get('opcache.enable');

// Check if extension loaded
extension_loaded('pdo_mysql');
extension_loaded('redis');

// List all loaded extensions
get_loaded_extensions();
```

### Debugging Internal Behavior

```php
// Track opcache hits/misses
$status = opcache_get_status(false);
echo "Hits: {$status['opcache_statistics']['hits']}\n";
echo "Misses: {$status['opcache_statistics']['misses']}\n";
echo "Hit rate: {$status['opcache_statistics']['opcache_hit_rate']}%\n";

// Memory usage
$status = opcache_get_status(false);
echo "Memory used: {$status['memory_usage']['used_memory']} bytes\n";
echo "Memory free: {$status['memory_usage']['free_memory']} bytes\n";

// JIT status
$jit = $status['jit'] ?? null;
echo "JIT enabled: " . ($jit ? 'yes' : 'no') . "\n";
echo "JIT buffer: {$jit['buffer_size']} bytes\n";
echo "JIT buffer free: {$jit['buffer_free']} bytes\n";
```

## Fiber (Coroutines) — PHP 8.1+

```php
// Cooperative multitasking within a single thread
$fiber = new Fiber(function (): void {
    $value = Fiber::suspend('first');
    echo "Value: $value\n";
    Fiber::suspend('second');
});

// Start fiber
$result = $fiber->start();
echo $result; // 'first'

// Resume with value
$fiber->resume('hello');
// Output: "Value: hello"
```

## Contributing to PHP

```bash
# Clone php-src
git clone https://github.com/php/php-src.git
cd php-src

# Build from source
./buildconf
./configure --disable-all --enable-debug
make -j$(nproc)

# Run tests
make test
# Run specific test
make test TESTS="tests/lang/001.phpt"

# Format of a .phpt test:
/*
--TEST--
Basic test
--FILE--
<?php
echo "Hello World";
?>
--EXPECT--
Hello World
*/
```

## Key Takeaway

Understanding PHP internals allows you to:
1. Write more performant code
2. Debug subtle memory/performance issues
3. Create extensions for specialized needs
4. Contribute to PHP core
5. Understand why PHP behaves the way it does
