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

// Add to recently viewed
add_to_recently_viewed($product_id);

$page_title = $product['name'];
include 'includes/header.php';
?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-secondary">Home</a></li>
            <li class="breadcrumb-item"><a href="products.php" class="text-decoration-none text-secondary">Products</a></li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Product Image -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white p-4">
                <div class="position-absolute top-0 end-0 m-4 z-2">
                    <a href="products.php?wishlist_toggle=<?php echo $product['id']; ?>" class="btn btn-white btn-sm rounded-circle shadow-sm p-3 fs-5 line-height-1">
                        <i class="bi <?php echo is_in_wishlist($product['id']) ? 'bi-heart-fill text-danger' : 'bi-heart'; ?>"></i>
                    </a>
                </div>
                <div class="product-image-container d-flex align-items-center justify-content-center zoom-container" style="height: 500px;">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         id="mainProductImage"
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         class="img-fluid rounded-4"
                         style="max-height: 100%; object-fit: contain;"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Crect fill=\'%23f8f9fa\' width=\'400\' height=\'400\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%236c757d\' font-size=\'20\'%3E📦 No Image%3C/text%3E%3C/svg%3E'">
                </div>
                <div class="text-center mt-3 text-secondary small">
                    <i class="bi bi-search"></i> Hover image to zoom
                </div>
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="product-details-content py-2">
                <div class="mb-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold small text-uppercase letter-spacing-1">Digital Product</span>
                </div>
                <h1 class="display-5 fw-bold text-dark mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="text-warning">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                    </div>
                    <span class="text-secondary fw-medium fs-5">4.5</span>
                    <span class="text-muted">(<?php echo rand(50, 500); ?> verified reviews)</span>
                </div>

                <div class="mb-4">
                    <h2 class="display-6 fw-bold text-primary"><?php echo format_price($product['price']); ?></h2>
                    <p class="text-success fw-bold small mb-0"><i class="bi bi-check-circle-fill me-1"></i> In Stock & Ready to Ship</p>
                </div>
                
                <div class="mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Description</h5>
                    <p class="text-secondary lh-lg fs-6">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </p>
                </div>
                
                <div class="row g-4 mb-5">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-4 border border-white shadow-sm d-flex align-items-center gap-3">
                            <i class="bi bi-box-seam fs-4 text-primary"></i>
                            <div>
                                <small class="text-secondary d-block">Inventory</small>
                                <span class="fw-bold text-dark"><?php echo $product['stock']; ?> units</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-4 border border-white shadow-sm d-flex align-items-center gap-3">
                            <i class="bi bi-shield-check fs-4 text-primary"></i>
                            <div>
                                <small class="text-secondary d-block">Warranty</small>
                                <span class="fw-bold text-dark">12 Months</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Add to Cart Form -->
                <form method="POST" class="row g-3 align-items-end">
                    <div class="col-sm-4">
                        <label for="quantity" class="form-label fw-bold small text-uppercase">Quantity</label>
                        <input type="number" 
                               id="quantity" 
                               name="quantity" 
                               class="form-control form-control-lg rounded-pill px-4 border shadow-sm" 
                               value="1" 
                               min="1" 
                               max="<?php echo $product['stock']; ?>">
                    </div>
                    <div class="col-sm-8 d-grid">
                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow border-0 transition-hover">
                            <i class="bi bi-cart-plus-fill me-2"></i> Add to Shopping Cart
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
