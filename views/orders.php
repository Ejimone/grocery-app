<?php if (!empty($isConfirmation) && !empty($order)): ?>
    <section class="card confirmation">
        <h1>Your order is placed!</h1>
        <p class="muted">Order ID: <?= htmlspecialchars($order['_id']) ?></p>
        <p>Estimated delivery: 35–45 minutes.</p>
        <a class="btn btn-secondary" href="/">Continue Shopping</a>
    </section>
<?php else: ?>
    <section>
        <h1 class="section-title">Recent Orders</h1>
        <div class="orders-list">
            <?php if (empty($orders)): ?>
                <p class="muted">No orders yet.</p>
            <?php else: ?>
                <?php foreach ($orders as $item): ?>
                    <article class="card order-card">
                        <div>
                            <h3><?= htmlspecialchars($item['customer_name']) ?></h3>
                            <p class="muted"><?= htmlspecialchars($item['customer_phone']) ?></p>
                        </div>
                        <div>
                            <p><?= format_price((int) $item['total']) ?></p>
                            <p class="muted"><?= htmlspecialchars($item['status']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>
