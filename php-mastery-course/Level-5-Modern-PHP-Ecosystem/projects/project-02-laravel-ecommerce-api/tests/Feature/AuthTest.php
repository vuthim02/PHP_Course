<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'alice@example.com']);
    }

    public function test_user_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'alice@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'bob@example.com',
            'password' => bcrypt('SecurePass123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'bob@example.com',
            'password' => 'SecurePass123!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'bob@example.com',
            'password' => bcrypt('SecurePass123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'bob@example.com',
            'password' => 'WrongPassword123!',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_access_profile(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/profile');

        $response->assertStatus(200);
        $response->assertJson(['email' => $user->email]);
    }
}
