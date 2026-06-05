<?php

declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use RuntimeException;

class JwtService
{
    private array $config;
    private string $secret;
    private string $algorithm;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/jwt.php';
        $this->secret = $this->config['secret'];
        $this->algorithm = $this->config['algorithm'];
    }

    public function generateToken(int $userId, array $scopes = ['user']): array
    {
        $now = time();

        $accessPayload = [
            'iss' => $this->config['issuer'],
            'sub' => $userId,
            'iat' => $now,
            'exp' => $now + $this->config['expiry'],
            'scopes' => $scopes,
            'type' => 'access',
        ];

        $refreshPayload = [
            'iss' => $this->config['issuer'],
            'sub' => $userId,
            'iat' => $now,
            'exp' => $now + $this->config['refresh_expiry'],
            'type' => 'refresh',
        ];

        return [
            'access_token' => JWT::encode($accessPayload, $this->secret, $this->algorithm),
            'refresh_token' => JWT::encode($refreshPayload, $this->secret, $this->algorithm),
            'expires_in' => $this->config['expiry'],
            'token_type' => 'Bearer',
        ];
    }

    public function validateToken(string $token): object
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));
            return $decoded;
        } catch (ExpiredException $e) {
            throw new RuntimeException('Token has expired');
        } catch (\Exception $e) {
            throw new RuntimeException('Invalid token');
        }
    }

    public function refreshToken(string $refreshToken): array
    {
        try {
            $decoded = JWT::decode($refreshToken, new Key($this->secret, $this->algorithm));

            if (!isset($decoded->type) || $decoded->type !== 'refresh') {
                throw new RuntimeException('Invalid refresh token');
            }

            return $this->generateToken((int) $decoded->sub, $decoded->scopes ?? ['user']);
        } catch (ExpiredException $e) {
            throw new RuntimeException('Refresh token has expired. Please login again.');
        } catch (\Exception $e) {
            throw new RuntimeException('Invalid refresh token');
        }
    }
}
