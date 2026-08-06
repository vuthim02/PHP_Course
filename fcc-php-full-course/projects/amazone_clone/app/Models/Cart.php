<?php

declare(strict_types=1);

namespace App\Models;

class Cart
{
    private static function token(): string
    {
        return session_id();
    }

    public static function add(int $productId, int $qty = 1): void
    {
        $stmt = db()->prepare(
            "INSERT INTO cart_items (session_token, product_id, quantity)
             VALUES (:t, :p, :q)
             ON CONFLICT (session_token, product_id)
             DO UPDATE SET quantity = quantity + excluded.quantity"
        );
        $stmt->execute([":t" => self::token(), ":p" => $productId, ":q" => $qty]);
    }

    public static function count(): int
    {
        $stmt = db()->prepare("SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE session_token = :t");
        $stmt->execute([":t" => self::token()]);
        return (int) $stmt->fetchColumn();
    }

    public static function items(): array
    {
        $stmt = db()->prepare(
            "SELECT ci.id, ci.product_id, ci.quantity, p.name, p.slug, p.price,
                    p.image_url, p.stock
             FROM cart_items ci
             JOIN products p ON p.id = ci.product_id
             WHERE ci.session_token = :t
             ORDER BY ci.added_at"
        );
        $stmt->execute([":t" => self::token()]);
        return $stmt->fetchAll();
    }

    public static function update(int $itemId, int $qty): void
    {
        if ($qty <= 0) {
            self::remove($itemId);
            return;
        }
        $stmt = db()->prepare(
            "UPDATE cart_items SET quantity = :q
             WHERE id = :id AND session_token = :t"
        );
        $stmt->execute([":q" => $qty, ":id" => $itemId, ":t" => self::token()]);
    }

    public static function remove(int $itemId): void
    {
        $stmt = db()->prepare("DELETE FROM cart_items WHERE id = :id AND session_token = :t");
        $stmt->execute([":id" => $itemId, ":t" => self::token()]);
    }

    public static function clear(): void
    {
        $stmt = db()->prepare("DELETE FROM cart_items WHERE session_token = :t");
        $stmt->execute([":t" => self::token()]);
    }

    /** Subtotal / shipping / tax / total for the current cart. */
    public static function summary(): array
    {
        $subtotal = array_sum(array_map(
            fn (array $i): float => (float) $i["price"] * (int) $i["quantity"],
            self::items()
        ));
        $subtotal = round($subtotal, 2);

        $freeOver = (float) config("site.free_shipping_over", 25.0);
        $shipping = ($subtotal >= $freeOver || $subtotal <= 0)
            ? 0.0
            : (float) config("site.flat_rate_shipping", 9.99);

        $tax  = round($subtotal * (float) config("site.tax_rate", 0.08), 2);
        $total = round($subtotal + $shipping + $tax, 2);

        return ["subtotal" => $subtotal, "shipping" => $shipping, "tax" => $tax, "total" => $total];
    }
}
