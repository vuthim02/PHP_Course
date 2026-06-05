<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class CorsMiddleware
{
    public function process(Request $request, callable $next): Response
    {
        $origin = $request->getHeader('Origin', '*');

        $allowedOrigins = [
            'http://localhost:3000',
            'http://localhost:8080',
            'https://bookstore.example.com',
        ];

        $origin = in_array($origin, $allowedOrigins) ? $origin : '';

        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response(null, 204);
            $response->setHeader('Access-Control-Allow-Origin', $origin ?: '*');
            $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $response->setHeader('Access-Control-Max-Age', '86400');
            $response->setHeader('Access-Control-Allow-Credentials', 'true');
            return $response;
        }

        $response = $next($request);

        if ($origin) {
            $response->setHeader('Access-Control-Allow-Origin', $origin);
        }
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        $response->setHeader('Vary', 'Origin');

        return $response;
    }
}
