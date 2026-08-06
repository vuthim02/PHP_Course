<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Order
{
    /** Creates a pending order + its line items, decrements stock, then clears the cart. */
    public static function create(array $address, float $total): int
    {
        $pdo = db();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO orders (user_id, total, status, shipping_address, city, zip)
                 VALUES (:u, :total, 'pending', :addr, :city, :zip)"
            );
            $stmt->execute([
                ":u"     => $_SESSION["user_id"] ?? null,
                ":total" => $total,
                ":addr"  => $address["shipping_address"],
                ":city"  => $address["city"],
                ":zip"   => $address["zip"],
            ]);
            $orderId = (int) $pdo->lastInsertId();

            $ins = $pdo->prepare(
                "INSERT INTO order_items (order_id, product_id, price_at_purchase, quantity)
                 VALUES (:o, :p, :price, :q)"
            );
            $stock = $pdo->prepare(
                "UPDATE products SET stock = stock - :q WHERE id = :id AND stock >= :q"
            );

            foreach (Cart::items() as $item) {
                $ins->execute([
                    ":o"     => $orderId,
                    ":p"     => $item["product_id"],
                    ":price" => $item["price"],
                    ":q"     => $item["quantity"],
                ]);
                $stock->execute([":q" => $item["quantity"], ":id" => $item["product_id"]]);
            }

            Cart::clear();
            $pdo->commit();

            return $orderId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /** All orders placed by a given user, newest first. */
    public static function ordersForUser(int $userId): array
    {
        $stmt = db()->prepare("SELECT * FROM orders WHERE user_id = :u ORDER BY id DESC");
        $stmt->execute([":u" => $userId]);
        return $stmt->fetchAll();
    }

    public static function markPaid(int $orderId): void
    {
        $stmt = db()->prepare("UPDATE orders SET status = 'paid' WHERE id = :id");
        $stmt->execute([":id" => $orderId]);
    }

    public static function withItems(int $orderId): ?array
    {
        $stmt = db()->prepare("SELECT * FROM orders WHERE id = :id");
        $stmt->execute([":id" => $orderId]);
        $order = $stmt->fetch();

        if ($order === false) {
            return null;
        }

        $items = db()->prepare(
            "SELECT oi.id AS item_id, oi.price_at_purchase, oi.quantity, p.name, p.slug, p.image_url
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = :id"
        );
        $items->execute([":id" => $orderId]);

        return ["order" => $order, "items" => $items->fetchAll()];
    }

    // ---- Admin helpers -------------------------------------------------------

    public static function countAll(): int
    {
        return (int) db()->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    }

    public static function countByStatus(string $status): int
    {
        $stmt = db()->prepare("SELECT COUNT(*) FROM orders WHERE status = :s");
        $stmt->execute([":s" => $status]);
        return (int) $stmt->fetchColumn();
    }

    /** Total revenue from orders that were actually paid for. */
    public static function revenue(): float
    {
        $stmt = db()->prepare(
            "SELECT COALESCE(SUM(total), 0) FROM orders WHERE status IN ('paid', 'shipped', 'delivered')"
        );
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    /** Every order with the customer's name + email, newest first. */
    public static function adminAll(?string $status = null, string $q = ""): array
    {
        $sql = "SELECT o.*, u.name AS user_name, u.email AS user_email
                FROM orders o
                LEFT JOIN users u ON u.id = o.user_id";
        $where = [];
        $params = [];
        if ($status !== null && $status !== "") {
            $where[] = "o.status = :s";
            $params[":s"] = $status;
        }
        if ($q !== "") {
            $where[] = "(o.id = :id OR u.name LIKE :q OR u.email LIKE :q2)";
            $params[":id"] = (int) $q;
            $params[":q"]  = "%" . $q . "%";
            $params[":q2"] = "%" . $q . "%";
        }
        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY o.id DESC";
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function updateStatus(int $orderId, string $status): void
    {
        $stmt = db()->prepare("UPDATE orders SET status = :s WHERE id = :id");
        $stmt->execute([":s" => $status, ":id" => $orderId]);
    }

    /** Cancels an order and puts its stock back into inventory. */
    public static function cancel(int $orderId): bool
    {
        $stmt = db()->prepare("SELECT status FROM orders WHERE id = :id");
        $stmt->execute([":id" => $orderId]);
        $status = $stmt->fetchColumn();
        if ($status === false || !in_array($status, ["pending", "paid"], true)) {
            return false;
        }

        $pdo = db();
        $pdo->beginTransaction();
        try {
            $restock = $pdo->prepare(
                "UPDATE products
                 SET stock = stock + (
                     SELECT oi.quantity
                     FROM order_items oi
                     WHERE oi.order_id = :oid AND oi.product_id = products.id
                 )
                 WHERE id IN (SELECT product_id FROM order_items WHERE order_id = :oid2)"
            );
            $restock->execute([":oid" => $orderId, ":oid2" => $orderId]);
            self::updateStatus($orderId, "cancelled");
            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /** Revenue per day for the last $days days (zero-filled, for the admin chart). */
    public static function salesByDay(int $days = 14): array
    {
        $stmt = db()->prepare(
            "SELECT date(created_at) AS day, SUM(total) AS revenue
             FROM orders
             WHERE status IN ('paid', 'shipped', 'delivered')
               AND date(created_at) >= date('now', :offset)
             GROUP BY day"
        );
        $stmt->bindValue(":offset", "-" . ($days - 1) . " days");
        $stmt->execute();

        $byDay = [];
        foreach ($stmt->fetchAll() as $r) {
            $byDay[$r["day"]] = (float) $r["revenue"];
        }

        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = date("Y-m-d", strtotime("-" . $i . " days"));
            $out[] = ["day" => $day, "revenue" => $byDay[$day] ?? 0.0];
        }
        return $out;
    }

    /** Best-selling products by quantity sold. */
    public static function topSellers(int $limit = 5): array
    {
        $stmt = db()->prepare(
            "SELECT p.id, p.name, p.slug, p.price, p.deal_price, p.image_url,
                    SUM(oi.quantity) AS sold
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             GROUP BY p.id
             ORDER BY sold DESC
             LIMIT :limit"
        );
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
