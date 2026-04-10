<section class="card form-card">
    <h2>Add Product</h2>
    <form action="/admin/products/add" method="POST" class="grid-form">
        <input class="input" name="name" placeholder="Name" required>
        <input class="input" name="description" placeholder="Description" required>
        <input class="input" type="number" name="price" placeholder="Price in paise" required>
        <input class="input" name="unit" placeholder="Unit (e.g. per kg)" required>
        <select class="input" name="category" required>
            <option value="fruits">Fruits</option>
            <option value="vegetables">Vegetables</option>
            <option value="dairy">Dairy</option>
            <option value="bakery">Bakery</option>
            <option value="beverages">Beverages</option>
            <option value="snacks">Snacks</option>
        </select>
        <input class="input" name="image_url" placeholder="Image URL (e.g. /images/products/apple.svg)" required>
        <input class="input" type="number" name="stock" placeholder="Stock" required>
        <label class="toggle-wrap"><input type="checkbox" name="featured" value="1"> Featured</label>
        <button class="btn btn-primary" type="submit">Save Product</button>
    </form>
</section>
