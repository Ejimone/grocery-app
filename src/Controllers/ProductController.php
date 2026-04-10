<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;

class ProductController
{
    public function home(): void
    {
        $products = Product::all();
        render('home', [
            'title' => 'Shop',
            'products' => $products,
        ]);
    }

    public function show(): void
    {
        $productId = (string) ($_GET['id'] ?? '');
        $product = Product::find($productId);

        if (!$product) {
            http_response_code(404);
            echo 'Product not found';
            return;
        }

        render('product', [
            'title' => $product['name'],
            'product' => $product,
        ]);
    }
}
