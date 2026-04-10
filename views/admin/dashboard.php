<?php if (!empty($showLogin)): ?>
    <section class="admin-login card">
        <h1>Admin Access</h1>
        <p class="muted">Enter admin password to continue.</p>
        <?php if (!empty($error)): ?>
            <p class="error-text"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="/admin/login">
            <input type="password" class="input" name="password" placeholder="Password" required>
            <button class="btn btn-primary full" type="submit">Login</button>
        </form>
    </section>
<?php else: ?>
    <section>
        <div class="admin-top">
            <h1 class="section-title">Dashboard</h1>
            <div class="admin-links">
                <button id="enable-push" class="btn btn-secondary" type="button">Enable Notifications</button>
                <a class="btn btn-secondary" href="/admin/products">Manage Products</a>
                <form action="/admin/logout" method="POST">
                    <button class="btn btn-secondary" type="submit">Logout</button>
                </form>
            </div>
        </div>

        <p class="muted">You will receive instant in-app alerts for new orders. Enable notifications to also get alerts when you are not actively using this tab.</p>
        <div id="admin-notification-stack" class="notification-stack" aria-live="polite"></div>

        <div class="stats-grid">
            <article class="card stat"><p class="muted">Total Orders</p><h3><?= (int) ($stats['count'] ?? 0) ?></h3></article>
            <article class="card stat"><p class="muted">Revenue</p><h3><?= format_price((int) ($stats['revenue'] ?? 0)) ?></h3></article>
            <article class="card stat"><p class="muted">Pending</p><h3><?= (int) ($stats['pending'] ?? 0) ?></h3></article>
        </div>

        <section class="card">
            <h2>Orders</h2>
            <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr><th>Name</th><th>Phone</th><th>Total</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                                <td><?= format_price((int) $order['total']) ?></td>
                                <td>
                                    <select class="status-select js-order-status" data-order-id="<?= htmlspecialchars($order['_id']) ?>">
                                        <?php foreach (['pending', 'confirmed', 'delivered'] as $status): ?>
                                            <option value="<?= $status ?>" <?= ($order['status'] ?? '') === $status ? 'selected' : '' ?>>
                                                <?= ucfirst($status) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </section>
<?php endif; ?>
