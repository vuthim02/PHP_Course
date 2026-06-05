# Chapter 17: Search Implementation

## Learning Objectives

- Implement full-text search with MySQL
- Integrate Elasticsearch for advanced search
- Build search APIs with faceting
- Handle search indexing

---

## 17.1 Elasticsearch Integration

```php
<?php
namespace App\Search;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchEngine
{
    private Client $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([$_ENV['ELASTICSEARCH_HOST'] ?? 'localhost:9200'])
            ->setBasicAuthentication(
                $_ENV['ELASTICSEARCH_USER'] ?? '',
                $_ENV['ELASTICSEARCH_PASS'] ?? ''
            )
            ->build();
    }

    public function index(string $index, string $id, array $body): void
    {
        $this->client->index([
            'index' => $index,
            'id' => $id,
            'body' => $body,
        ]);
    }

    public function search(string $index, string $query, array $filters = []): array
    {
        $must = [
            'multi_match' => [
                'query' => $query,
                'fields' => ['title^3', 'content', 'tags^2', 'category'],
                'fuzziness' => 'AUTO',
            ],
        ];

        foreach ($filters as $field => $value) {
            $must[] = ['term' => [$field => $value]];
        }

        $params = [
            'index' => $index,
            'body' => [
                'query' => [
                    'bool' => ['must' => $must],
                ],
                'highlight' => [
                    'fields' => [
                        'content' => ['fragment_size' => 150, 'number_of_fragments' => 3],
                        'title' => ['fragment_size' => 100],
                    ],
                ],
                'aggs' => [
                    'categories' => ['terms' => ['field' => 'category.keyword']],
                    'price_range' => [
                        'range' => [
                            'field' => 'price',
                            'ranges' => [
                                ['to' => 50],
                                ['from' => 50, 'to' => 100],
                                ['from' => 100, 'to' => 500],
                                ['from' => 500],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this->client->search($params)->asArray();
    }

    public function bulkIndex(string $index, array $documents): void
    {
        $params = ['body' => []];
        
        foreach ($documents as $doc) {
            $params['body'][] = ['index' => ['_index' => $index, '_id' => $doc['id']]];
            $params['body'][] = $doc;
        }

        $this->client->bulk($params);
    }

    public function deleteIndex(string $index): void
    {
        if ($this->client->indices()->exists(['index' => $index])->asBool()) {
            $this->client->indices()->delete(['index' => $index]);
        }
    }
}

// Index a product
$search = new ElasticsearchEngine();
$search->index('products', '123', [
    'id' => 123,
    'title' => 'PHP Programming Book',
    'content' => 'Learn PHP from beginner to advanced...',
    'category' => 'Books',
    'price' => 39.99,
    'tags' => ['php', 'programming', 'web'],
    'created_at' => '2024-01-15',
]);
```

---

## 17.2 Exercises

1. Implement full-text search with MySQL FULLTEXT indexes
2. Set up Elasticsearch and index your database content
3. Build a search API with faceted navigation
4. Implement autocomplete/suggest functionality

---

## Further Reading

- **Doc:** [Elasticsearch PHP](https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/index.html)
- **Doc:** [Meilisearch PHP](https://github.com/meilisearch/meilisearch-php)
