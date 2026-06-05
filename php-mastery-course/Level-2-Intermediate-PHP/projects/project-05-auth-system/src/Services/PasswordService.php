<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Core\Database;

class PasswordService
{
    public function requestReset(string $email): string
    {
        $user = User::findByEmail($email);

        if (!$user) {
            // Don't reveal if email exists
            throw new \RuntimeException('If the email exists, a reset link has been sent.');
        }

        $token = $user->generateResetToken();
        $user->save();

        // In production, send email here. For this project, return token.
        return $token;
    }

    public function resetPassword(string $token, string $newPassword): void
    {
        $hashedToken = hash('sha256', $token);
        $user = User::findByResetToken($hashedToken);

        if (!$user) {
            throw new \RuntimeException('Invalid or expired reset token.');
        }

        if (strlen($newPassword) < 8) {
            throw new \RuntimeException('Password must be at least 8 characters.');
        }

        $user->password = password_hash($newPassword, PASSWORD_BCRYPT);
        $user->clearResetToken();
        $user->save();
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): void
    {
        $user = User::find($userId);

        if (!$user) {
            throw new \RuntimeException('User not found.');
        }

        if (!$user->verifyPassword($currentPassword)) {
            throw new \RuntimeException('Current password is incorrect.');
        }

        if (strlen($newPassword) < 8) {
            throw new \RuntimeException('New password must be at least 8 characters.');
        }

        $user->password = password_hash($newPassword, PASSWORD_BCRYPT);
        $user->save();
    }
}
