<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get product ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart']) || isset($_POST['ship_order'])) {
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        add_to_cart($product_id, $quantity);
        
        if (isset($_POST['ship_order'])) {
            header('Location: checkout.php');
        } else {
            $_SESSION['success_message'] = 'Product added to cart!';
            header('Location: cart.php');
        }
        exit();
    }
}

// Handle Wishlist Toggle
if (isset($_GET['wishlist_toggle'])) {
    $w_id = intval($_GET['wishlist_toggle']);
    if (is_in_wishlist($w_id)) {
        remove_from_wishlist($w_id);
    } else {
        add_to_wishlist($w_id);
    }
    // Stay on page
    $current_url = strtok($_SERVER["REQUEST_URI"], '?') . '?id=' . $product_id;
    header("Location: $current_url");
    exit;
}

// Fetch product details
$product = get_product_by_id($product_id);

if (!$product) {
    header('Location: products.php');
    exit();
}

add_to_recently_viewed($product_id);

$page_title = $product['name'];
include 'includes/header.php';

// Related Products Logic
$all_products = get_all_products();
$related_products = [];
foreach($all_products as $p) {
    if($p['category'] == $product['category'] && $p['id'] != $product['id']) {
        $related_products[] = $p;
    }
}
// Fill if scarce
if(count($related_products) < 4) {
    foreach($all_products as $p) {
        if($p['id'] != $product['id'] && !in_array($p, $related_products)) {
            $related_products[] = $p;
        }
        if(count($related_products) >= 4) break;
    }
}
?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-5">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none" style="color: #6B7280;">Home</a></li>
            <li class="breadcrumb-item"><a href="products.php" class="text-decoration-none" style="color: #6B7280;">Shop</a></li>
            <li class="breadcrumb-item active fw-bold" aria-current="page" style="color: #D1D5DB;"><?php echo htmlspecialchars($product['category']); ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Product Image Section -->
        <div class="col-lg-6">
            <div class="card p-5 border-0 rounded-5 position-relative mb-4" style="background-color: #14161A;">
                <div class="position-absolute top-0 end-0 m-4 z-2">
                    <a href="?wishlist_toggle=<?php echo $product['id']; ?>" class="btn rounded-circle d-flex align-items-center justify-content-center p-0" 
                       style="width: 44px; height: 44px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(4px);">
                        <i class="bi <?php echo is_in_wishlist($product['id']) ? 'bi-heart-fill text-danger' : 'bi-heart text-white'; ?>" style="font-size: 1.25rem;"></i>
                    </a>
                </div>
                
                <div class="product-image-container d-flex align-items-center justify-content-center" style="height: 500px;">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         class="img-fluid"
                         style="max-height: 100%; object-fit: contain; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.5)); transition: transform 0.3s ease;">
                </div>
            </div>
            
            <!-- Gallery Thumbnails (Static for visual quality) -->
            <div class="row g-3">
                <div class="col-3">
                    <div class="p-3 rounded-4 cursor-pointer" style="background-color: #14161A; border: 1px solid #7C3AED;">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid" style="opacity: 1;">
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-3 rounded-4 cursor-pointer" style="background-color: #14161A; border: 1px solid transparent; opacity: 0.5;">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid">
                    </div>
                </div>
                <div class="col-3">
                    <div class="p-3 rounded-4 cursor-pointer" style="background-color: #14161A; border: 1px solid transparent; opacity: 0.5;">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Details Section -->
        <div class="col-lg-6">
            <div class="ps-lg-4">
                <!-- Meta -->
                <div class="mb-3 d-flex align-items-center gap-2">
                     <span class="badge rounded-pill px-3 py-2 text-uppercase fw-bold" 
                           style="background-color: rgba(124, 58, 237, 0.1); color: #A78BFA; letter-spacing: 1px;">
                         <?php echo htmlspecialchars($product['category']); ?>
                     </span>
                     <?php if($product['stock'] > 0): ?>
                         <span class="text-success fw-bold small ms-2"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> In Stock</span>
                     <?php else: ?>
                         <span class="text-danger fw-bold small ms-2"><i class="bi bi-circle-fill" style="font-size: 8px;"></i> Out of Stock</span>
                     <?php endif; ?>
                </div>

                <!-- Title -->
                <h1 class="display-5 fw-bold mb-3" style="color: #E5E7EB; line-height: 1.2;">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h1>
                
                <!-- Ratings -->
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="d-flex text-warning">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                    </div>
                    <span style="color: #E5E7EB; font-weight: 600;">4.5</span>
                    <span style="color: #9CA3AF;">(<?php echo rand(120, 500); ?> verified reviews)</span>
                </div>

                <!-- Price -->
                <div class="mb-5 d-flex align-items-end gap-3">
                    <h2 class="display-4 fw-bold mb-0" style="color: #7C3AED;">
                        ₹<?php echo number_format($product['price'], 0); ?>
                    </h2>
                    <?php if($product['price'] > 500): ?>
                        <span class="text-decoration-line-through mb-2 fs-5" style="color: #6B7280;">
                            ₹<?php echo number_format($product['price'] * 1.2, 0); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Description -->
                <div class="mb-5">
                    <h5 class="fw-bold mb-3" style="color: #E5E7EB;">Overview</h5>
                    <p class="fs-6 mb-0" style="color: #9CA3AF; line-height: 1.8; max-width: 90%;">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </p>
                </div>
                
                <!-- Info Cards -->
                <div class="row g-3 mb-5">
                    <div class="col-6">
                        <div class="p-3 rounded-4 d-flex align-items-center gap-3" style="background-color: #14161A; border: 1px solid #2D2D35;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.1); color: #10B981;">
                                <i class="bi bi-box-seam fs-5"></i>
                            </div>
                            <div>
                                <small class="d-block text-uppercase fw-bold" style="color: #6B7280; font-size: 0.7rem; letter-spacing: 0.5px;">Inventory</small>
                                <span class="fw-bold" style="color: #E5E7EB;"><?php echo $product['stock']; ?> Units</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-4 d-flex align-items-center gap-3" style="background-color: #14161A; border: 1px solid #2D2D35;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                                <i class="bi bi-shield-check fs-5"></i>
                            </div>
                            <div>
                                <small class="d-block text-uppercase fw-bold" style="color: #6B7280; font-size: 0.7rem; letter-spacing: 0.5px;">Warranty</small>
                                <span class="fw-bold" style="color: #E5E7EB;">1 Year</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase mb-2" style="color: #9CA3AF;">Quantity</label>
                        <div class="d-flex align-items-center bg-dark border border-secondary border-opacity-25 rounded-pill px-2" style="width: fit-content;">
                            <button type="button" class="btn text-white" onclick="this.nextElementSibling.stepDown()"><i class="bi bi-dash"></i></button>
                            <input type="number" name="quantity" class="form-control bg-transparent border-0 text-white text-center fw-bold" style="width: 60px;" value="1" min="1" max="<?php echo $product['stock']; ?>">
                            <button type="button" class="btn text-white" onclick="this.previousElementSibling.stepUp()"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>

                    <div class="d-flex gap-3 flex-column flex-sm-row">
                        <button type="submit" name="add_to_cart" class="btn btn-lg rounded-pill px-5 fw-bold flex-grow-1" 
                                style="border: 2px solid #7C3AED; color: #E5E7EB; background: transparent;">
                            Add to Cart
                        </button>
                        <button type="submit" name="ship_order" class="btn btn-lg rounded-pill px-5 fw-bold flex-grow-1" 
                                style="background-color: #7C3AED; color: white; border: none; box-shadow: 0 10px 20px rgba(124, 58, 237, 0.3);">
                            Buy Now
                        </button>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <span class="small" style="color: #6B7280;">
                            <i class="bi bi-lock-fill me-1"></i> Secure transaction powered by Univault
                        </span>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
<div class="container py-5" style="border-top: 1px solid #2D2D35;">
    <h3 class="fw-bold mb-4" style="color: #E5E7EB;">Related Products</h3>
    <div class="global-grid">
        <?php foreach(array_slice($related_products, 0, 4) as $related): ?>
            <!-- Global Product Card -->
            <a href="product-detail.php?id=<?php echo $related['id']; ?>" class="global-product-card">
                <button class="wishlist-btn <?php echo is_in_wishlist($related['id']) ? 'active' : ''; ?>" 
                        onclick="event.preventDefault(); window.location.href='?wishlist_toggle=<?php echo $related['id']; ?>'">
                    <i class="bi <?php echo is_in_wishlist($related['id']) ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                </button>
                <div class="img-wrapper">
                    <?php if ($related['stock'] == 0): ?><div class="sold-out-overlay">Sold Out</div><?php endif; ?>
                    <img src="<?php echo htmlspecialchars($related['image']); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>" loading="lazy">
                </div>
                <div class="card-content">
                    <span class="unit-pill">1 Unit</span>
                    <h3 class="product-title"><?php echo htmlspecialchars($related['name']); ?></h3>
                    <div class="product-category"><?php echo htmlspecialchars($related['category']); ?></div>
                    <div class="action-row">
                        <div class="price">
                            ₹<?php echo number_format($related['price'], 0); ?>
                        </div>
                        <button class="btn-add" onclick="event.preventDefault(); window.location.href='products.php?add_to_cart=<?php echo $related['id']; ?>'">ADD</button>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
