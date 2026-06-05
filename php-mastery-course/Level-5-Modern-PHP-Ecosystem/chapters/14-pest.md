# Chapter 14: Testing with Pest PHP

## Learning Objectives

- Write expressive tests with Pest
- Use higher-order tests
- Create custom expectations
- Implement Pest for API testing

---

## 14.1 Pest Syntax

```php
<?php
// Pest PHP — expressive and elegant testing

uses(Tests\TestCase::class)->in('Feature');

// Basic test
it('can calculate order total', function () {
    $order = new Order(['items' => [
        ['price' => 10, 'quantity' => 2],
        ['price' => 5, 'quantity' => 3],
    ]]);

    expect($order->total())->toBe(35.00);
});

// Higher-order test
test('product prices are positive')
    ->expect(fn() => new Product(['price' => -10]))
    ->toThrow(InvalidArgumentException::class);

// Dataset tests
dataset('product_prices', [
    [10.00, 12.00],   // price, expected with tax
    [100.00, 120.00],
    [0.00, 0.00],
]);

it('calculates price with tax correctly', function (float $price, float $expected) {
    $product = new Product(['price' => $price]);
    expect($product->priceWithTax())->toBe($expected);
})->with('product_prices');

// API Testing
it('returns paginated list of posts', function () {
    Post::factory()->count(30)->create();

    $response = $this->getJson('/api/posts');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [['id', 'title', 'content']],
            'meta' => ['current_page', 'last_page', 'total'],
        ]);
});

it('requires authentication for creating posts', function () {
    $this->postJson('/api/posts', [
        'title' => 'Test',
    ])->assertStatus(401);
});

it('validates required fields', function () {
    $this->actingAs(User::factory()->create())
        ->postJson('/api/posts', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'content']);
});

// Architecture tests
test('controllers')
    ->expect('App\Http\Controllers')
    ->toExtendNothing()
    ->toUse('Illuminate\Http\JsonResponse');

test('globals')
    ->expect(['dd', 'dump', 'var_dump'])
    ->not->toBeUsed();
```

---

## 14.2 Exercises

1. Convert PHPUnit tests to Pest for a service class
2. Write higher-order tests for validation rules
3. Create dataset tests for edge cases
4. Add architecture tests to enforce coding standards

---

## Further Reading

- **Doc:** [Pest PHP](https://pestphp.com/)
