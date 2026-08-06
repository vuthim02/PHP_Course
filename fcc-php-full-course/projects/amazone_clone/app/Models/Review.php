<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Review
{
    /** All reviews for a product, newest first, with the reviewer's name. */
    public static function forProduct(int $productId): array
    {
        $stmt = db()->prepare(
            "SELECT r.id, r.rating, r.comment, r.created_at, u.name AS user_name
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             WHERE r.product_id = :pid
             ORDER BY r.created_at DESC"
        );
        $stmt->bindValue(":pid", $productId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** A single user's review of a product (null if they haven't reviewed it). */
    public static function userReview(int $productId, int $userId): ?array
    {
        $stmt = db()->prepare(
            "SELECT id, rating, comment, created_at
             FROM reviews
             WHERE product_id = :pid AND user_id = :uid
             LIMIT 1"
        );
        $stmt->bindValue(":pid", $productId, PDO::PARAM_INT);
        $stmt->bindValue(":uid", $userId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function create(int $productId, int $userId, int $rating, string $comment): void
    {
        $stmt = db()->prepare(
            "INSERT INTO reviews (product_id, user_id, rating, comment)
             VALUES (:pid, :uid, :rating, :comment)"
        );
        $stmt->bindValue(":pid", $productId, PDO::PARAM_INT);
        $stmt->bindValue(":uid", $userId, PDO::PARAM_INT);
        $stmt->bindValue(":rating", $rating, PDO::PARAM_INT);
        $stmt->bindValue(":comment", $comment);
        $stmt->execute();

        self::recompute($productId);
    }

    public static function delete(int $reviewId, int $productId): void
    {
        $stmt = db()->prepare("DELETE FROM reviews WHERE id = :rid");
        $stmt->bindValue(":rid", $reviewId, PDO::PARAM_INT);
        $stmt->execute();

        self::recompute($productId);
    }

    /** Keep products.rating_avg / rating_count in sync with the reviews table. */
    public static function recompute(int $productId): void
    {
        $stmt = db()->prepare(
            "UPDATE products
             SET rating_avg = COALESCE(
                     (SELECT ROUND(AVG(rating), 1) FROM reviews WHERE product_id = :pid),
                     0
                 ),
                 rating_count = (SELECT COUNT(*) FROM reviews WHERE product_id = :pid)
             WHERE id = :pid"
        );
        $stmt->bindValue(":pid", $productId, PDO::PARAM_INT);
        $stmt->execute();
    }

    // ---- Admin helpers -------------------------------------------------------

    /** Every review with product + reviewer info, newest first. */
    public static function adminAll(): array
    {
        return db()->query(
            "SELECT r.*, u.name AS user_name, u.email AS user_email,
                    p.name AS product_name, p.slug AS product_slug
             FROM reviews r
             JOIN users u ON u.id = r.user_id
             JOIN products p ON p.id = r.product_id
             ORDER BY r.id DESC"
        )->fetchAll();
    }

    /** Deletes any review by id (moderation) and recomputes the product rating. */
    public static function deleteById(int $reviewId): void
    {
        $stmt = db()->prepare("SELECT product_id FROM reviews WHERE id = :id");
        $stmt->bindValue(":id", $reviewId, PDO::PARAM_INT);
        $stmt->execute();
        $productId = $stmt->fetchColumn();

        if ($productId !== false) {
            self::delete($reviewId, (int) $productId);
        }
    }
}
