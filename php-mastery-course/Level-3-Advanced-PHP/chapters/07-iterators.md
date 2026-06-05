# Chapter 7: Iterators

## Learning Objectives

- Implement the Iterator interface
- Use SPL iterators
- Create custom iterable objects
- Combine iterators for data processing

---

## 7.1 Iterator Interface

```php
<?php
class PaginatedCollection implements \Iterator
{
    private int $position = 0;
    private array $items = [];
    private bool $hasMore = true;

    public function __construct(
        private int $pageSize = 20,
    ) {}

    public function current(): mixed
    {
        return $this->items[$this->position];
    }

    public function next(): void
    {
        $this->position++;
        
        // Fetch next page if needed
        if ($this->position >= count($this->items) && $this->hasMore) {
            $this->fetchNextPage();
        }
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
        $this->items = [];
        $this->hasMore = true;
        $this->fetchNextPage();
    }

    private function fetchNextPage(): void
    {
        $page = (int)floor($this->position / $this->pageSize) + 1;
        $result = $this->fetchFromApi($page);
        
        $this->items = array_merge($this->items, $result['data']);
        $this->hasMore = $result['has_more'];
    }

    private function fetchFromApi(int $page): array
    {
        // Simulated API call
        return [
            'data' => range(1, $this->pageSize),
            'has_more' => $page < 10,
        ];
    }
}

// Usage
foreach (new PaginatedCollection() as $key => $item) {
    echo "{$key}: {$item}\n";
}
```

---

## 7.2 SPL Iterators

```php
<?php
// FilterIterator
class ProductFilterIterator extends \FilterIterator
{
    public function __construct(
        \Iterator $iterator,
        private float $minPrice,
        private string $category,
    ) {
        parent::__construct($iterator);
    }

    public function accept(): bool
    {
        $product = $this->current();
        return $product['price'] >= $this->minPrice
            && $product['category'] === $this->category;
    }
}

$products = [
    ['name' => 'Laptop', 'price' => 1200, 'category' => 'electronics'],
    ['name' => 'Shirt', 'price' => 25, 'category' => 'clothing'],
    ['name' => 'Phone', 'price' => 800, 'category' => 'electronics'],
];

$filtered = new ProductFilterIterator(
    new \ArrayIterator($products),
    500,
    'electronics'
);

foreach ($filtered as $product) {
    print_r($product);
}

// LimitIterator
$limited = new \LimitIterator(
    new \ArrayIterator(range(1, 100)),
    10,  // Start at index 10
    20   // Take 20 items
);

// AppendIterator (merge multiple iterators)
$first = new \ArrayIterator(['a', 'b', 'c']);
$second = new \ArrayIterator([1, 2, 3]);
$merged = new \AppendIterator();
$merged->append($first);
$merged->append($second);

foreach ($merged as $item) {
    echo $item; // a b c 1 2 3
}
```

---

## 7.3 Exercises

1. Create an Iterator that traverses a directory tree recursively
2. Implement a FilterIterator for user age > 18
3. Use LimitIterator to implement pagination
4. Combine multiple iterators with AppendIterator

---

## Further Reading

- **Doc:** [PHP Iterators](https://www.php.net/manual/en/class.iterator.php)
- **Doc:** [SPL Iterators](https://www.php.net/manual/en/spl.iterators.php)
