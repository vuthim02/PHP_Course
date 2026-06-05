<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Services\JwtService;
use App\Validators\AuthValidator;

class AuthController
{
    private User $userModel;
    private JwtService $jwtService;

    public function __construct()
    {
        $this->userModel = new User();
        $this->jwtService = new JwtService();
    }

    public function register(Request $request): Response
    {
        $data = $request->getBody();

        $errors = AuthValidator::validateRegister($data);
        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $existing = $this->userModel->findByEmail($data['email']);
        if ($existing) {
            return Response::error('Email already registered', 409);
        }

        $userId = $this->userModel->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $tokens = $this->jwtService->generateToken($userId);
        $user = $this->userModel->findById($userId);

        return Response::json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'tokens' => $tokens,
            ],
        ], 201);
    }

    public function login(Request $request): Response
    {
        $data = $request->getBody();

        $errors = AuthValidator::validateLogin($data);
        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $user = $this->userModel->findByEmail($data['email']);
        if (!$user || !$this->userModel->verifyPassword($data['password'], $user['password'])) {
            return Response::error('Invalid email or password', 401);
        }

        $tokens = $this->jwtService->generateToken($user['id'], [$user['role']]);

        return Response::json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                ],
                'tokens' => $tokens,
            ],
        ]);
    }

    public function refresh(Request $request): Response
    {
        $data = $request->getBody();

        if (empty($data['refresh_token'])) {
            return Response::error('Refresh token is required', 422);
        }

        try {
            $tokens = $this->jwtService->refreshToken($data['refresh_token']);
            return Response::success($tokens);
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 401);
        }
    }

    public function me(Request $request): Response
    {
        $userId = (int) $request->getRouteParam();
        $user = $this->userModel->findById($userId);

        if (!$user) {
            return Response::error('User not found', 404);
        }

        return Response::success($user);
    }
}
