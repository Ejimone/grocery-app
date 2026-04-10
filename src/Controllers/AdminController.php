<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Connection;
use App\Models\Order;
use App\Models\Product;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class AdminController
{
    public function dashboard(): void
    {
        if (!$this->isAuthenticated()) {
            render('admin/dashboard', [
                'title' => 'Admin Login',
                'showLogin' => true,
            ]);
            return;
        }

        render('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'stats' => Order::stats(),
            'orders' => Order::all(),
            'showLogin' => false,
            'adminDashboard' => true,
            'latestOrderMs' => $this->latestOrderMs(),
        ]);
    }

    public function login(): void
    {
        $password = (string) ($_POST['password'] ?? '');
        $adminPassword = $_ENV['ADMIN_PASSWORD'] ?? '#1234jain';

        if ($password === $adminPassword) {
            $_SESSION['admin_auth'] = true;
            redirect('/admin');
        }

        render('admin/dashboard', [
            'title' => 'Admin Login',
            'showLogin' => true,
            'error' => 'Invalid password',
        ]);
    }

    public function logout(): void
    {
        unset($_SESSION['admin_auth']);
        redirect('/admin');
    }

    public function products(): void
    {
        if (!$this->isAuthenticated()) {
            redirect('/admin');
        }

        render('admin/products', [
            'title' => 'Admin Products',
            'products' => Product::all(),
        ]);
    }

    public function store(): void
    {
        if (!$this->isAuthenticated()) {
            redirect('/admin');
        }

        Product::create($_POST);
        redirect('/admin/products');
    }

    public function delete(): void
    {
        if (!$this->isAuthenticated()) {
            redirect('/admin');
        }

        $id = (string) ($_POST['id'] ?? '');
        if ($id !== '') {
            Product::delete($id);
        }

        redirect('/admin/products');
    }

    public function updateStatus(): void
    {
        if (!$this->isAuthenticated()) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false]);
            return;
        }

        $data = is_json_request() ? request_json() : $_POST;
        $orderId = (string) ($data['order_id'] ?? '');
        $status = (string) ($data['status'] ?? 'pending');

        if ($orderId !== '' && in_array($status, ['pending', 'confirmed', 'delivered'], true)) {
            Order::updateStatus($orderId, $status);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function subscribe(): void
    {
        if (!$this->isAuthenticated()) {
            $this->json(['success' => false], 403);
            return;
        }

        $data = request_json();
        $endpoint = trim((string) ($data['endpoint'] ?? ''));
        $keys = $data['keys'] ?? [];
        $p256dh = trim((string) ($keys['p256dh'] ?? ''));
        $auth = trim((string) ($keys['auth'] ?? ''));

        if ($endpoint === '' || $p256dh === '' || $auth === '') {
            $this->json(['success' => false, 'message' => 'Invalid subscription payload'], 422);
            return;
        }

        Connection::collection('push_subscriptions')->updateOne(
            ['endpoint' => $endpoint],
            ['$set' => [
                'endpoint' => $endpoint,
                'keys' => ['p256dh' => $p256dh, 'auth' => $auth],
                'updated_at' => new UTCDateTime((int) (microtime(true) * 1000)),
            ]],
            ['upsert' => true]
        );

        $this->json(['success' => true]);
    }

    public function pollOrders(): void
    {
        if (!$this->isAuthenticated()) {
            $this->json(['success' => false], 403);
            return;
        }

        $since = max(0, (int) ($_GET['since'] ?? 0));

        $cursor = Connection::collection('orders')->find([], ['sort' => ['_id' => 1], 'limit' => 120]);

        $orders = [];
        $latestMs = $since;

        foreach ($cursor as $order) {
            $row = (array) $order;
            $createdMs = $this->orderTimestampMs($row['_id'] ?? null);
            if ($createdMs <= $since) {
                continue;
            }
            $latestMs = max($latestMs, $createdMs);

            $orders[] = [
                'id' => (string) ($row['_id'] ?? ''),
                'customer_name' => (string) ($row['customer_name'] ?? 'Customer'),
                'total' => (int) ($row['total'] ?? 0),
                'created_at_ms' => $createdMs,
            ];
        }

        $this->json([
            'success' => true,
            'orders' => $orders,
            'latest_order_ms' => $latestMs,
        ]);
    }

    private function latestOrderMs(): int
    {
        $latest = Connection::collection('orders')->findOne([], ['sort' => ['_id' => -1]]);
        if (!$latest) {
            return 0;
        }

        $row = (array) $latest;
        return $this->orderTimestampMs($row['_id'] ?? null);
    }

    private function orderTimestampMs(mixed $value): int
    {
        if ($value instanceof ObjectId) {
            return $value->getTimestamp() * 1000;
        }

        return 0;
    }

    private function json(array $payload, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($payload);
    }

    private function isAuthenticated(): bool
    {
        return (bool) ($_SESSION['admin_auth'] ?? false);
    }
}
