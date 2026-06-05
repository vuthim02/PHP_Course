<?php

declare(strict_types=1);

namespace MicroFramework\Middleware;

use MicroFramework\Http\Request;
use MicroFramework\Http\Response;

interface RequestHandlerInterface
{
    public function handle(Request $request): Response;
}
