<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);
        $body = '<p>' . implode('</p><p>', fake()->paragraphs(5)) . '</p>';

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'body' => $body,
            'excerpt' => fake()->paragraph(2),
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-3 months'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
