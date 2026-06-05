# Chapter 20: Enterprise Security (SOC2, PCI DSS, GDPR)

## Learning Objectives

- Implement security compliance
- Build audit logging
- Handle data privacy (GDPR)
- Meet PCI DSS requirements

---

## 20.1 Audit Logging

```php
<?php
class AuditLogger
{
    public function __construct(
        private \PDO $pdo,
        private string $appName,
    ) {}

    public function log(
        string $action,
        string $entityType,
        string $entityId,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $userId = null,
    ): void {
        $stmt = $this->pdo->prepare(
            'INSERT INTO audit_logs
             (app_name, action, entity_type, entity_id, old_values, new_values, user_id, ip_address, user_agent, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
        );
        
        $stmt->execute([
            $this->appName,
            $action,
            $entityType,
            $entityId,
            $oldValues ? json_encode($oldValues) : null,
            $newValues ? json_encode($newValues) : null,
            $userId,
            $_SERVER['REMOTE_ADDR'] ?? 'CLI',
            $_SERVER['HTTP_USER_AGENT'] ?? 'CLI',
        ]);
    }

    public function query(array $filters, int $page = 1, int $perPage = 50): array
    {
        $where = [];
        $params = [];

        foreach (['action', 'entity_type', 'entity_id', 'user_id'] as $field) {
            if (isset($filters[$field])) {
                $where[] = "{$field} = ?";
                $params[] = $filters[$field];
            }
        }

        if (isset($filters['from'])) {
            $where[] = 'created_at >= ?';
            $params[] = $filters['from'];
        }

        if (isset($filters['to'])) {
            $where[] = 'created_at <= ?';
            $params[] = $filters['to'];
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset = ($page - 1) * $perPage;

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM audit_logs {$whereClause}");
        $stmt->execute($params);
        $count = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare(
            "SELECT * FROM audit_logs {$whereClause} ORDER BY created_at DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute([...$params, $perPage, $offset]);

        return [
            'data' => $stmt->fetchAll(),
            'total' => $count,
            'page' => $page,
        ];
    }

    // Audit logs table
    // CREATE TABLE audit_logs (
    //     id BIGINT AUTO_INCREMENT PRIMARY KEY,
    //     app_name VARCHAR(100) NOT NULL,
    //     action VARCHAR(50) NOT NULL,
    //     entity_type VARCHAR(50) NOT NULL,
    //     entity_id VARCHAR(100) NOT NULL,
    //     old_values JSON,
    //     new_values JSON,
    //     user_id VARCHAR(100),
    //     ip_address VARCHAR(45),
    //     user_agent TEXT,
    //     created_at DATETIME NOT NULL,
    //     INDEX idx_entity (entity_type, entity_id),
    //     INDEX idx_user (user_id),
    //     INDEX idx_action (action),
    //     INDEX idx_created (created_at)
    // );
}
```

---

## 20.2 Exercises

1. Implement comprehensive audit logging
2. Add data anonymization for GDPR compliance
3. Implement encryption at rest and in transit
4. Create security policies documentation

---

## Further Reading

- **Doc:** [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- **Doc:** [GDPR Compliance](https://gdpr.eu/)
- **Doc:** [PCI DSS](https://www.pcisecuritystandards.org/)
