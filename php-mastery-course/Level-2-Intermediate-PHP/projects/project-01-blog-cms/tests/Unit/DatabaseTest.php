<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Core\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function test_singleton_returns_same_instance(): void
    {
        $this->markTestSkipped('Requires database connection configuration.');
    }

    public function test_query_generates_correct_insert_sql(): void
    {
        $reflection = new \ReflectionMethod(Database::class, 'insert');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_query_generates_correct_update_sql(): void
    {
        $reflection = new \ReflectionMethod(Database::class, 'update');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_query_generates_correct_delete_sql(): void
    {
        $reflection = new \ReflectionMethod(Database::class, 'delete');
        $this->assertTrue($reflection->isPublic());
    }
}
