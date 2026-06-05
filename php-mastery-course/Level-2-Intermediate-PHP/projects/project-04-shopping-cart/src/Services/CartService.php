<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Core\Database;

class CartService
{
    private Cart $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function addItem(int $productId, int $quantity = 1): void
    {
        $this->cart->add($productId, $quantity);
    }

    public function updateItem(int $productId, int $quantity): void
    {
        $this->cart->update($productId, $quantity);
    }

    public function removeItem(int $productId): void
    {
        $this->cart->remove($productId);
    }

    public function checkout(int $userId): Order
    {
        $items = $this->cart->getItems();

        if (empty($items)) {
            throw new \RuntimeException('Cart is empty.');
        }

        $db = Database::getInstance();

        try {
            $db->getConnection()->beginTransaction();

            $total = $this->cart->total();

            $order = new Order();
            $order->user_id = $userId;
            $order->total = $total;
            $order->status = 'completed';
            $orderId = $order->save();
            $order->id = $orderId;

            foreach ($items as $item) {
                $product = $item['product'];

                if (!$product->isInStock($item['quantity'])) {
                    throw new \RuntimeException("Insufficient stock for: {$product->name}");
                }

                $orderItem = new OrderItem();
                $orderItem->order_id = $orderId;
                $orderItem->product_id = $product->id;
                $orderItem->product_name = $product->name;
                $orderItem->price = $product->price;
                $orderItem->quantity = $item['quantity'];
                $orderItem->subtotal = $item['subtotal'];
                $orderItem->save();

                $db->update('products', [
                    'stock' => $product->stock - $item['quantity'],
                ], 'id = ?', [$product->id]);
            }

            $db->getConnection()->commit();
            $this->cart->clear();

            return $order;

        } catch (\Exception $e) {
            $db->getConnection()->rollBack();
            throw $e;
        }
    }
}
