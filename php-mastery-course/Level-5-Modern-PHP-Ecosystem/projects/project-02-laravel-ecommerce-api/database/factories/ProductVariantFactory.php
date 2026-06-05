<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        $sizes = ['S', 'M', 'L', 'XL'];
        $colors = ['Black', 'White', 'Blue', 'Red'];

        return [
            'name' => fake()->randomElement($sizes) . ' / ' . fake()->randomElement($colors),
            'sku' => 'VAR-' . strtoupper(Str::random(8)),
            'price' => fake()->optional(0.5)->randomFloat(2, 5, 100),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'attributes' => [
                'size' => fake()->randomElement($sizes),
                'color' => fake()->randomElement($colors),
            ],
        ];
    }
}
