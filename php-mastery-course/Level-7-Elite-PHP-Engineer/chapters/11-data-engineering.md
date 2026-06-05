# Chapter 11: Data Engineering

## Learning Objectives

- Build ETL pipelines in PHP
- Implement data warehousing
- Create analytics systems
- Handle big data processing

---

## 11.1 ETL Pipeline

```php
<?php
namespace App\Data\ETL;

abstract class EtlPipeline
{
    abstract public function extract(): \Generator;
    abstract public function transform(array $row): array;
    abstract public function load(array $rows): void;

    public function run(): void
    {
        $batch = [];
        $batchSize = 1000;
        $total = 0;

        foreach ($this->extract() as $row) {
            $batch[] = $this->transform($row);
            $total++;

            if (count($batch) >= $batchSize) {
                $this->load($batch);
                echo "Processed {$total} records\n";
                $batch = [];
            }
        }

        // Load remaining
        if (!empty($batch)) {
            $this->load($batch);
        }

        echo "ETL complete: {$total} records processed\n";
    }
}

// Example: Import orders from API to warehouse
class OrderETL extends EtlPipeline
{
    public function __construct(
        private ApiClient $api,
        private PDO $warehouse,
    ) {}

    public function extract(): \Generator
    {
        $page = 1;
        do {
            $response = $this->api->get("/orders", ['page' => $page]);
            foreach ($response['data'] as $order) {
                yield $order;
            }
            $page++;
            $hasMore = $response['meta']['has_more'] ?? false;
        } while ($hasMore);
    }

    public function transform(array $row): array
    {
        return [
            'order_id' => $row['id'],
            'customer_id' => $row['customer']['id'],
            'customer_email' => $row['customer']['email'],
            'total' => $row['total'],
            'currency' => $row['currency'],
            'status' => $row['status'],
            'items_count' => count($row['items']),
            'created_date' => date('Y-m-d', strtotime($row['created_at'])),
            'created_at' => $row['created_at'],
        ];
    }

    public function load(array $rows): void
    {
        $this->warehouse->beginTransaction();
        
        $stmt = $this->warehouse->prepare(
            'INSERT INTO fact_orders (order_id, customer_id, customer_email, total, currency, status, items_count, created_date, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE status = VALUES(status), total = VALUES(total)'
        );

        foreach ($rows as $row) {
            $stmt->execute([
                $row['order_id'],
                $row['customer_id'],
                $row['customer_email'],
                $row['total'],
                $row['currency'],
                $row['status'],
                $row['items_count'],
                $row['created_date'],
                $row['created_at'],
            ]);
        }

        $this->warehouse->commit();
    }
}
```

---

## 11.2 Exercises

1. Build an ETL pipeline that imports data from an external API
2. Design a star schema data warehouse
3. Create aggregate tables for reporting
4. Schedule ETL jobs with cron or a scheduler

---

## Further Reading

- **Doc:** [Kimball Dimensional Modeling](https://www.kimballgroup.com/data-warehouse-business-intelligence-resources/)
- **Tool:** [Apache Airflow](https://airflow.apache.org/)
