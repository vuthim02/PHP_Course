<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class ReturnRequest
{
    /** All return requests made by a user, newest first, with order info. */
    public static function forUser(int $userId): array
    {
        $stmt = db()->prepare(
            "SELECT r.id, r.status, r.reason, r.created_at, o.id AS order_id,
                    o.total AS order_total
             FROM returns r
             JOIN orders o ON o.id = r.order_id
             WHERE r.user_id = :u
             ORDER BY r.id DESC"
        );
        $stmt->execute([":u" => $userId]);
        return $stmt->fetchAll();
    }

    /** Creates a return request from the given order item ids (all must belong to the order). */
    public static function create(int $userId, int $orderId, array $orderItemIds, string $reason): int
    {
        $pdo = db();
        $pdo->beginTransaction();

        try {
            // Verify every item id belongs to this order before recording anything.
            $count = count($orderItemIds);
            if ($count > 0) {
                $placeholders = implode(",", array_fill(0, $count, "?"));
                $check = $pdo->prepare(
                    "SELECT COUNT(*) FROM order_items
                     WHERE order_id = :order AND id IN ($placeholders)"
                );
                $params = array_merge([":order" => $orderId], $orderItemIds);
                $check->execute($params);
                if ((int) $check->fetchColumn() !== $count) {
                    throw new \RuntimeException("One or more items do not belong to this order.");
                }
            }

            $stmt = $pdo->prepare(
                "INSERT INTO returns (order_id, user_id, reason, status)
                 VALUES (:order, :user, :reason, 'requested')"
            );
            $stmt->execute([
                ":order"  => $orderId,
                ":user"   => $userId,
                ":reason" => $reason,
            ]);
            $returnId = (int) $pdo->lastInsertId();

            $ins = $pdo->prepare(
                "INSERT INTO return_items (return_id, order_item_id, quantity)
                 SELECT :rid, id, quantity FROM order_items WHERE id = :oid"
            );
            foreach ($orderItemIds as $itemId) {
                $ins->execute([":rid" => $returnId, ":oid" => $itemId]);
            }

            $pdo->commit();
            return $returnId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /** A single return request plus the products being returned. */
    public static function withItems(int $returnId): ?array
    {
        $stmt = db()->prepare(
            "SELECT r.*, o.shipping_address, o.city, o.zip
             FROM returns r
             JOIN orders o ON o.id = r.order_id
             WHERE r.id = :id"
        );
        $stmt->execute([":id" => $returnId]);
        $request = $stmt->fetch();
        if ($request === false) {
            return null;
        }

        $items = db()->prepare(
            "SELECT ri.quantity AS return_qty, oi.price_at_purchase, p.name, p.slug, p.image_url
             FROM return_items ri
             JOIN order_items oi ON oi.id = ri.order_item_id
             JOIN products p ON p.id = oi.product_id
             WHERE ri.return_id = :id"
        );
        $items->execute([":id" => $returnId]);

        return ["request" => $request, "items" => $items->fetchAll()];
    }

    // ---- Admin helpers -------------------------------------------------------

    public static function countByStatus(string $status): int
    {
        $stmt = db()->prepare("SELECT COUNT(*) FROM returns WHERE status = :s");
        $stmt->execute([":s" => $status]);
        return (int) $stmt->fetchColumn();
    }

    /** All return requests with customer + order info, newest first. */
    public static function adminAll(?string $status = null): array
    {
        $sql = "SELECT r.*, u.name AS user_name, u.email AS user_email, o.total AS order_total
                FROM returns r
                JOIN orders o ON o.id = r.order_id
                LEFT JOIN users u ON u.id = r.user_id";
        $params = [];
        if ($status !== null && $status !== "") {
            $sql .= " WHERE r.status = :s";
            $params[":s"] = $status;
        }
        $sql .= " ORDER BY r.id DESC";
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $returnId, string $status): void
    {
        $stmt = db()->prepare("UPDATE returns SET status = :s WHERE id = :id");
        $stmt->bindValue(":s", $status);
        $stmt->bindValue(":id", $returnId, PDO::PARAM_INT);
        $stmt->execute();
    }

    /** Adds returned quantities back to stock. Call when items are received. */
    public static function restock(int $returnId): void
    {
        $stmt = db()->prepare(
            "UPDATE products
             SET stock = stock + (
                 SELECT SUM(ri.quantity)
                 FROM return_items ri
                 JOIN order_items oi ON oi.id = ri.order_item_id
                 WHERE ri.return_id = :rid AND oi.product_id = products.id
             )
             WHERE id IN (
                 SELECT oi.product_id
                 FROM return_items ri
                 JOIN order_items oi ON oi.id = ri.order_item_id
                 WHERE ri.return_id = :rid2
             )"
        );
        $stmt->bindValue(":rid", $returnId, PDO::PARAM_INT);
        $stmt->bindValue(":rid2", $returnId, PDO::PARAM_INT);
        $stmt->execute();
    }
}
