<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get product ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    add_to_cart($product_id, $quantity);
    $_SESSION['success_message'] = 'Product added to cart!';
    header('Location: cart.php');
    exit();
}

// Fetch product details from CSV
$product = get_product_by_id($product_id);

if (!$product) {
    header('Location: products.php');
    exit();
}

$page_title = $product['name'];
include 'includes/header.php';
?>

<div class="container">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin: 3rem 0;">
        <!-- Product Image -->
        <div>
            <div class="product-image" style="height: 500px; border: 2px solid var(--gray-200); border-radius: 8px;">
                <?php if (!empty($product['image'])): ?>
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         style="width: 100%; height: 100%; object-fit: contain;"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Crect fill=\'%23e5e5e5\' width=\'400\' height=\'400\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23a3a3a3\' font-size=\'20\'%3ENo Image%3C/text%3E%3C/svg%3E'">
                <?php else: ?>
                    <svg width="200" height="200" viewBox="0 0 200 200" fill="none">
                        <rect width="200" height="200" fill="#e5e5e5"/>
                        <text x="100" y="100" text-anchor="middle" dy=".3em" fill="#a3a3a3" font-size="20">No Image</text>
                    </svg>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Product Details -->
        <div>
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($product['name']); ?></h1>
            <div class="product-price" style="font-size: 2rem; margin-bottom: 2rem;">
                <?php echo format_price($product['price']); ?>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem;">Description</h3>
                <p style="color: var(--gray-600); line-height: 1.8;">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </p>
            </div>
            
            <div style="margin-bottom: 2rem;">
                <p><strong>Stock:</strong> <?php echo $product['stock']; ?> units available</p>
            </div>
            
            <!-- Add to Cart Form -->
            <form method="POST" style="display: flex; gap: 1rem; align-items: center;">
                <div class="form-group" style="margin: 0;">
                    <label for="quantity">Quantity:</label>
                    <input type="number" 
                           id="quantity" 
                           name="quantity" 
                           class="form-control" 
                           value="1" 
                           min="1" 
                           max="<?php echo $product['stock']; ?>"
                           style="width: 100px;">
                </div>
                <button type="submit" name="add_to_cart" class="btn btn-black" style="margin-top: 1.5rem;">
                    Add to Cart
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
