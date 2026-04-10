<?php

declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $staticFile = __DIR__ . $requestPath;

    if ($requestPath !== '/' && is_file($staticFile)) {
        return false;
    }
}

session_start();

use App\Controllers\AdminController;
use App\Controllers\CartController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Router;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/helpers.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$router = new Router();

$router->get('/', [ProductController::class, 'home']);
$router->get('/product', [ProductController::class, 'show']);
$router->get('/orders', [OrderController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->get('/cart', [CartController::class, 'index']);
$router->get('/checkout', [OrderController::class, 'checkout']);
$router->post('/checkout', [OrderController::class, 'store']);
$router->get('/order-confirmation', [OrderController::class, 'confirmation']);
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->post('/admin/login', [AdminController::class, 'login']);
$router->post('/admin/logout', [AdminController::class, 'logout']);
$router->get('/admin/products', [AdminController::class, 'products']);
$router->post('/admin/products/add', [AdminController::class, 'store']);
$router->post('/admin/products/delete', [AdminController::class, 'delete']);
$router->post('/admin/orders/status', [AdminController::class, 'updateStatus']);
$router->post('/admin/notifications/subscribe', [AdminController::class, 'subscribe']);
$router->get('/admin/orders/poll', [AdminController::class, 'pollOrders']);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $router->dispatch($method, $path);
} catch (Throwable $exception) {
    http_response_code(500);
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Something went wrong</title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body class="error-body">
        <main class="error-card">
            <h1>We could not load the store right now.</h1>
            <p>Please check your MongoDB connection settings and try again.</p>
        </main>
    </body>
    </html>
    <?php
}
