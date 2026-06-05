<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        $electronics = Category::factory()->create(['name' => 'Electronics']);
        $clothing = Category::factory()->create(['name' => 'Clothing']);
        $home = Category::factory()->create(['name' => 'Home & Garden']);
        $books = Category::factory()->create(['name' => 'Books']);

        $laptop = Product::factory()
            ->recycle($electronics)
            ->has(ProductVariant::factory()->count(2), 'variants')
            ->create([
                'name' => 'ProBook X1 Laptop',
                'price' => 1299.99,
                'compare_price' => 1499.99,
                'stock_quantity' => 50,
            ]);

        Product::factory()
            ->recycle($clothing)
            ->create([
                'name' => 'Classic T-Shirt',
                'price' => 29.99,
                'stock_quantity' => 200,
            ]);

        Product::factory()
            ->recycle($home)
            ->count(3)
            ->create();

        Product::factory()
            ->recycle($books)
            ->count(5)
            ->create();
    }
}
