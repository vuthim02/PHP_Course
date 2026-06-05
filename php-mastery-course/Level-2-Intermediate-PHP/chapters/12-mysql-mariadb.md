# Chapter 12: MySQL and MariaDB

## Learning Objectives

- Design normalized database schemas
- Write complex JOIN queries
- Use indexes effectively
- Implement migrations

---

## 12.1 Schema Design

```sql
-- Normalized e-commerce schema
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    parent_id BIGINT UNSIGNED NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(280) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT UNSIGNED DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE RESTRICT,
    INDEX idx_active_price (is_active, price),
    INDEX idx_category (category_id),
    FULLTEXT INDEX idx_search (name, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_tags (
    product_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (product_id, tag_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

---

## 12.2 Complex Queries

```php
<?php
class ProductRepository
{
    public function __construct(private PDO $pdo) {}

    public function getProductsWithCategoryAndTags(
        ?string $category = null,
        ?string $search = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        string $sort = 'created_at',
        string $direction = 'DESC',
        int $page = 1,
        int $perPage = 20
    ): array {
        $where = ['p.is_active = 1'];
        $params = [];

        if ($category) {
            $where[] = 'c.slug = :category';
            $params['category'] = $category;
        }

        if ($search) {
            $where[] = 'MATCH(p.name, p.description) AGAINST(:search IN BOOLEAN MODE)';
            $params['search'] = $search . '*';
        }

        if ($minPrice !== null) {
            $where[] = 'p.price >= :min_price';
            $params['min_price'] = $minPrice;
        }

        if ($maxPrice !== null) {
            $where[] = 'p.price <= :max_price';
            $params['max_price'] = $maxPrice;
        }

        $allowedSorts = ['price', 'created_at', 'name', 'popularity'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        // Count query
        $countStmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM products p
             JOIN categories c ON p.category_id = c.id
             WHERE {$whereClause}"
        );
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Data query
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug,
                       GROUP_CONCAT(t.name SEPARATOR ', ') as tags
                FROM products p
                JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_tags pt ON p.id = pt.product_id
                LEFT JOIN tags t ON pt.tag_id = t.id
                WHERE {$whereClause}
                GROUP BY p.id
                ORDER BY {$sort} {$direction}
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => (int)ceil($total / $perPage),
        ];
    }
}
```

---

## 12.3 Exercises

1. Design a schema for a blog with posts, comments, and likes
2. Write a query that fetches posts with author, comment count, and like count
3. Add appropriate indexes and verify with EXPLAIN
4. Create a migration system that tracks schema versions

---

## Further Reading

- **Doc:** [MySQL Documentation](https://dev.mysql.com/doc/)
- **Doc:** [MariaDB Documentation](https://mariadb.com/docs/)
