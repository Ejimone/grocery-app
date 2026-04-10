<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Connection;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

class Product
{
    public static function all(): array
    {
        $cursor = Connection::collection('products')->find([], [
            'sort' => ['featured' => -1, 'created_at' => -1],
        ]);

        return array_map([self::class, 'normalize'], $cursor->toArray());
    }

    public static function find(string $id): ?array
    {
        if (!$id) {
            return null;
        }

        try {
            $objectId = new ObjectId($id);
        } catch (\Throwable) {
            return null;
        }

        $product = Connection::collection('products')->findOne([
            '_id' => $objectId,
        ]);

        if (!$product) {
            return null;
        }

        return self::normalize($product);
    }

    public static function create(array $data): void
    {
        $imageUrl = trim((string) ($data['image_url'] ?? ''));
        if ($imageUrl === '') {
            $imageUrl = '/images/products/placeholder.svg';
        }

        Connection::collection('products')->insertOne([
            'name' => trim((string) ($data['name'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'price' => (int) ($data['price'] ?? 0),
            'unit' => trim((string) ($data['unit'] ?? '')),
            'category' => trim((string) ($data['category'] ?? '')),
            'image_url' => $imageUrl,
            'stock' => (int) ($data['stock'] ?? 0),
            'featured' => isset($data['featured']) && (string) $data['featured'] === '1',
            'created_at' => new UTCDateTime((int) (microtime(true) * 1000)),
        ]);
    }

    public static function delete(string $id): void
    {
        try {
            $objectId = new ObjectId($id);
        } catch (\Throwable) {
            return;
        }

        Connection::collection('products')->deleteOne([
            '_id' => $objectId,
        ]);
    }

    private static function normalize(object|array $product): array
    {
        $item = (array) $product;
        $item['_id'] = (string) $item['_id'];
        if (empty($item['image_url'])) {
            $item['image_url'] = '/images/products/placeholder.svg';
        }
        return $item;
    }
}
