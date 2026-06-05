# PDO — PHP Data Objects Quick Reference

## Connection

```php
// MySQL
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=myapp;charset=utf8mb4';
$user = 'root';
$pass = 'secret';
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_STRINGIFY_FETCHES  => false,
];

$pdo = new PDO($dsn, $user, $pass, $options);

// SQLite
$pdo = new PDO('sqlite:/path/to/database.sqlite');

// PostgreSQL
$pdo = new PDO('pgsql:host=127.0.0.1;dbname=myapp');
```

## Important Connection Options

| Option | Value | Effect |
|--------|-------|--------|
| `ERRMODE_EXCEPTION` | `2` | Throws exceptions on errors — always use this |
| `DEFAULT_FETCH_MODE` | `FETCH_ASSOC` | Returns column names as keys |
| `EMULATE_PREPARES` | `false` | Uses real prepared statements |
| `STRINGIFY_FETCHES` | `false` | Keeps numeric types as int/float |

## Prepared Statements

### Named Parameters

```php
$stmt = $pdo->prepare(
    'SELECT * FROM users WHERE email = :email AND active = :active'
);
$stmt->execute([':email' => $email, ':active' => true]);
```

### Positional Parameters

```php
$stmt = $pdo->prepare(
    'SELECT * FROM users WHERE email = ? AND active = ?'
);
$stmt->execute([$email, true]);
```

## Fetching Data

```php
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$id]);

// Single row
$user = $stmt->fetch();               // Associative array
$user = $stmt->fetch(PDO::FETCH_OBJ); // Object
$user->name;

// All rows
$users = $stmt->fetchAll();           // Array of associative arrays

// Single column
$names = $pdo->query('SELECT name FROM users')
    ->fetchAll(PDO::FETCH_COLUMN);

// Key-value pairs
$pairs = $pdo->query('SELECT id, name FROM users')
    ->fetchAll(PDO::FETCH_KEY_PAIR);

// Count
$count = $stmt->fetchColumn();        // First column of first row

// Iterate (memory efficient for large results)
$stmt = $pdo->query('SELECT * FROM large_table');
while ($row = $stmt->fetch()) {
    processRow($row);
}
```

## Insert / Update / Delete

```php
// INSERT
$stmt = $pdo->prepare(
    'INSERT INTO users (name, email) VALUES (:name, :email)'
);
$stmt->execute([':name' => 'Alice', ':email' => 'alice@example.com']);
$newId = (int) $pdo->lastInsertId();

// UPDATE
$stmt = $pdo->prepare(
    'UPDATE users SET name = :name WHERE id = :id'
);
$stmt->execute([':name' => 'Bob', ':id' => 5]);
$affected = $stmt->rowCount();  // Number of rows changed

// DELETE
$stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
$stmt->execute([$id]);
```

## Transactions

```php
try {
    $pdo->beginTransaction();

    $pdo->exec('UPDATE accounts SET balance = balance - 100 WHERE id = 1');
    $pdo->exec('UPDATE accounts SET balance = balance + 100 WHERE id = 2');

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    throw $e;
}
```

## IN Clauses

```php
// Dynamic IN clause with positional params
$ids = [1, 2, 3, 5];
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare(
    "SELECT * FROM users WHERE id IN ($placeholders)"
);
$stmt->execute($ids);

// Named version
$params = [];
foreach ($ids as $i => $id) {
    $key = ":id$i";
    $params[$key] = $id;
    $keys[] = $key;
}
$stmt = $pdo->prepare(
    'SELECT * FROM users WHERE id IN (' . implode(',', $keys) . ')'
);
$stmt->execute($params);
```

## LIKE Queries

```php
$stmt = $pdo->prepare(
    'SELECT * FROM users WHERE name LIKE :name'
);
$stmt->execute([':name' => '%' . $search . '%']);

// Use LIKE with ESCAPE for user input containing % or _
$search = str_replace(['%', '_'], ['\\%', '\\_'], $search);
$stmt->execute([':name' => '%' . $search . '%']);
```

## NULL Handling

```php
// Use NULL in prepared statements
$stmt = $pdo->prepare(
    'SELECT * FROM users WHERE deleted_at IS NULL AND role = ?'
);
$stmt->execute([$role]);

// Set column to NULL
$stmt = $pdo->prepare(
    'UPDATE users SET deleted_at = :deleted_at WHERE id = :id'
);
$stmt->execute([':deleted_at' => null, ':id' => 5]);
```

## Error Handling

```php
try {
    $stmt = $pdo->prepare('INVALID SQL');
    $stmt->execute();
} catch (PDOException $e) {
    // Log the real error
    error_log($e->getMessage());

    // Show user-friendly message
    echo 'A database error occurred. Please try again.';

    // Optionally re-throw
    throw $e;
}
```

## Useful Helper Functions

```php
// Bulk insert
function bulkInsert(PDO $pdo, string $table, array $rows): void
{
    if (empty($rows)) return;

    $columns = implode(',', array_keys($rows[0]));
    $placeholders = '(' . implode(',', array_fill(0, count($rows[0]), '?')) . ')';
    $values = implode(',', array_fill(0, count($rows), $placeholders));

    $stmt = $pdo->prepare(
        "INSERT INTO $table ($columns) VALUES $values"
    );

    $flat = [];
    foreach ($rows as $row) {
        $flat = array_merge($flat, array_values($row));
    }

    $stmt->execute($flat);
}

// Upsert (MySQL-specific)
function upsert(PDO $pdo, string $table, array $data, string $uniqueKey): void
{
    $columns = implode(',', array_keys($data));
    $placeholders = ':' . implode(',:', array_keys($data));

    $updates = [];
    foreach (array_keys($data) as $col) {
        if ($col !== $uniqueKey) {
            $updates[] = "$col = VALUES($col)";
        }
    }

    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)
            ON DUPLICATE KEY UPDATE " . implode(',', $updates);

    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
}
```

## Query Profiling

```php
$pdo->exec('SET profiling = 1');

// After your queries:
$stmt = $pdo->query('SHOW PROFILES');
$profiles = $stmt->fetchAll();
print_r($profiles);
```

## Common Pitfalls

1. **Not setting `ERRMODE_EXCEPTION`** — Silent failures are the #1 PDO bug.
2. **Using `quote()` instead of prepared statements** — `quote()` is error-prone; always use prepared statements.
3. **Forgetting `EMULATE_PREPARES = false`** — With emulation on, PDO substitutes parameters client-side (security issue with some edge cases).
4. **Not using `fetchColumn()` for counts** — `fetchColumn()` returns the first column of the first row.
5. **Not closing large result sets** — PDOStatement can hold resources; unset large results `unset($stmt)`.
