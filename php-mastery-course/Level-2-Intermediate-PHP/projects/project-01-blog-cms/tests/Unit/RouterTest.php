<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Core\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
    }

    public function test_register_get_route(): void
    {
        $handler = fn() => 'hello';
        $this->router->get('/test', $handler);

        $reflection = new \ReflectionClass($this->router);
        $routesProp = $reflection->getProperty('routes');
        $routes = $routesProp->getValue($this->router);

        $this->assertCount(1, $routes);
        $this->assertEquals('GET', $routes[0]['method']);
    }

    public function test_register_post_route(): void
    {
        $handler = fn() => 'created';
        $this->router->post('/users', $handler);

        $reflection = new \ReflectionClass($this->router);
        $routesProp = $reflection->getProperty('routes');
        $routes = $routesProp->getValue($this->router);

        $this->assertCount(1, $routes);
        $this->assertEquals('POST', $routes[0]['method']);
    }

    public function test_route_with_parameters_converts_pattern(): void
    {
        $handler = fn(array $params) => $params;
        $this->router->get('/users/{id}', $handler);

        $reflection = new \ReflectionClass($this->router);
        $routesProp = $reflection->getProperty('routes');
        $routes = $routesProp->getValue($this->router);

        $this->assertStringContainsString('(?P<id>[^/]+)', $routes[0]['pattern']);
    }
}
