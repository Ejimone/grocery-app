<section class="product-detail card">
    <div class="image-box large">
        <img class="product-image" src="<?= htmlspecialchars($product['image_url'] ?? '/images/products/placeholder.svg') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
    </div>
    <h1><?= htmlspecialchars($product['name']) ?></h1>
    <p class="muted"><?= htmlspecialchars($product['unit']) ?></p>
    <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
    <p class="product-price"><?= format_price((int) $product['price']) ?></p>

    <div class="qty-wrap" data-product-id="<?= htmlspecialchars($product['_id']) ?>">
        <button class="qty-btn" type="button" data-qty-action="minus">−</button>
        <span id="detail-qty">1</span>
        <button class="qty-btn" type="button" data-qty-action="plus">+</button>
    </div>

    <button class="btn btn-primary full js-add-cart-detail" type="button" data-product-id="<?= htmlspecialchars($product['_id']) ?>">
        Add to Cart
    </button>
</section>
