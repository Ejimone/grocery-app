<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Product;

class CartController
{
    private const DELIVERY_FEE = 4000;

    public function index(): void
    {
        render('cart', [
            'title' => 'Cart',
            'items' => array_values(Cart::items()),
            'subtotal' => Cart::subtotal(),
            'deliveryFee' => self::DELIVERY_FEE,
            'total' => Cart::subtotal() + self::DELIVERY_FEE,
        ]);
    }

    public function add(): void
    {
        $data = is_json_request() ? request_json() : $_POST;
        $productId = (string) ($data['product_id'] ?? '');
        $qty = (int) ($data['qty'] ?? 1);

        $product = Product::find($productId);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Product not found'], 404);
            return;
        }

        Cart::add($product, $qty);

        $subtotal = Cart::subtotal();
        $total = $subtotal + self::DELIVERY_FEE;

        $this->json([
            'success' => true,
            'cart_count' => Cart::count(),
            'items' => array_values(Cart::items()),
            'subtotal' => $subtotal,
            'delivery_fee' => self::DELIVERY_FEE,
            'total' => $total,
            'subtotal_formatted' => format_price($subtotal),
            'delivery_fee_formatted' => format_price(self::DELIVERY_FEE),
            'total_formatted' => format_price($total),
        ]);
    }

    public function remove(): void
    {
        $data = is_json_request() ? request_json() : $_POST;
        $productId = (string) ($data['product_id'] ?? '');
        $qty = (int) ($data['qty'] ?? 1);

        Cart::remove($productId, $qty);

        $subtotal = Cart::subtotal();
        $total = $subtotal + self::DELIVERY_FEE;

        $this->json([
            'success' => true,
            'cart_count' => Cart::count(),
            'items' => array_values(Cart::items()),
            'subtotal' => $subtotal,
            'delivery_fee' => self::DELIVERY_FEE,
            'total' => $total,
            'subtotal_formatted' => format_price($subtotal),
            'delivery_fee_formatted' => format_price(self::DELIVERY_FEE),
            'total_formatted' => format_price($total),
        ]);
    }

    private function json(array $payload, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($payload);
    }
}
