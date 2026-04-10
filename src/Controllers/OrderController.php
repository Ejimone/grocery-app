<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Services\PushNotificationService;

class OrderController
{
    private const DELIVERY_FEE = 4000;

    public function index(): void
    {
        $orders = Order::all();
        render('orders', [
            'title' => 'Orders',
            'orders' => $orders,
        ]);
    }

    public function checkout(): void
    {
        $items = array_values(Cart::items());
        if (!$items) {
            redirect('/cart');
        }

        $subtotal = Cart::subtotal();

        render('checkout', [
            'title' => 'Checkout',
            'items' => $items,
            'subtotal' => $subtotal,
            'deliveryFee' => self::DELIVERY_FEE,
            'total' => $subtotal + self::DELIVERY_FEE,
        ]);
    }

    public function store(): void
    {
        $items = array_values(Cart::items());

        if (!$items) {
            redirect('/cart');
        }

        $name = trim((string) ($_POST['customer_name'] ?? ''));
        $phone = trim((string) ($_POST['customer_phone'] ?? ''));
        $address = trim((string) ($_POST['customer_address'] ?? ''));

        if ($name === '' || $phone === '' || $address === '') {
            redirect('/checkout');
        }

        $subtotal = Cart::subtotal();
        $orderId = Order::create([
            'customer_name' => $name,
            'customer_phone' => $phone,
            'customer_address' => $address,
            'items' => array_map(function ($item) {
                return [
                    'product_id' => $item['product_id'],
                    'name' => $item['name'],
                    'price' => (int) $item['price'],
                    'quantity' => (int) $item['qty'],
                    'unit' => $item['unit'],
                ];
            }, $items),
            'total' => $subtotal + self::DELIVERY_FEE,
        ]);

        // Do not block checkout completion if push notification delivery fails.
        PushNotificationService::sendNewOrder([
            'order_id' => $orderId,
            'customer_name' => $name,
            'total' => $subtotal + self::DELIVERY_FEE,
        ]);

        Cart::clear();

        redirect('/order-confirmation?id=' . urlencode($orderId));
    }

    public function confirmation(): void
    {
        $orderId = (string) ($_GET['id'] ?? '');
        $order = Order::find($orderId);

        if (!$order) {
            http_response_code(404);
            echo 'Order not found';
            return;
        }

        render('orders', [
            'title' => 'Order Confirmation',
            'order' => $order,
            'isConfirmation' => true,
        ]);
    }
}
