# PHP Complete Reference — Every Topic in PHP

A comprehensive, structured map of absolutely all PHP content: from first syntax to elite enterprise engineering. Based on official PHP docs, PHP-FIG standards, and 2026 ecosystem research.

---

## PART 1: PHP FUNDAMENTALS

### 1.1 What Is PHP
- Server-side scripting language (runs on a server, generates HTML sent to browser)
- Created 1994 by Rasmus Lerdorf; evolved through PHP 3, 4, 5, 7, 8
- Powers ~77% of all websites with known server-side language (WordPress, Wikipedia, Facebook's legacy)
- Interpreted, dynamically typed, runs on Zend Engine
- Current versions: 8.4 (stable), 8.5 (latest, released Nov 2025), PHP 9 in planning

### 1.2 Environment Setup
- **Local stacks**: XAMPP, WAMP, Laragon (Windows); MAMP, Laravel Herd (macOS); native packages (Fedora/Ubuntu)
- **Professional**: Docker containers, PHP-FPM + Nginx
- **CLI check**: `php -v`, `php -m` (extensions), `php --ini` (config files)
- Key INI files: `php.ini` (development/production variants)
- Essential extensions: `pdo_mysql`, `openssl`, `mbstring`, `intl`, `opcache`, `curl`, `json`, `gd`
- Editors: VS Code + Intelephense, PhpStorm, PHP Insights

### 1.3 Syntax & Structure
- PHP tags: `<?php ?>` (required), short echo `<?= ?>` (always available for output)
- Closing tag `?>` optional (omit in pure PHP files, PSR-1)
- Statements end with semicolon `;`
- Comments: `//`, `#`, `/* */`
- Case sensitivity: variables are case-sensitive; functions/classes are NOT (but be consistent)
- Reserved keywords cannot be used as identifiers
- Output: `echo`, `print`, `printf()`, `var_dump()`, `print_r()`, `die()/exit()`

### 1.4 Variables
- Declared with `$` prefix: `$name = "Alice";`
- Naming: must start with letter/underscore; camelCase recommended; no hyphens/spaces
- **Scope**: local (function), global (needs `global` keyword or `$GLOBALS`), static (persists), superglobal
- **Variable variables**: `$$name`
- **References**: `$b = &$a;` (both point to same data)
- Type: dynamically typed (no declaration needed)

### 1.5 Data Types (8 types)
**Scalar** (one value):
- `bool` — true/false
- `int` — whole numbers (64-bit)
- `float` (double) — decimals
- `string` — text (single/double quotes, heredoc, nowdoc)

**Compound** (multiple values):
- `array` — ordered map, indexed/associative/multidimensional
- `object` — instance of a class
- `callable` — functions/closure/method
- `iterable` — array or Traversable

**Special**:
- `null`
- `resource` — external resource (file handle, DB connection)

**Type checks**: `is_int()`, `is_string()`, `is_float()`, `is_bool()`, `is_array()`, `is_object()`, `is_null()`, `is_numeric()`, `is_scalar()`, `gettype()`

### 1.6 Type Juggling & Casting
- **Juggling** (automatic): `"5" + 3` → `8`; `"Hello " . 5` → string
- Boolean truthiness: empty string, `"0"`, `0`, `[]`, `null` are FALSE
- **Explicit casting**: `(int)`, `(float)`, `(string)`, `(bool)`, `(array)`, `(object)`
- Functions: `intval()`, `floatval()`, `strval()`, `boolval()`, `settype()`
- **Strict mode**: `declare(strict_types=1);` at top of file — enables strict type checking
- Type declarations: `function add(int $a, int $b): int`
- Union types: `int|string`, nullable `?Type`, `mixed`, `never`, `true`/`false` (8.2)
- Loose vs strict comparison: `==` (loose, `1 == "1"` true) vs `===` (strict, also type)

### 1.7 Constants
- `define('NAME', value)` — runtime, can be arrays (7+)
- `const NAME = value` — compile-time, class scope
- Class constants: `const`, accessed `ClassName::CONST`
- **Magic constants**: `__LINE__`, `__FILE__`, `__DIR__`, `__FUNCTION__`, `__CLASS__`, `__METHOD__`, `__NAMESPACE__`, `__TRAIT__`, `__PROPERTY__` (8.4)
- Constants are case-sensitive by default, UPPER_SNAKE_CASE convention

### 1.8 Operators
- **Arithmetic**: `+ - * / % **`
- **Assignment**: `= += -= *= /= %= .=`
- **Comparison**: `== === != !== < > <= >= <=> ` (spaceship), `??` (null coalescing), `? :` (ternary), `??=` (coalescing assignment)
- **Logical**: `&& || ! and or xor`
- **String**: `.` (concat), `.=`
- **Increment/Decrement**: `++ --`
- **Bitwise**: `& | ^ ~ << >>`
- **Error control**: `@` (discouraged)
- **Pipe operator** `|>` (PHP 8.5): `$value |> fn1 |> fn2`
- **Array operators**: `+` (union), `==`, `===`, `!=`, `!==`
- **Instanceof**: `$obj instanceof ClassName`

### 1.9 Control Flow
- `if`, `else`, `elseif`
- `switch` / `case` / `default` / `break` (8.5: end cases with `:` not `;`)
- `match` (PHP 8.0) — strict, returns value, no break needed
- `ternary` and `null coalescing`
- **Alternative syntax** for templates: `if: ... endif;`, `foreach: ... endforeach;` etc.

### 1.10 Loops
- `for` (counter), `while` (condition-first), `do-while` (runs once), `foreach` (arrays/objects)
- Loop control: `break` (with optional level `break 2`), `continue` (with level)
- `foreach ($arr as $value)`, `foreach ($arr as $key => $value)`, by reference `&$value` (unset after!)
- `array_walk`, `array_map`, `array_filter` for functional iteration

### 1.11 Functions
- Declaration: `function name(params): returnType {}`
- Params: defaults, type hints, nullable, variadic `...$args`, named arguments (8.0)
- Return: value, `return`, by reference `&`, type declarations, `void`, `never` (8.1)
- **Scope**: local by default, `global`, `static`
- **Anonymous functions / closures**: `function ($x) use ($var) { return $x; }`
- **Arrow functions**: `fn($x) => $x * 2` (implicit `use`, single expression)
- **First-class callables**: `$fn = strlen(...);` (8.1)
- **Callbacks**: passed to array functions, `call_user_func()`, `call_user_func_array()`
- **Variable functions**: `$funcName();`
- **Recursion**: function calls itself
- **Strict types**, union returns, `Closure::getCurrent()` (8.5)

### 1.12 Arrays (most used data structure)
- Creation: `[]` literal, `array()`
- Types: **indexed** `[0,1,2]`, **associative** `['name' => 'Alice']`, **multidimensional**
- Access: `$arr[0]`, `$arr['key']`, nested `$arr['a']['b']`
- Add: `$arr[] = $val;`, `array_push()`, `array_unshift()`
- Remove: `array_pop()`, `array_shift()`, `unset($arr['key'])`
- Count: `count()`, `sizeof()`
- Key functions: `array_keys()`, `array_values()`, `array_key_exists()`, `in_array()`, `array_search()`
- Merge: `array_merge()`, `+` union, `array_combine()`
- Transform: `array_map()`, `array_filter()`, `array_reduce()`, `array_walk()`
- Sort: `sort()`, `rsort()`, `asort()`, `ksort()`, `usort()` (custom), `array_multisort()`
- Slice/splice: `array_slice()`, `array_splice()`, `array_chunk()`, `array_reverse()`
- Flatten/group: `array_column()`, `array_flip()`, `array_unique()`, `array_intersect()`, `array_diff()`
- Fill: `array_fill()`, `array_fill_keys()`, `range()`
- Math on arrays: `array_sum()`, `array_product()`
- **PHP 8.4**: `array_find()`, `array_find_key()`, `array_any()`, `array_all()`
- **PHP 8.5**: `array_first()`, `array_last()`
- Destructuring: `list()`, `[$a, $b] = $arr;`
- `foreach` + `&$value` pitfall, unpacking `...` spread operator

### 1.13 Strings
- Quotes: single (no interpolation), double (interpolation), heredoc (`<<<EOT`), nowdoc (`<<<'EOT'`)
- Interpolation: `"Value: $var"`, `"{$obj->prop}"`
- Escaping: `\n \t \\ \" \$`
- Length: `strlen()` (bytes) vs `mb_strlen()` (chars, multibyte)
- Search: `strpos()`, `strrpos()`, `str_contains()` (8.0), `str_starts_with()`, `str_ends_with()` (8.0)
- Extract: `substr()`, `mb_substr()`, `explode()`, `str_split()`
- Replace: `str_replace()`, `str_ireplace()`, `substr_replace()`, `strtr()`
- Case: `strtoupper()`, `strtolower()`, `ucfirst()`, `ucwords()`, `lcfirst()`
- Trim: `trim()`, `ltrim()`, `rtrim()`
- Format: `sprintf()`, `vsprintf()`, `number_format()`, `implode()/join()`, `str_pad()`, `str_repeat()`
- Pattern: `preg_match()`, `preg_match_all()`, `preg_replace()`, `preg_split()`, `preg_quote()`
- Multibyte: `mb_strlen()`, `mb_substr()`, `mb_convert_encoding()`, `mb_detect_encoding()`
- HTML: `htmlspecialchars()`, `htmlentities()`, `strip_tags()`
- Hash: `md5()`, `sha1()`, `hash()`, `hash_hmac()`, `crc32()`
- URL: `urlencode()`, `urldecode()`, `base64_encode()`, `base64_decode()`
- **JSON**: `json_encode()`, `json_decode()` (with `true` for assoc array), `json_validate()` (8.3), errors via `json_last_error()`

### 1.14 Numbers & Math
- Integers (signed 64-bit), floats (double precision), overflow to float
- Rounding: `round()`, `floor()`, `ceil()`, `intdiv()`, `fmod()`
- Random: `rand()`, `mt_rand()`, `random_int()` (crypto-secure, preferred), `random_bytes()` (8.5 adds deprecation for seeded variants)
- Absolute/value: `abs()`, `min()`, `max()`
- Power/log: `pow()`, `sqrt()`, `exp()`, `log()`, `log10()`
- Trig: `sin()`, `cos()`, `tan()`, `pi()`, `deg2rad()`, `rad2deg()`
- Format: `number_format()`, `floatval()`, `intval()`, `(int)` with overflow warnings (8.5)
- **BCMath** (arbitrary precision): `bcadd()`, `bcsub()`, `bcmul()`, `bcdiv()`, `bccomp()`
- Infinity: `INF`, `is_finite()`, `is_infinite()`, `is_nan()`

### 1.15 Date & Time
- `date(format)`, `time()`, `strtotime()`, `mktime()`
- **DateTime class**: `new DateTime()`, `new DateTimeImmutable()`, `format()`, `modify()`, `add()/sub()` with `DateInterval`
- Timezones: `date_default_timezone_set()`, `DateTimeZone`, `timezone_identifiers_list()`
- `DateTimeImmutable` (recommended — immutable)
- Intervals: `DateInterval`, `DatePeriod` for ranges
- `strtotime('next monday')`, `date_diff()`, `date_create()`
- Format chars: `Y-m-d H:i:s`, `U` (unix), ISO 8601 `c`

### 1.16 Error Handling
- Error types: `E_ERROR`, `E_WARNING`, `E_NOTICE`, `E_DEPRECATED`, `E_PARSE`, `E_STRICT`
- Display vs log: `display_errors`, `log_errors`, `error_reporting()`
- **Exceptions**: `throw new Exception('msg')`, `try/catch/finally`, `catch (Exception $e)`
- `Throwable` interface (base for Error + Exception)
- Custom exceptions: `class MyException extends Exception {}`
- Multiple catches: `catch (TypeError | ValueError $e)`
- Error handlers: `set_error_handler()`, `set_exception_handler()`, `register_shutdown_function()`
- Getters: `get_error_handler()`, `get_exception_handler()` (8.5)
- Attributes: `#[SensitiveParameter]` hides params from backtraces
- Fatal error backtraces (8.5), `error_get_last()`
- Logging: `error_log()`, Monolog library
- Best practice: catch at boundaries, log, respond gracefully; never expose details in production

### 1.17 File Operations
- Read: `file_get_contents()`, `file()` (lines array), `fopen()` + `fread()`
- Write: `file_put_contents()`, `fwrite()`, `fputs()`
- Pointers: `fopen($path, $mode)`, modes `r w a r+ w+ x c`, `fgetc()`, `fgets()`, `feof()`
- Directories: `mkdir()`, `rmdir()`, `scandir()`, `glob()`, `opendir()/readdir()`
- Info: `file_exists()`, `is_file()`, `is_dir()`, `filesize()`, `filemtime()`, `pathinfo()`, `basename()`, `dirname()`
- `realpath()`, `unlink()` (delete), `copy()`, `rename()`, `touch()`
- Permissions: `chmod()`, `chown()`, `fileperms()`
- Streaming large files, memory efficiency, locking `flock()`
- CSV: `fgetcsv()`, `fputcsv()`
- Uploads: `$_FILES`, `move_uploaded_file()`, `is_uploaded_file()`

### 1.18 Includes & Requires
- `include`, `require`, `include_once`, `require_once`
- Difference: include = warning on failure; require = fatal
- File organization, templates, partials
- Path resolution, `__DIR__`, `dirname(__DIR__)`
- `include` returns value; `include` in functions (scoping)
- Prefer `require_once` for classes; autoloading replaces manual includes (later)

### 1.19 Superglobals
- `$_GET` — URL query params
- `$_POST` — form body (application/x-www-form-urlencoded, multipart)
- `$_REQUEST` — GET+POST combined (avoid; ambiguous)
- `$_SERVER` — server info: `HTTP_HOST`, `REQUEST_URI`, `REQUEST_METHOD`, `REMOTE_ADDR`, `HTTP_USER_AGENT`, `PHP_SELF`, `SCRIPT_NAME`
- `$_FILES` — uploads: `name`, `type`, `tmp_name`, `error`, `size`
- `$_SESSION` — session data
- `$_COOKIE` — cookies
- `$_ENV` — environment variables
- `$GLOBALS` — all globals
- `$_SESSION`, `$_COOKIE` mutable; others read-only
- **Never trust superglobals** — validate/sanitize everything

### 1.20 Forms & User Input
- HTML forms: `method="get|post"`, `action`, `enctype="multipart/form-data"` for files
- Retrieval: `$_GET`, `$_POST`, `$_FILES`
- Validation: required fields, format (email, URL, phone), length, whitelist
- `filter_var()`, `filter_input()`, filters: `FILTER_VALIDATE_EMAIL`, `FILTER_SANITIZE_STRING`, `FILTER_VALIDATE_INT`
- Sanitization vs validation — validate on server, never trust client
- CSRF tokens on all forms
- Re-display submitted values, error messages, `htmlspecialchars()` output

### 1.21 Sessions & Cookies
- **Cookies**: `setcookie()`, `setrawcookie()`, `$_COOKIE`, `expires`, `path`, `domain`, `secure`, `httponly`, `samesite`, `partitioned` (8.5)
- **Sessions**: `session_start()`, `$_SESSION`, `session_id()`, `session_regenerate_id(true)`
- Session config: `session.gc_maxlifetime`, `session.cookie_httponly`, `session.cookie_secure`, `session.cookie_samesite`, `session.use_strict_mode`
- Handlers: files (default), Redis, database, Memcached
- Store server-side (sessions) vs client-side (cookies); sessions safer
- Security: regenerate ID on login, HttpOnly, SameSite, HTTPS-only
- Flash messages pattern (store + clear after display)

### 1.22 Regular Expressions (PCRE)
- `preg_match()`, `preg_match_all()`, `preg_replace()`, `preg_split()`, `preg_grep()`
- Delimiters `~ ... ~`, modifiers `i m s u x`
- Common patterns: `^[a-z0-9_-]+$`, email, URL, phone
- Quantifiers `* + ? {n,m}`, anchors `^ $`, classes `[a-z]`, groups `()` capturing/non-capturing `(?:)`
- Lookahead `(?=...)`, lookbehind `(?<=...)`
- Named groups `(?P<name>)`
- Performance: backtracking, `preg_last_error()`

---

## PART 2: OBJECT-ORIENTED PHP

### 2.1 Classes & Objects
- `class`, `new`, `->`, `$this`, `::` (static/const access)
- Properties with type declarations + defaults
- Methods, `public`/`protected`/`private` visibility
- `__construct()`, `__destruct()`, `__clone()`
- **Constructor property promotion** (8.0): `public function __construct(private int $id) {}`
- `readonly` properties/classes (8.1/8.2)
- **Property hooks** (8.4): `public string $name { get => ...; set => ...; }`
- **Asymmetric visibility** (8.4): `public private(set) string $name`
- `static` properties/methods, `self`, `static`, `parent`
- Object cloning: `clone`, `clone $obj` with `__clone()`, **`clone()` function (8.5)** with property array
- Object comparison: `==` (same props/class) vs `===` (same instance)

### 2.2 Inheritance & Polymorphism
- `extends` (single inheritance), method override
- `parent::method()`, `parent::__construct()`
- `final` keyword (class/method), `final` on promoted properties (8.5)
- Abstract classes: `abstract class`, `abstract method`
- `$this` late static binding, `static::`

### 2.3 Interfaces & Abstract Classes & Traits
- `interface`: contract of method signatures; multiple interfaces allowed
- Interface constants (8.1+), type hints for interfaces
- `abstract class`: partial implementation, single inheritance
- **Trait**: horizontal code reuse — `use TraitName;`
- Multiple traits, conflict resolution: `insteadof`, `as`
- Trait precedence: class > trait > parent
- Traits can have: methods, properties, static members, abstract methods, constants (8.2)
- `#[Deprecated]` on traits (8.5)

### 2.4 Magic Methods
- `__construct`, `__destruct`, `__call`, `__callStatic`
- `__get`, `__set`, `__isset`, `__unset`
- `__toString`, `__invoke`, `__clone`, `__sleep`/`__wakeup` (soft-deprecated 8.5 → use `__serialize`/`__unserialize`)
- `__serialize`, `__unserialize` (recommended)
- `__set_state`, `__debugInfo`
- Overloading via magic methods (dynamic properties discouraged, deprecated 8.2)

### 2.5 Namespaces & Autoloading
- `namespace Vendor\Sub;`, `use`, `as` alias, fully qualified `\Namespace\Class`
- Group use statements `use Foo\{Bar, Baz};`
- Global fallback, `__NAMESPACE__`
- **Autoloading**: `spl_autoload_register()`, PSR-4 via Composer, `require 'vendor/autoload.php'`
- One class per file, namespace mirrors directory

### 2.6 Advanced OOP
- Anonymous classes: `new class { ... }`
- Closures binding: `Closure::bind()`, `Closure::bindTo()`, `Closure::call()`, `Closure::fromCallable()`
- **Generators**: `yield`, `yield from`, `Generator::current()`, memory-efficient iteration
- **Iterators**: `Iterator` interface, `IteratorAggregate`, `Traversable`, `ArrayIterator`, SPL iterators
- **Serialization**: `serialize()`, `unserialize()` (dangerous on untrusted input), `__serialize`
- Weak references: `WeakReference`, `WeakMap`
- **Attributes** (8.0): `#[Attribute]`, custom attributes, `ReflectionAttribute`
- Built-in attributes: `#[ReturnTypeWillChange]`, `#[Deprecated]` (8.3), `#[Override]` (8.3), `#[SensitiveParameter]`, `#[NoDiscard]` (8.5), `#[DelayedTargetValidation]` (8.5)

### 2.7 Enums (PHP 8.1)
- Pure: `enum Status { case Draft; case Published; }`
- Backed: `enum Status: string { case Published = 'published'; }`
- Methods, constants, static methods, interfaces
- `match($status)`, `->value`, `Status::tryFrom()`, `Status::cases()`
- Serialization support

### 2.8 Reflection
- `ReflectionClass`, `ReflectionMethod`, `ReflectionProperty`, `ReflectionParameter`, `ReflectionAttribute`
- `ReflectionFunction`, `ReflectionEnum` (8.1)
- Used by DI containers, frameworks, test tools, static analyzers

---

## PART 3: MODERN PHP 8.x FEATURES (by version)

### PHP 8.0 (Nov 2020)
- JIT compiler, union types, named arguments, match expression, nullsafe `?->`, constructor promotion, attributes, `mixed` type, `static` return type, `Stringable` interface, `str_contains/starts_with/ends_with`, `get_debug_type()`, `throw` expression

### PHP 8.1 (Nov 2021)
- Fibers, enums, readonly properties, `never` return type, first-class callables, array unpacking with string keys, `final` class constants, intersection types (`A&B`), `new` in initializers, `Deprecated` attribute

### PHP 8.2 (Nov 2022)
- Readonly classes, DNF types, `true`/`false` standalone types, constants in traits, `#[AllowDynamicProperties]`, `json_validate()`, `Random\Randomizer`, `mysqli_execute_query()`, deprecated dynamic properties

### PHP 8.3 (Nov 2023)
- Typed class constants, `#[\Override]`, dynamic class constant fetch, `readonly` amendments, `json_validate()`, `mb_str_pad()`, `Randomizer::getBytesFromString()`, `#[\Deprecated]` attribute, CLI `--rc` flag

### PHP 8.4 (Nov 2024)
- **Property hooks**, **asymmetric visibility**, HTML5-compliant DOM parser (`Dom\HTMLDocument`), **lazy objects**, new array functions (`array_find`, `array_any`, `array_all`), `mb_ucfirst()`, `#[\Deprecated]` on functions, exit as function, `request_parse_body()`, `new` without parentheses, PDO driver-specific subclasses

### PHP 8.5 (Nov 2025)
- **Pipe operator** `|>` — chain functions left-to-right
- **URI extension** — `Uri\Rfc3986\Uri`, `Uri\WhatWg\Url` (RFC 3986 + WHATWG standards)
- **`clone()` with properties** — clone-with / "with-er" pattern for readonly
- `array_first()`, `array_last()`
- `#[\NoDiscard]` — warns on unused return value
- Closures & first-class callables in constant expressions
- `#[\Override]` on properties, `#[\Deprecated]` on traits/constants, `#[\DelayedTargetValidation]`
- `final` constructor property promotion
- Static asymmetric visibility
- Fatal error backtraces
- `Closure::getCurrent()`, `get_error_handler()`, `get_exception_handler()`
- OPcache now always compiled in
- CHIPS partitioned cookies
- **Deprecations**: backtick operator, non-canonical casts `(integer)`/`(boolean)`, `__sleep`/`__wakeup`, `curl_close()`, resource close functions, `case` ending with `;`

### PHP 9 (planned)
- Removal of deprecated features, further cleanup

---

## PART 4: DATABASES

### 4.1 Database Fundamentals
- Relational DBs: MySQL, MariaDB, PostgreSQL, SQLite, SQL Server, Oracle
- Tables, rows, columns, primary keys, foreign keys, indexes, unique constraints
- SQL: `SELECT`, `INSERT`, `UPDATE`, `DELETE`, `JOIN` (INNER/LEFT/RIGHT/FULL), `WHERE`, `ORDER BY`, `GROUP BY`, `HAVING`, `LIMIT`, `OFFSET`, subqueries, `UNION`
- Normalization (1NF→3NF, Boyce-Codd), denormalization trade-offs
- Transactions (ACID), `COMMIT`, `ROLLBACK`
- Indexing strategies, `EXPLAIN`, query optimization
- NoSQL vs SQL (Redis, MongoDB)

### 4.2 PDO (PHP Data Objects) — RECOMMENDED
- Abstraction layer over multiple databases
- Connection: DSN `mysql:host=...;dbname=...;charset=utf8mb4`
- Options: `ATTR_ERRMODE => ERRMODE_EXCEPTION`, `ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC`, `ATTR_EMULATE_PREPARES => false`
- **Prepared statements** (SQL injection defense):
  ```php
  $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
  $stmt->execute(['email' => $email]);
  $user = $stmt->fetch();
  ```
- Positional `?` vs named `:name` placeholders
- `bindParam()` (by reference) vs `bindValue()` vs `execute(array)`
- Fetch modes: `FETCH_ASSOC`, `FETCH_NUM`, `FETCH_OBJ`, `FETCH_BOTH`, `FETCH_COLUMN`, `FETCH_KEY_PAIR`, `FETCH_CLASS`, `FETCH_GROUP`
- `fetch()`, `fetchAll()`, `fetchColumn()`, `rowCount()`, `lastInsertId()`
- **Transactions**: `beginTransaction()`, `commit()`, `rollBack()`
- Error handling: `PDOException`, `errorInfo()`, `errorCode()`
- Multiple execution (prepare once, execute many)
- LIKE/IN/LIMIT with prepared statements (LIMIT needs int cast)
- PDO subclasses per driver (8.4): `Pdo\Mysql`, `Pdo\Pgsql`, `Pdo\Sqlite`

### 4.3 MySQLi
- Procedural & object styles, `mysqli_connect()`
- Prepared statements with `bind_param('ssi', ...)` type strings
- Legacy `mysql_*` functions removed in PHP 7

### 4.4 ORMs & Query Builders
- Doctrine ORM (Symfony, data mapper)
- Eloquent (Laravel, active record)
- Raw vs query builder vs ORM — pick per complexity
- Migrations for schema versioning

---

## PART 5: WEB DEVELOPMENT

### 5.1 HTTP Fundamentals
- Request/response cycle, methods `GET POST PUT PATCH DELETE`
- Status codes: 200, 201, 204, 301/302/307/308, 400, 401, 403, 404, 409, 422, 429, 500, 503
- Headers: `Content-Type`, `Authorization`, `Accept`, `Cache-Control`, security headers
- `http_response_code()`, `header()`, `$_SERVER['REQUEST_METHOD']`
- State: cookies + sessions (HTTP is stateless)

### 5.2 Templating
- PHP itself as template (`<?= $var ?>`)
- Alternative syntax control structures
- Engines: Blade (Laravel), Twig (Symfony), Latte, Mustache
- Layouts/partials/components, escaping output, XSS safety

### 5.3 REST APIs
- Design: resources, HTTP verbs, status codes, nested resources, filtering/pagination
- JSON in/out: `json_encode`, `json_decode`, `Content-Type: application/json`
- Input: `file_get_contents('php://input')` for raw body
- Auth: token, API key, JWT, OAuth2, Sanctum/Passport (Laravel), API Platform (Symfony)
- Versioning, rate limiting, OpenAPI/Swagger docs
- Errors: structured error responses

### 5.4 GraphQL
- Schema-first, queries/mutations, resolution
- Libraries: Lighthouse (Laravel), GraphQLite, webonyx/graphql-php

### 5.5 Real-Time / WebSockets
- WebSocket protocol, SSE (Server-Sent Events)
- PHP: Swoole, ReactPHP, Amp, Workerman
- Laravel Broadcasting + Echo + Reverb, Symfony Mercure
- Laravel Livewire / Inertia.js for full-stack interactivity

### 5.6 CLI Applications
- `php script.php`, `$argv`, `$argc`, `STDIN/STDOUT/STDERR`
- Console commands: Symfony Console, Laravel Artisan
- Cron jobs, schedulers, daemons, `pcntl` (process control)

### 5.7 Email
- `mail()`, PHPMailer, Symfony Mailer, Laravel Mail
- SMTP, templates, attachments, queues for sending

### 5.8 File Uploads
- Multipart forms, `$_FILES` structure, `move_uploaded_file()`
- Validation: extension, MIME, size, image verification (`getimagesize`)
- Store outside webroot, random filenames, never execute uploads

### 5.9 Internationalization
- `setlocale()`, `gettext`/`_()`, translations
- `intl` extension, `NumberFormatter`, `IntlDateFormatter`, `IntlListFormatter` (8.5)
- `Locale` class, `Locale::getPrimaryLanguage()`, RTL checks (8.5)
- `mb_*` multibyte functions, UTF-8 everywhere

---

## PART 6: SECURITY (OWASP-aligned)

### 6.1 OWASP Top 10
1. **Broken Access Control** (IDOR) → server-side ownership checks, policies
2. **Cryptographic Failures** → `password_hash()`, AES-256-GCM, HTTPS/HSTS
3. **Injection** (SQL/command/LDAP) → prepared statements, no `eval`/`exec` on user input
4. **Insecure Design** → threat modeling, rate limits
5. **Security Misconfiguration** → hardened `php.ini`, no error display
6. **Vulnerable Components** → `composer audit`, keep updated
7. **Auth Failures** → session_regenerate_id, MFA
8. **Integrity Failures** → no `unserialize()` on user input, HMAC signing
9. **Logging Failures** → structured logs, alerting
10. **SSRF** → URL allowlists, block private IPs

### 6.2 SQL Injection
- Cause: interpolating user input into SQL
- Fix: PDO prepared statements (100% defense) — never `addslashes()`, never concatenation
- Also: validate types, least-privilege DB users

### 6.3 XSS (Cross-Site Scripting)
- Cause: unescaped output
- Fix: `htmlspecialchars($data, ENT_QUOTES, 'UTF-8')` in HTML context; context-aware escaping for JS/URL/attr
- Defense in depth: Content-Security-Policy header, template auto-escaping (Twig/Blade)

### 6.4 CSRF (Cross-Site Request Forgery)
- Cause: no token on state-changing requests
- Fix: synchronizer token in session + form, `hash_equals()` (timing-safe), `SameSite` cookies; APIs use custom headers

### 6.5 Password Handling
- `password_hash($pw, PASSWORD_BCRYPT, ['cost' => 12])` or `PASSWORD_ARGON2ID`
- `password_verify()`, `password_needs_rehash()` for upgrades
- Never MD5/SHA1/SHA256; never plaintext
- Timing-safe comparison, generic error messages ("invalid credentials")

### 6.6 Session & Cookie Security
- `session_regenerate_id(true)` after login/privilege change
- Cookie flags: `HttpOnly`, `Secure`, `SameSite=Strict|Lax`, `partitioned`
- Session timeouts, idle limits, secure storage (Redis)
- Session fixation/hijacking prevention

### 6.7 File Upload Security
- Validate MIME server-side (not extension), size limits, scan
- Store outside webroot, random names, disable PHP execution in upload dir

### 6.8 Security Headers
```
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
Content-Security-Policy: default-src 'self'
Strict-Transport-Security: max-age=31536000
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=()
```

### 6.9 Hardened php.ini (production)
- `display_errors=Off`, `log_errors=On`
- `expose_php=Off`, `session.cookie_httponly=1`
- Disable dangerous functions, restrict file access, `open_basedir`
- Secrets in env vars / secret managers, never in code or `.env` committed to git
- Input validation at boundaries (allowlists), output encoding, dependency scanning (`composer audit`, Snyk)

---

## PART 7: ECOSYSTEM & TOOLING

### 7.1 Composer (dependency manager)
- `composer init`, `require`, `install`, `update`, `remove`
- `composer.json` / `composer.lock` — commit both; never commit `vendor/`
- SemVer constraints: `^1.0`, `~1.0`, `1.*`
- Repositories: Packagist, private VCS
- **PSR-4 autoloading**:
  ```json
  { "autoload": { "psr-4": { "App\\": "src/" } } }
  ```
  Namespace prefix → directory; `App\Models\User` → `src/Models/User.php`
- `composer dump-autoload` (`-o`, `--classmap-authoritative` for prod)
- `composer audit` for vulnerabilities
- Scripts, plugins, global installs

### 7.2 PSR Standards (PHP-FIG)
- **PSR-0** — deprecated autoloading (use PSR-4)
- **PSR-1** — basic coding standard (tags, UTF-8, side effects)
- **PSR-3** — logger interface
- **PSR-4** — autoloading standard
- **PSR-6** — caching interface
- **PSR-7** — HTTP message interface (Request/Response)
- **PSR-11** — container interface (DI)
- **PSR-12** — extended coding style (4 spaces, LF, 120-char soft limit)
- **PSR-13** — hypermedia links
- **PSR-14** — event dispatcher
- **PSR-15** — HTTP handlers/middleware
- **PSR-16** — simple cache
- **PSR-17** — HTTP factories
- **PSR-18** — HTTP client
- **PSR-20** — clock

### 7.3 Code Quality Tools
- **PHPStan** — static analysis (levels 0-9, catches type errors)
- **Psalm** — static analysis
- **Larastan** — Laravel-aware PHPStan
- **PHP-CS-Fixer** — style fixing (PSR-12)
- **PHP_CodeSniffer** — `phpcs`/`phpcbf`
- **Rector** — automated refactoring/upgrades
- **Pint** — Laravel's code style tool
- **Xdebug** — debugging, profiling, coverage
- **Composer audit** / Snyk — dependency security

### 7.4 Debugging
- `var_dump()`, `print_r()`, `dd()` (Laravel), `dump()`/`die()`
- Xdebug step debugging (IDE breakpoints), `xdebug.mode=debug`
- Symfony Web Profiler / Laravel Telescope / Laravel Debugbar
- Log files, structured logging with Monolog
- Error tracking: Sentry, Bugsnag, Flare
- `error_reporting(E_ALL)`, `display_errors=1` in dev only

---

## PART 8: FRAMEWORKS

### 8.1 Laravel (most popular, fast development)
- Artisan CLI, Blade templating, Eloquent ORM (Active Record)
- Migrations, seeders, factories, `php artisan make:model`
- Routing, controllers, middleware, policies, Form Requests
- Auth: Breeze, Jetstream, Fortify; API: Sanctum (tokens), Passport (OAuth2)
- Queues + Horizon, caching, events/listeners, notifications
- Livewire, Inertia.js, Reverb (WebSockets), Octane (perf)
- Ecosystem: Forge (servers), Vapor (serverless), Nova (admin), Cashier (billing), Telescope (debug), Envoyer (deploy)
- Testing: PHPUnit + Pest built in

### 8.2 Symfony (enterprise, components)
- Reusable components (used by Laravel internally, Drupal, Magento)
- Doctrine ORM (Data Mapper), Twig templates (sandboxed)
- Dependency injection / service container, bundles
- Security component (firewalls, voters, authenticators)
- Messenger (queues), Mercure (realtime), API Platform
- `#[Route]` attributes, Flex recipes, LTS versions (3-4 years support)
- Web Profiler, MakerBundle, Console

### 8.3 Other Frameworks
- **Slim** — micro-framework for APIs
- **Laminas** (Zend) — enterprise
- **CodeIgniter 4** — lightweight
- **CakePHP** — convention-heavy
- **Yii 2** — fast performance
- **Phalcon** — C-extension framework
- **Flight**, **Lumen** (micro, now Laravel), **Spiral** (roadrunner-native)

### 8.4 CMS Platforms
- **WordPress** (40%+ of web) — themes, plugins, hooks
- **Drupal**, **Joomla**, **Magento** (e-commerce), **WooCommerce**

---

## PART 9: TESTING

### 9.1 Testing Levels
- **Unit** — isolate single functions/classes, no I/O
- **Feature/Integration** — HTTP request through app, real database
- **End-to-End** — browser flows (Laravel Dusk, Playwright, Symfony Panther)
- **API tests** — contracts and responses
- Pyramid vs modern "trophy" shape — most value in integration/feature tests

### 9.2 PHPUnit
- Install: `composer require --dev phpunit/phpunit`
- `phpunit.xml` config, `TestCase` base class, `setUp()/tearDown()`
- Test methods: `test_*` prefix or `#[Test]` attribute
- Assertions: `assertEquals`, `assertSame`, `assertTrue`, `assertContains`, `assertInstanceOf`, `assertDatabaseHas` (Laravel)
- **Data providers**: `#[DataProvider]`, multiple inputs
- **Doubles**: `createStub()` (return values) vs `createMock()` (expectations)
- Mocking static/final → wrapper interfaces, Mockery
- Attributes over docblocks (PHPUnit 11+), `#[Group]`

### 9.3 Pest (runs on PHPUnit)
- `test('name', fn () => ...)` / `it()`
- Chainable `expect($x)->toBe(5)->toBeInt()`
- Datasets `->with([...])`, hooks `beforeEach`
- **Architecture tests**: `arch()->expect('App\Models')->not->toUse(...)`
- Mutation testing (`pest --mutate`), `--parallel`, `--profile`

### 9.4 TDD & Best Practices
- Red-Green-Refactor
- Test behavior, not implementation
- Database strategies: `RefreshDatabase`, `DatabaseTransactions`, `LazilyRefreshDatabase`
- Isolate external services (mock HTTP, mail, queues)
- Coverage: Xdebug/PCOV, targets (logic 90%, controllers 80%)
- CI: GitHub Actions runs `composer install` + `pest/phpunit` on every push
- ParaTest / `--parallel` for speed

---

## PART 10: PERFORMANCE & CONCURRENCY

### 10.1 OPcache (bytecode cache)
- Compiles PHP once, reuses in shared memory
- Always included since PHP 8.5
- Tuning: `memory_consumption=512`, `max_accelerated_files=10000`, `validate_timestamps=0` (prod)
- **Preloading**: `opcache.preload` — pre-compile framework files → huge cold-start wins
- ~10% throughput gain from proper config

### 10.2 JIT (Just-In-Time Compilation)
- `opcache.jit=tracing`, `jit_buffer_size=128M`
- Only helps CPU-bound workloads (+60-95%): image processing, heavy framework routing
- Hurts I/O-bound (DB/network waits): disable JIT
- Not compatible with FrankenPHP worker mode (ZTS)

### 10.3 Application Servers
| Server | Type | Best For |
|---|---|---|
| PHP-FPM | classic process manager | maximum compatibility (default) |
| RoadRunner | Go + persistent workers | high-traffic APIs, Laravel Octane (+111% RPS) |
| Swoole | PHP extension, coroutines | WebSockets, high-latency I/O, max bare-metal RPS |
| FrankenPHP | Caddy-embedded, single binary | containers, simplest deploy, worker mode 3-5x |

### 10.4 Concurrency (Fibers)
- **Fibers** (8.1): stackful coroutines — cooperative concurrency, NOT parallelism
- `Fiber::suspend()`, `$fiber->resume()`, schedulers
- Use for I/O-bound multiplexing (10,000+ concurrent HTTP calls)
- Libraries: Amp v3, ReactPHP (event loops, non-blocking DNS/HTTP/db)
- True parallelism: `parallel` extension or worker processes
- Async HTTP servers, WebSocket handlers, message queue consumers

### 10.5 Caching
- **Redis**: sessions, cache, queues, pub/sub, rate limiting
- **Memcached**: distributed cache
- APCu, opcache, HTTP caching (ETag, Cache-Control), browser caching
- Query caching (Doctrine second-level cache), Laravel cache drivers
- Cache invalidation patterns, TTLs, stampede protection

### 10.6 Queues & Background Jobs
- Problem: slow tasks (email, images, payments) block responses
- Laravel Queues + Horizon, Symfony Messenger, PHP background processes
- Drivers: database, Redis, SQS, Beanstalkd, RabbitMQ
- Workers, retries, dead-letter queues, idempotency, cron scheduling

### 10.7 Profiling & Monitoring
- Xdebug profiler, Blackfire, Tideways
- Query logging/EXPLAIN, slow query logs
- APM: Sentry, New Relic, Datadog
- Metrics, logs, traces (observability), health checks, alerting

---

## PART 11: ARCHITECTURE & DESIGN

### 11.1 SOLID Principles
- **S** — Single Responsibility (one reason to change per class)
- **O** — Open/Closed (open for extension, closed for modification)
- **L** — Liskov Substitution (subtypes substitutable for base)
- **I** — Interface Segregation (no fat interfaces)
- **D** — Dependency Inversion (depend on abstractions)
- Apply pragmatically (YAGNI, KISS) — don't over-engineer

### 11.2 Design Patterns
- **Creational**: Singleton (use sparingly), Factory, Abstract Factory, Builder, Prototype, Multiton, Pool
- **Structural**: Adapter, Bridge, Composite, Decorator, Facade, Flyweight, Proxy
- **Behavioral**: Strategy, Observer, Command, Chain of Responsibility, Iterator, Mediator, Memento, State, Template Method, Visitor, Interpreter
- **Web/enterprise**: MVC, Repository, Service Layer, DTO, Action classes, DI

### 11.3 Modern Pattern Shifts (2026)
- Property hooks → replace getters/setters
- Enums + `match` → replace Strategy pattern
- Constructor promotion + DI containers → replace Factories
- Pipe operator → replace nested calls/handler chains
- Three-Use Rule: don't abstract until 3 concrete cases exist

### 11.4 Dependency Injection & Containers
- Constructor injection (preferred), setter, interface
- **DI Container**: PSR-11 (`get()`, `has()`), auto-wiring via Reflection
- PHP-DI, Symfony DependencyInjection, Laravel Service Container
- Avoid Service Locator anti-pattern, avoid constructor bloat (>5-7 deps)

### 11.5 Architectural Styles
- **MVC/MVP/MVVM**, Layered architecture
- **Hexagonal** (ports & adapters), **Clean Architecture**
- **DDD** — Domain-Driven Design: entities, value objects, aggregates, repositories, domain events
- **CQRS** (command/query separation), Event Sourcing
- **Monolith** vs **Modular Monolith** vs **Microservices**
- **REST** vs **GraphQL** vs **RPC/gRPC**

### 11.6 Refactoring & Legacy PHP
- Spaghetti code → services/actions, extract interfaces
- Introduce tests before refactoring
- Upgrade paths: PHP 5→7→8, `php -l` (lint), Rector
- `declare(strict_types=1)`, modernize incrementally

---

## PART 12: DEPLOYMENT & DEVOPS

### 12.1 Local Development
- Docker Compose (php-fpm, nginx, mysql, redis)
- Laravel Sail, Herd, Homestead
- `.env` environment variables (Dotenv)

### 12.2 Server Configuration
- Nginx/Apache + PHP-FPM
- `pm.max_children`, pool tuning
- Caddy (auto-HTTPS), FrankenPHP single binary
- HTTPS/TLS, HTTP/2/3
- Shared hosting (cPanel) vs VPS vs PaaS

### 12.3 Containerization
- Dockerfiles (multi-stage: build → runtime)
- php extensions via `docker-php-ext-install`
- Compose orchestration, `--network host` + CPU pinning for performance
- Kubernetes (advanced), image scanning

### 12.4 CI/CD
- GitHub Actions, GitLab CI, Jenkins
- Pipeline: lint → static analysis → tests → build → deploy
- `composer install --no-dev --optimize-autoloader` in production
- Deploy strategies: FTP (legacy), rsync, rolling, blue-green, zero-downtime

### 12.5 Platforms
- Forge/Envoyer (Laravel), Vapor (serverless), Heroku, Railway, Fly.io, Render
- AWS (EC2, Lambda, ECS, RDS), DigitalOcean, Vercel (frontend + serverless)
- Environment secrets, log aggregation, backups

### 12.6 Monitoring & Operations
- Log aggregation (Sentry, Papertrail, ELK)
- Error tracking, uptime checks
- Health endpoints, scheduled tasks (cron), worker supervision
- Incident runbooks, rollback plans

---

## PART 13: CAREER & RESOURCES

### 13.1 Learning Roadmap (6-9 months to job-ready)
1. PHP basics + OOP (2-3 months) — Level 1-3 of your course
2. Databases (PDO/MySQL) + security (month 4)
3. Composer, PSR, testing (month 5)
4. Laravel + REST APIs (months 6-7)
5. Queues, Redis, Docker, deployment (month 8)
6. Advanced: architecture, DDD, AI integration, CI/CD (ongoing)

### 13.2 What Separates Junior vs Senior
- **Junior**: core PHP, basic Laravel, REST, Git
- **Senior**: service container, events, Horizon, Redis strategies, Docker, CI/CD, cloud, AI integrations, system architecture

### 13.3 Best Resources
- **Official**: [php.net manual](https://www.php.net/manual/en/) (complete reference)
- **Roadmap**: [roadmap.sh/php](https://roadmap.sh/php)
- **Structured path**: [phpfromzero.com](https://www.phpfromzero.com/) (free, ~600 lessons)
- **Best practices**: [phptherightway.com](https://phptherightway.com/)
- **PSR standards**: [php-fig.org/psr](https://www.php-fig.org/psr/)
- **Frameworks**: [laravel.com/docs](https://laravel.com/docs), [symfony.com/doc](https://symfony.com/doc)
- **Testing**: [pestphp.com](https://pestphp.com/docs), [docs.phpunit.de](https://docs.phpunit.de)
- **Databases**: [phpdelusions.net/pdo](https://phpdelusions.net/pdo), [phptutorial.net](https://www.phptutorial.net/php-pdo/)
- **Security**: [OWASP Top 10](https://owasp.org/www-project-top-ten/), [OWASP cheatsheets](https://cheatsheetseries.owasp.org/)
- **Version features**: [php.watch](https://php.watch/), [Stitcher.io](https://stitcher.io/)

### 13.4 Official Documentation Cheat-Sheet
- `php.net/manual` — every function documented with examples
- `php.watch` — version-by-version changes
- RFCs — wiki.php.net/rfc
- PHP.FIG — standards

---

*Note: This is the complete scope of PHP content. Your `php-mastery-course` (Level 0-7) covers this exact progression in depth with chapters, projects, and assessments. Use this document as your map; use the course chapters for the deep dives.*
