<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class User
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = db()->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([":email" => strtolower(trim($email))]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([":id" => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /** Creates a user and returns the new id. Passwords are stored as bcrypt hashes. */
    public static function create(string $name, string $email, string $password): int
    {
        $stmt = db()->prepare(
            "INSERT INTO users (name, email, password_hash)
             VALUES (:name, :email, :hash)"
        );
        $stmt->execute([
            ":name"  => trim($name),
            ":email" => strtolower(trim($email)),
            ":hash"  => password_hash($password, PASSWORD_DEFAULT),
        ]);
        return (int) db()->lastInsertId();
    }

    /** Returns the user row only when the email + password match. */
    public static function verify(string $email, string $password): ?array
    {
        $user = self::findByEmail($email);
        if ($user === null || !password_verify($password, $user["password_hash"])) {
            return null;
        }
        return $user;
    }

    public static function updateShippingAddress(int $id, array $address): void
    {
        $stmt = db()->prepare(
            "UPDATE users
             SET shipping_address = :addr, city = :city, zip = :zip
             WHERE id = :id"
        );
        $stmt->execute([
            ":addr" => $address["shipping_address"],
            ":city" => $address["city"],
            ":zip"  => $address["zip"],
            ":id"   => $id,
        ]);
    }

    // ---- Admin helpers -------------------------------------------------------

    public static function adminAll(): array
    {
        return db()->query(
            "SELECT u.*, COUNT(o.id) AS order_count
             FROM users u
             LEFT JOIN orders o ON o.user_id = u.id
             GROUP BY u.id
             ORDER BY u.id ASC"
        )->fetchAll();
    }

    public static function setAdmin(int $id, bool $isAdmin): void
    {
        $stmt = db()->prepare("UPDATE users SET is_admin = :a WHERE id = :id");
        $stmt->bindValue(":a", $isAdmin ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
