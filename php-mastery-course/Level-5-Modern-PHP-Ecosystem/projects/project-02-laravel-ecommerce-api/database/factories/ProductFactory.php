<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraphs(3, true),
            'price' => fake()->randomFloat(2, 10, 500),
            'compare_price' => fake()->optional(0.3)->randomFloat(2, 20, 600),
            'stock_quantity' => fake()->numberBetween(0, 500),
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'is_active' => true,
            'category_id' => Category::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attrs) => ['is_active' => false]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn(array $attrs) => ['stock_quantity' => 0]);
    }
}
