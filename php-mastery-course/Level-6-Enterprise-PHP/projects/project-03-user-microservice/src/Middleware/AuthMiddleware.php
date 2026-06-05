<?php

declare(strict_types=1);

namespace UserService\Middleware;

use UserService\Services\AuthService;

/**
 * AuthMiddleware
 *
 * Middleware that validates Bearer tokens from the Authorization header.
 * In a microservice, authentication is typically handled at the gateway level,
 * but each service may also validate tokens for defense-in-depth.
 */
final class AuthMiddleware
{
    public function __construct(private AuthService $authService)
    {
    }

    /**
     * Process the request. Returns user ID if authenticated, null otherwise.
     */
    public function process(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return null;
        }

        $token = $matches[1];
        return $this->authService->validateToken($token);
    }

    /**
     * Require authentication. Sends 401 if not authenticated.
     */
    public function require(): string
    {
        $userId = $this->process();

        if ($userId === null) {
            http_response_code(401);
            echo json_encode(['error' => 'Authentication required']);
            exit;
        }

        return $userId;
    }
}
