<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Database\Connection;
use Dotenv\Dotenv;
use MongoDB\BSON\UTCDateTime;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->safeLoad();

$collection = Connection::collection('products');
$collection->deleteMany([]);

$products = [
    ['name' => 'Royal Gala Apple', 'description' => 'Crisp and naturally sweet apples for snacking.', 'price' => 24900, 'unit' => 'per kg', 'category' => 'fruits', 'image_url' => '/images/products/apple.svg', 'stock' => 120, 'featured' => true],
    ['name' => 'Banana Bunch', 'description' => 'Soft, ripe bananas rich in potassium.', 'price' => 8900, 'unit' => 'per dozen', 'category' => 'fruits', 'image_url' => '/images/products/banana.svg', 'stock' => 160, 'featured' => false],
    ['name' => 'Orange Basket', 'description' => 'Juicy oranges packed with vitamin C.', 'price' => 17900, 'unit' => 'per kg', 'category' => 'fruits', 'image_url' => '/images/products/orange.svg', 'stock' => 100, 'featured' => true],
    ['name' => 'Baby Spinach', 'description' => 'Fresh green spinach leaves.', 'price' => 6900, 'unit' => '250g', 'category' => 'vegetables', 'image_url' => '/images/products/spinach.svg', 'stock' => 90, 'featured' => false],
    ['name' => 'Organic Carrot', 'description' => 'Crunchy carrots for salads and cooking.', 'price' => 9900, 'unit' => 'per kg', 'category' => 'vegetables', 'image_url' => '/images/products/carrot.svg', 'stock' => 110, 'featured' => true],
    ['name' => 'Cherry Tomato', 'description' => 'Sweet mini tomatoes.', 'price' => 13900, 'unit' => '500g', 'category' => 'vegetables', 'image_url' => '/images/products/tomato.svg', 'stock' => 80, 'featured' => false],
    ['name' => 'Whole Milk', 'description' => 'Creamy full-fat milk.', 'price' => 6200, 'unit' => '1 litre', 'category' => 'dairy', 'image_url' => '/images/products/milk.svg', 'stock' => 140, 'featured' => true],
    ['name' => 'Greek Yogurt', 'description' => 'Thick yogurt with high protein.', 'price' => 12500, 'unit' => '500g', 'category' => 'dairy', 'image_url' => '/images/products/yogurt.svg', 'stock' => 60, 'featured' => false],
    ['name' => 'Sourdough Loaf', 'description' => 'Slow-fermented artisan sourdough.', 'price' => 19900, 'unit' => '1 loaf', 'category' => 'bakery', 'image_url' => '/images/products/sourdough.svg', 'stock' => 45, 'featured' => true],
    ['name' => 'Butter Croissant', 'description' => 'Flaky all-butter croissant.', 'price' => 9500, 'unit' => '2 pieces', 'category' => 'bakery', 'image_url' => '/images/products/croissant.svg', 'stock' => 70, 'featured' => false],
    ['name' => 'Cold Brew Coffee', 'description' => 'Smooth ready-to-drink cold brew.', 'price' => 17500, 'unit' => '750 ml', 'category' => 'beverages', 'image_url' => '/images/products/coffee.svg', 'stock' => 50, 'featured' => true],
    ['name' => 'Sea Salt Chips', 'description' => 'Kettle-cooked crispy potato chips.', 'price' => 8500, 'unit' => '150g', 'category' => 'snacks', 'image_url' => '/images/products/chips.svg', 'stock' => 130, 'featured' => false],
];

$payload = array_map(static function (array $item): array {
    $item['created_at'] = new UTCDateTime((int) (microtime(true) * 1000));
    return $item;
}, $products);

$collection->insertMany($payload);

echo "Seeded products successfully.\n";
