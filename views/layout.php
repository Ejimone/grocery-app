<?php
/** @var string $viewFile */
$title = $title ?? 'Grocery Store';
$activePath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isAdminRoute = str_starts_with($activePath, '/admin');
$isAdminDashboard = !empty($adminDashboard);
$latestOrderMs = (int) ($latestOrderMs ?? 0);
$vapidPublicKey = (string) ($_ENV['VAPID_PUBLIC_KEY'] ?? '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> | Grocery</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body
    data-admin-dashboard="<?= $isAdminDashboard ? '1' : '0' ?>"
    data-latest-order-ms="<?= $latestOrderMs ?>"
    data-vapid-public-key="<?= htmlspecialchars($vapidPublicKey) ?>"
>
    <header class="nav-wrap">
        <nav class="nav container">
            <a class="logo" href="/">grocery</a>
            <button class="hamburger" type="button" id="hamburger" aria-label="Toggle menu">☰</button>
            <div class="nav-center" id="navMenu">
                <?php if ($isAdminRoute): ?>
                    <a href="/admin" class="nav-link <?= $activePath === '/admin' ? 'active' : '' ?>">Dashboard</a>
                    <a href="/admin/products" class="nav-link <?= $activePath === '/admin/products' ? 'active' : '' ?>">Products</a>
                <?php else: ?>
                    <a href="/" class="nav-link <?= $activePath === '/' ? 'active' : '' ?>">Shop</a>
                    <a href="/orders" class="nav-link <?= $activePath === '/orders' ? 'active' : '' ?>">Orders</a>
                    <a href="/admin" class="nav-link <?= str_starts_with($activePath, '/admin') ? 'active' : '' ?>">Account</a>
                <?php endif; ?>
            </div>
            <?php if (!$isAdminRoute): ?>
                <a class="cart-pill" href="/cart" aria-label="Cart">
                    <span>Cart</span>
                    <span id="cart-count"><?= cart_count() ?></span>
                </a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container page-content">
        <?php require $viewFile; ?>
    </main>

    <footer class="footer container">
        <p>Fresh groceries, quietly delivered.</p>
    </footer>

    <script src="/js/app.js"></script>
</body>
</html>
