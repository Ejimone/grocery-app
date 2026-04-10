<section class="hero">
    <p class="eyebrow">Everyday essentials</p>
    <h1>Groceries that feel effortless.</h1>
    <p class="hero-sub">Shop premium produce and pantry favorites in a few quiet taps.</p>
    <input id="search-input" class="input search-input" type="text" placeholder="Search products...">
</section>

<section class="filters" id="category-filters">
    <button class="pill active" data-category="all" type="button">All</button>
    <button class="pill" data-category="fruits" type="button">Fruits</button>
    <button class="pill" data-category="vegetables" type="button">Vegetables</button>
    <button class="pill" data-category="dairy" type="button">Dairy</button>
    <button class="pill" data-category="bakery" type="button">Bakery</button>
    <button class="pill" data-category="beverages" type="button">Beverages</button>
    <button class="pill" data-category="snacks" type="button">Snacks</button>
</section>

<section>
    <h2 class="section-title">Shop</h2>
    <div class="product-grid" id="product-grid">
        <?php foreach ($products as $product): ?>
            <article
                class="card product-card"
                data-category="<?= htmlspecialchars($product['category']) ?>"
                data-name="<?= htmlspecialchars(strtolower($product['name'])) ?>"
                data-product-id="<?= htmlspecialchars($product['_id']) ?>"
                data-product-title="<?= htmlspecialchars($product['name']) ?>"
                data-product-unit="<?= htmlspecialchars($product['unit']) ?>"
                data-product-description="<?= htmlspecialchars($product['description']) ?>"
                data-product-image="<?= htmlspecialchars($product['image_url'] ?? '/images/products/placeholder.svg') ?>"
                data-product-price="<?= (int) $product['price'] ?>"
            >
                <a href="/product?id=<?= urlencode($product['_id']) ?>" class="product-link js-product-trigger">
                    <div class="image-box">
                        <img class="product-image" src="<?= htmlspecialchars($product['image_url'] ?? '/images/products/placeholder.svg') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="muted"><?= htmlspecialchars($product['unit']) ?></p>
                </a>
                <div class="product-row">
                    <strong><?= format_price((int) $product['price']) ?></strong>
                    <button
                        class="icon-btn js-add-cart"
                        type="button"
                        data-product-id="<?= htmlspecialchars($product['_id']) ?>"
                        aria-label="Add <?= htmlspecialchars($product['name']) ?>"
                    >
                        +
                    </button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="product-modal" id="product-modal" aria-hidden="true">
    <div class="product-modal-backdrop" data-modal-close></div>
    <article class="card product-modal-card" role="dialog" aria-modal="true" aria-labelledby="product-modal-title">
        <button class="modal-close" type="button" data-modal-close aria-label="Close">×</button>
        <div class="image-box large">
            <img id="modal-product-image" class="product-image" src="/images/products/placeholder.svg" alt="Product image">
        </div>
        <h2 id="product-modal-title">Product</h2>
        <p id="modal-product-unit" class="muted"></p>
        <p id="modal-product-description" class="product-description"></p>
        <p id="modal-product-price" class="product-price"></p>

        <div class="qty-wrap">
            <button class="qty-btn" type="button" id="modal-qty-minus">−</button>
            <span id="modal-qty">1</span>
            <button class="qty-btn" type="button" id="modal-qty-plus">+</button>
        </div>

        <button id="modal-add-cart" class="btn btn-primary full" type="button" data-product-id="">
            Add to Cart
        </button>
    </article>
</section>
