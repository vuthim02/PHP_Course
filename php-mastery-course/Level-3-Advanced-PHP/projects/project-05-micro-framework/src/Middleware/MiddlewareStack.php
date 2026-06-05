<?php

declare(strict_types=1);

namespace MicroFramework\Middleware;

use MicroFramework\Http\Request;
use MicroFramework\Http\Response;

class MiddlewareStack implements RequestHandlerInterface
{
    private array $middleware = [];
    private RequestHandlerInterface $coreHandler;

    public function __construct(?RequestHandlerInterface $coreHandler = null)
    {
        $this->coreHandler = $coreHandler ?? new class implements RequestHandlerInterface {
            public function handle(Request $request): Response
            {
                return new Response(404, 'Not Found');
            }
        };
    }

    public function add(MiddlewareInterface $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    public function addCallable(callable $middleware): void
    {
        $this->middleware[] = new class($middleware) implements MiddlewareInterface {
            public function __construct(private readonly callable $callback) {}
            public function process(Request $request, RequestHandlerInterface $handler): Response
            {
                return ($this->callback)($request, $handler);
            }
        };
    }

    public function setCoreHandler(RequestHandlerInterface $handler): void
    {
        $this->coreHandler = $handler;
    }

    public function handle(Request $request): Response
    {
        return $this->processMiddleware(0, $request);
    }

    private function processMiddleware(int $index, Request $request): Response
    {
        if ($index < count($this->middleware)) {
            $middleware = $this->middleware[$index];
            $next = new class($this, $index) implements RequestHandlerInterface {
                public function __construct(
                    private MiddlewareStack $stack,
                    private int $index
                ) {}
                public function handle(Request $request): Response
                {
                    return $this->stack->processMiddleware($this->index + 1, $request);
                }
            };
            return $middleware->process($request, $next);
        }

        return $this->coreHandler->handle($request);
    }
}
