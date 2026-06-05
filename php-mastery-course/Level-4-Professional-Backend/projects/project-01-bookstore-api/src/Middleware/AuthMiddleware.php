<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Services\JwtService;

class AuthMiddleware
{
    private JwtService $jwtService;

    public function __construct()
    {
        $this->jwtService = new JwtService();
    }

    public function process(Request $request, callable $next): Response
    {
        $token = $request->getBearerToken();

        if (!$token) {
            return Response::error('Authentication required', 401);
        }

        try {
            $payload = $this->jwtService->validateToken($token);
            $request->setRouteParam($payload->sub);
            return $next($request);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $status = str_contains($message, 'expired') ? 401 : 401;
            return Response::error($message, $status);
        }
    }

    public function requireScope(string $scope): callable
    {
        return function (Request $request, callable $next) use ($scope) {
            $token = $request->getBearerToken();
            if (!$token) {
                return Response::error('Authentication required', 401);
            }

            try {
                $payload = $this->jwtService->validateToken($token);
                $scopes = $payload->scopes ?? [];

                if (!in_array($scope, $scopes, true)) {
                    return Response::error('Insufficient permissions', 403);
                }

                $request->setRouteParam($payload->sub);
                return $next($request);
            } catch (\Exception $e) {
                return Response::error('Invalid token', 401);
            }
        };
    }
}
