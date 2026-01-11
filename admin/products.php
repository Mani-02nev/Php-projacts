<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$page_title = 'Manage Products';
$success = '';
$error = '';

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = clean_input($_POST['name']);
    $description = clean_input($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image = clean_input($_POST['image']);
    
    if (add_product($name, $description, $price, $stock, $image)) {
        $success = 'Product added successfully!';
    } else {
        $error = 'Failed to add product.';
    }
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (delete_product($id)) {
        $success = 'Product deleted successfully!';
    } else {
        $error = 'Failed to delete product.';
    }
}

$products = get_all_products();

include '../includes/header.php';
?>

<div class="container">
    <h1 class="section-title">Manage Products</h1>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="form-container">
        <h2>Add New Product</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="price">Price (₹)</label>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input type="number" id="stock" name="stock" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label for="image">Image Filename (optional)</label>
                <input type="text" id="image" name="image" class="form-control" placeholder="e.g., product.jpg">
                <small style="color: var(--gray-600);">Place image in assets/images/ folder</small>
            </div>
            <button type="submit" name="add_product" class="btn btn-black" style="width: 100%;">Add Product</button>
        </form>
    </div>
    
    <h2 style="margin: 3rem 0 1rem;">All Products</h2>
    <table class="cart-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo format_price($product['price']); ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td><?php echo htmlspecialchars($product['image']); ?></td>
                    <td>
                        <a href="?delete=<?php echo $product['id']; ?>" 
                           class="btn" 
                           style="padding: 0.5rem 1rem; background: var(--black); color: var(--white);"
                           onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
