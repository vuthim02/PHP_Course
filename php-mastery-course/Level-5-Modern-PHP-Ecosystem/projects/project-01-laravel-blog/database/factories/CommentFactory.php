<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'body' => '<p>' . implode('</p><p>', fake()->paragraphs(2)) . '</p>',
            'is_approved' => fake()->boolean(70),
        ];
    }
}
