<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Connection;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Order
{
    public static function create(array $data): string
    {
        $result = Connection::collection('orders')->insertOne([
            'customer_name' => trim((string) $data['customer_name']),
            'customer_phone' => trim((string) $data['customer_phone']),
            'customer_address' => trim((string) $data['customer_address']),
            'items' => $data['items'],
            'total' => (int) $data['total'],
            'status' => 'pending',
            'created_at' => new UTCDateTime((int) (microtime(true) * 1000)),
        ]);

        return (string) $result->getInsertedId();
    }

    public static function find(string $id): ?array
    {
        try {
            $objectId = new ObjectId($id);
        } catch (\Throwable) {
            return null;
        }

        $item = Connection::collection('orders')->findOne([
            '_id' => $objectId,
        ]);

        if (!$item) {
            return null;
        }

        $order = (array) $item;
        $order['_id'] = (string) $order['_id'];
        return $order;
    }

    public static function all(): array
    {
        $cursor = Connection::collection('orders')->find([], [
            'sort' => ['_id' => -1],
        ]);

        return array_map(function ($order) {
            $item = (array) $order;
            $item['_id'] = (string) $item['_id'];
            return $item;
        }, $cursor->toArray());
    }

    public static function stats(): array
    {
        $orders = self::all();
        $revenue = 0;
        $pending = 0;

        foreach ($orders as $order) {
            $revenue += (int) ($order['total'] ?? 0);
            if (($order['status'] ?? '') === 'pending') {
                $pending++;
            }
        }

        return [
            'count' => count($orders),
            'revenue' => $revenue,
            'pending' => $pending,
        ];
    }

    public static function updateStatus(string $orderId, string $status): void
    {
        try {
            $objectId = new ObjectId($orderId);
        } catch (\Throwable) {
            return;
        }

        Connection::collection('orders')->updateOne(
            ['_id' => $objectId],
            ['$set' => ['status' => $status]]
        );
    }
}
