<?php

declare(strict_types=1);

namespace MicroFramework\Middleware;

use MicroFramework\Http\Request;
use MicroFramework\Http\Response;

interface MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response;
}
