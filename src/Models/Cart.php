<?php

declare(strict_types=1);

namespace App\Models;

class Cart
{
    public static function items(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    public static function add(array $product, int $qty = 1): void
    {
        $qty = max(1, $qty);
        $productId = $product['_id'];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (!isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = [
                'product_id' => $productId,
                'name' => $product['name'],
                'price' => (int) $product['price'],
                'qty' => 0,
                'unit' => $product['unit'],
                'image_url' => $product['image_url'] ?? '/images/products/placeholder.svg',
            ];
        }

        $_SESSION['cart'][$productId]['qty'] += $qty;
    }

    public static function remove(string $productId, int $qty = 1): void
    {
        if (!isset($_SESSION['cart'][$productId])) {
            return;
        }

        $_SESSION['cart'][$productId]['qty'] -= max(1, $qty);

        if ($_SESSION['cart'][$productId]['qty'] <= 0) {
            unset($_SESSION['cart'][$productId]);
        }
    }

    public static function count(): int
    {
        $total = 0;
        foreach (self::items() as $item) {
            $total += (int) $item['qty'];
        }
        return $total;
    }

    public static function subtotal(): int
    {
        $sum = 0;
        foreach (self::items() as $item) {
            $sum += (int) $item['price'] * (int) $item['qty'];
        }
        return $sum;
    }

    public static function clear(): void
    {
        $_SESSION['cart'] = [];
    }
}
