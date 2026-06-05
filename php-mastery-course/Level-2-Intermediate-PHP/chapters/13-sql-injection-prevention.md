# Chapter 13: SQL Injection Prevention

## Learning Objectives

- Understand SQL injection attacks
- Use prepared statements exclusively
- Validate and sanitize inputs
- Implement defense in depth

---

## 13.1 How SQL Injection Works

```php
<?php
// ❌ VULNERABLE CODE
function getUserByName(string $name): array
{
    $sql = "SELECT * FROM users WHERE name = '{$name}'";
    // If $name = "' OR '1'='1", SQL becomes:
    // SELECT * FROM users WHERE name = '' OR '1'='1'
    // Returns ALL users!
    
    // If $name = "'; DROP TABLE users; --",
    // SQL becomes:
    // SELECT * FROM users WHERE name = ''; DROP TABLE users; --'
    // TABLE DELETED!
    
    return query($sql);
}

// ✅ SAFE: Use prepared statements
function findUserByName(string $name): ?array
{
    $stmt = $this->pdo->prepare('SELECT * FROM users WHERE name = ?');
    $stmt->execute([$name]);
    return $stmt->fetch() ?: null;
}
```

---

## 13.2 Defense in Depth

```php
<?php
class SecureQueryBuilder
{
    public function __construct(private PDO $pdo) {}

    // Always use prepared statements
    public function findUser(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE id = ? AND deleted_at IS NULL'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // Always whitelist dynamic identifiers (table/column names)
    public function listUsers(string $sortBy, string $direction): array
    {
        $allowedColumns = ['id', 'name', 'email', 'created_at'];
        $sortBy = in_array($sortBy, $allowedColumns) ? $sortBy : 'created_at';
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        $stmt = $this->pdo->prepare(
            "SELECT * FROM users ORDER BY {$sortBy} {$direction}"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Use type casting for numeric values
    public function getProducts(array $ids): array
    {
        $ids = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $stmt = $this->pdo->prepare(
            "SELECT * FROM products WHERE id IN ({$placeholders})"
        );
        $stmt->execute($ids);
        return $stmt->fetchAll();
    }

    // Validate LIKE patterns
    public function searchByName(string $term): array
    {
        // Escape LIKE wildcards
        $term = str_replace(['%', '_'], ['\\%', '\\_'], $term);
        
        $stmt = $this->pdo->prepare(
            'SELECT * FROM users WHERE name LIKE ?'
        );
        $stmt->execute(["%{$term}%"]);
        return $stmt->fetchAll();
    }
}
```

---

## 13.3 Exercises

1. Demonstrate how an SQL injection attack works in a vulnerable code snippet
2. Fix the vulnerable code using prepared statements
3. Implement a whitelist-based sort/filter system
4. Write a script that validates all inputs before database queries

---

## Further Reading

- **Doc:** [OWASP SQL Injection Prevention](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)
- **Doc:** [PHP PDO Prepared Statements](https://www.php.net/manual/en/pdo.prepared-statements.php)
