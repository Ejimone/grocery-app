<?php

declare(strict_types=1);

use App\Models\Cart;

function render(string $view, array $data = []): void
{
    $viewPath = __DIR__ . '/../views/' . $view . '.php';

    if (!file_exists($viewPath)) {
        http_response_code(500);
        echo 'View not found';
        return;
    }

    extract($data, EXTR_SKIP);
    $viewFile = $viewPath;
    require __DIR__ . '/../views/layout.php';
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function format_price(int|float $paise): string
{
    $rupees = $paise / 100;
    return '₹' . number_format($rupees, 2);
}

function cart_count(): int
{
    return Cart::count();
}

function is_json_request(): bool
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    return str_contains($contentType, 'application/json');
}

function request_json(): array
{
    $body = file_get_contents('php://input') ?: '{}';
    $parsed = json_decode($body, true);
    return is_array($parsed) ? $parsed : [];
}
