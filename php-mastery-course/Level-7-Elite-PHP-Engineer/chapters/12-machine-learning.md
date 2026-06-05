# Chapter 12: Machine Learning in PHP

## Learning Objectives

- Implement ML algorithms in PHP
- Use PHP-ML library for classification, regression, clustering
- Build prediction services with trained models
- Deploy ML models as PHP microservices
- Understand when ML in PHP is practical vs when to use Python

---

## 12.1 PHP-ML: Classification, Regression, Clustering

```php
<?php
use Phpml\Classification\KNearestNeighbors;
use Phpml\Classification\NaiveBayes;
use Phpml\Regression\LeastSquares;
use Phpml\Clustering\KMeans;
use Phpml\CrossValidation\RandomSplit;
use Phpml\Metric\Accuracy;
use Phpml\Preprocessing\LabelEncoder;
use Phpml\FeatureExtraction\TfIdfTransformer;

class MLService
{
    public function classifyText(array $samples, array $labels, string $newText): string
    {
        $classifier = new NaiveBayes();
        $classifier->train($samples, $labels);
        return $classifier->predict([$newText])[0];
    }

    public function predictPrice(array $trainingData, array $features): float
    {
        $samples = [];
        $targets = [];

        foreach ($trainingData as $data) {
            $samples[] = [$data['size'], $data['rooms'], $data['age']];
            $targets[] = $data['price'];
        }

        $regression = new LeastSquares();
        $regression->train($samples, $targets);
        return $regression->predict([$features]);
    }

    public function clusterUsers(array $users, int $clusters = 3): array
    {
        $samples = array_map(fn($u) => [
            $u['age'],
            $u['purchases'],
            $u['avg_order_value'],
        ], $users);

        $kmeans = new KMeans($clusters);
        return $kmeans->cluster($samples);
    }
}

// Usage
$ml = new MLService();

// Text classification
$samples = [
    'Great product, fast shipping',
    'Terrible quality, broke immediately',
    'Average experience, nothing special',
];
$labels = ['positive', 'negative', 'neutral'];

$prediction = $ml->classifyText($samples, $labels, 'Amazing quality, highly recommend');
echo "Sentiment: {$prediction}\n"; // positive

// Price prediction
$training = [
    ['size' => 100, 'rooms' => 2, 'age' => 5, 'price' => 250000],
    ['size' => 200, 'rooms' => 4, 'age' => 2, 'price' => 500000],
    ['size' => 50, 'rooms' => 1, 'age' => 10, 'price' => 150000],
];

$predicted = $ml->predictPrice($training, [150, 3, 3]);
echo "Predicted price: \${$predicted}\n";
```

---

## 12.2 Building a Recommendation Engine

```php
<?php
class ProductRecommender
{
    private array $interactionMatrix = [];
    private array $productSimilarity = [];

    // Collaborative filtering: users who bought X also bought Y
    public function buildSimilarityMatrix(array $purchases): void
    {
        // $purchases = [[user_id, product_id], ...]
        $productUsers = [];
        foreach ($purchases as [$userId, $productId]) {
            $productUsers[$productId][] = $userId;
        }

        $productIds = array_keys($productUsers);
        foreach ($productIds as $p1) {
            foreach ($productIds as $p2) {
                if ($p1 === $p2) continue;
                $common = array_intersect($productUsers[$p1], $productUsers[$p2]);
                $union = array_unique(array_merge($productUsers[$p1], $productUsers[$p2]));
                $this->productSimilarity[$p1][$p2] = count($common) / max(count($union), 1);
            }
        }
    }

    public function recommend(int $productId, int $limit = 5): array
    {
        $similarities = $this->productSimilarity[$productId] ?? [];
        arsort($similarities);
        return array_slice(array_keys($similarities), 0, $limit);
    }
}

// Usage
$recommender = new ProductRecommender();
$recommender->buildSimilarityMatrix($purchaseHistory);
$recommendations = $recommender->recommend($currentProductId);
```

---

## 12.3 Deploying ML as a Microservice

```php
<?php
// api/predict.php
require __DIR__ . '/../vendor/autoload.php';

// Load pre-trained model
$modelPath = __DIR__ . '/../models/churn_model.phpml';
$classifier = unserialize(file_get_contents($modelPath));

// API endpoint
$input = json_decode(file_get_contents('php://input'), true);

$features = [
    $input['days_since_last_login'],
    $input['total_purchases'],
    $input['avg_order_value'],
    $input['support_tickets'],
    $input['is_subscribed'] ? 1 : 0,
];

$prediction = $classifier->predict([$features])[0];
$probability = $classifier->predictProbability([$features]);

http_response_code(200);
header('Content-Type: application/json');
echo json_encode([
    'will_churn' => $prediction === 'yes',
    'probability' => max($probability),
    'confidence' => 'high',
]);
```

### Model Training Script

```php
<?php
// train_churn_model.php
use Phpml\Classification\RandomForest;

// Load training data from database
$stmt = $pdo->query('SELECT features, label FROM training_data');
$samples = [];
$labels = [];

while ($row = $stmt->fetch()) {
    $samples[] = json_decode($row['features'], true);
    $labels[] = $row['label'];
}

// Train and persist model
$classifier = new RandomForest(50); // 50 trees
$classifier->train($samples, $labels);

file_put_contents(
    __DIR__ . '/models/churn_model.phpml',
    serialize($classifier)
);

echo "Model trained and saved.\n";
```

---

## 12.4 When to Use PHP vs Python for ML

| Factor | PHP | Python |
|--------|-----|--------|
| ML libraries | PHP-ML (basic algorithms) | scikit-learn, TensorFlow, PyTorch |
| Deep learning | Not practical | Extensive support |
| NLP | Basic (TF-IDF, Naive Bayes) | Advanced (transformers, BERT) |
| Computer vision | Limited (GD/Imagick) | OpenCV, YOLO, Detectron |
| Model serving | Good (built-in web server) | FastAPI, Flask, TensorFlow Serving |
| Integration | Same codebase as app | Separate service + API call |

**Recommendation:** Use PHP-ML for simple classification and regression tasks. For deep learning or complex NLP, expose Python models via a REST API and call them from PHP with Guzzle.

---

## 12.5 Exercises

1. Build a spam classifier with PHP-ML using a dataset of emails
2. Implement a product recommendation engine using collaborative filtering
3. Create a customer churn prediction model and deploy it as an API endpoint
4. Build an A/B test result analyzer that determines statistical significance
5. Implement a simple image classifier using pixel color histograms
6. Create a pipeline that trains a model nightly and deploys it as a microservice
7. Benchmark PHP-ML vs Python scikit-learn on the same dataset

---

## Further Reading

- **Doc:** [PHP-ML](https://php-ml.readthedocs.io/)
- **Doc:** [PHP-ML GitHub](https://github.com/php-ai/php-ml)
- **Book:** "Machine Learning with PHP" by Nils Adermann
- **Tool:** [Rubix ML](https://rubixml.com/) — alternative ML library for PHP
