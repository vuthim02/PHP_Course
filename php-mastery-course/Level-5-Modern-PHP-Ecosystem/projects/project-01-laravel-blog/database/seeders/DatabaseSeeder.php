<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        User::factory(5)->create();

        $categories = Category::factory(5)->create();
        $tags = Tag::factory(10)->create();

        Post::factory(20)
            ->recycle($admin)
            ->recycle($categories)
            ->recycle($tags)
            ->has(Comment::factory(3)->recycle(User::all()), 'comments')
            ->create();
    }
}
