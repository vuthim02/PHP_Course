<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Core\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        $_GET = [];
        $_POST = [];
        $_SERVER = [];
    }

    public function test_get_returns_default_when_key_missing(): void
    {
        $this->assertEquals('default', Request::get('nonexistent', 'default'));
    }

    public function test_get_returns_value_when_key_exists(): void
    {
        $_GET['name'] = 'Alice';
        $this->assertEquals('Alice', Request::get('name'));
    }

    public function test_post_returns_default_when_key_missing(): void
    {
        $this->assertEquals(null, Request::post('nonexistent'));
    }

    public function test_has_returns_true_for_existing_key(): void
    {
        $_POST['email'] = 'test@example.com';
        $this->assertTrue(Request::has('email'));
    }

    public function test_has_returns_false_for_missing_key(): void
    {
        $this->assertFalse(Request::has('missing'));
    }

    public function test_all_merges_get_and_post(): void
    {
        $_GET['page'] = '2';
        $_POST['name'] = 'Bob';
        $this->assertEquals(['page' => '2', 'name' => 'Bob'], Request::all());
    }
}
