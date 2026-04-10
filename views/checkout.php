<section>
    <h1 class="section-title">Checkout</h1>

    <div class="checkout-layout">
        <form action="/checkout" method="POST" class="card form-card">
            <label class="label" for="customer_name">Name</label>
            <input class="input" id="customer_name" name="customer_name" required>

            <label class="label" for="customer_phone">Phone</label>
            <input class="input" id="customer_phone" name="customer_phone" required>

            <label class="label" for="customer_address">Address</label>
            <textarea class="input" id="customer_address" name="customer_address" rows="4" required></textarea>

            <button class="btn btn-primary full" type="submit">Place Order</button>
        </form>

        <aside class="card summary">
            <h2>Order Summary</h2>
            <?php foreach ($items as $item): ?>
                <div class="summary-row">
                    <span><?= htmlspecialchars($item['name']) ?> × <?= (int) $item['qty'] ?></span>
                    <span><?= format_price((int) $item['price'] * (int) $item['qty']) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="summary-row"><span>Subtotal</span><span><?= format_price((int) $subtotal) ?></span></div>
            <div class="summary-row"><span>Delivery</span><span><?= format_price((int) $deliveryFee) ?></span></div>
            <div class="summary-row total"><span>Total</span><span><?= format_price((int) $total) ?></span></div>
        </aside>
    </div>
</section>
