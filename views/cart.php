<section>
    <h1 class="section-title">Your Cart</h1>

    <div class="cart-layout">
        <div class="card cart-items" id="cart-items">
            <?php if (!$items): ?>
                <p class="muted">Your cart is empty.</p>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <article class="cart-item" data-product-id="<?= htmlspecialchars($item['product_id']) ?>">
                        <div class="image-box small">
                            <img class="product-image" src="<?= htmlspecialchars($item['image_url'] ?? '/images/products/placeholder.svg') ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        </div>
                        <div>
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="muted"><?= htmlspecialchars($item['unit']) ?></p>
                        </div>
                        <div class="cart-actions">
                            <button class="qty-btn js-cart-remove" type="button" data-product-id="<?= htmlspecialchars($item['product_id']) ?>">−</button>
                            <span><?= (int) $item['qty'] ?></span>
                            <button class="qty-btn js-cart-add" type="button" data-product-id="<?= htmlspecialchars($item['product_id']) ?>">+</button>
                        </div>
                        <strong><?= format_price((int) $item['price'] * (int) $item['qty']) ?></strong>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <aside class="card summary" id="cart-summary">
            <h2>Summary</h2>
            <div class="summary-row"><span>Subtotal</span><span id="subtotal-value"><?= format_price((int) $subtotal) ?></span></div>
            <div class="summary-row"><span>Delivery</span><span id="delivery-value"><?= format_price((int) $deliveryFee) ?></span></div>
            <div class="summary-row total"><span>Total</span><span id="total-value"><?= format_price((int) $total) ?></span></div>
            <a class="btn btn-primary full" href="/checkout">Proceed to Checkout</a>
        </aside>
    </div>
</section>
