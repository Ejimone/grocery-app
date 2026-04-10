<section>
    <div class="admin-top">
        <h1 class="section-title">Products</h1>
        <a class="btn btn-secondary" href="/admin">Back to Dashboard</a>
    </div>

    <?php require __DIR__ . '/add-product.php'; ?>

    <section class="card">
        <h2>Product List</h2>
        <div class="table-wrap">
            <table class="admin-table">
                <thead>
                    <tr><th>Item</th><th>Category</th><th>Price</th><th>Stock</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <span class="admin-item">
                                    <img class="admin-thumb" src="<?= htmlspecialchars($product['image_url'] ?? '/images/products/placeholder.svg') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                    <span><?= htmlspecialchars($product['name']) ?></span>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($product['category']) ?></td>
                            <td><?= format_price((int) $product['price']) ?></td>
                            <td><?= (int) $product['stock'] ?></td>
                            <td>
                                <form action="/admin/products/delete" method="POST" onsubmit="return confirm('Delete this product?');">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($product['_id']) ?>">
                                    <button class="btn btn-secondary" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</section>
