<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'My Wishlist';

// Handle Wishlist Toggle
if (isset($_GET['remove'])) {
    $p_id = intval($_GET['remove']);
    remove_from_wishlist($p_id);
    header('Location: wishlist.php');
    exit();
}

$wishlist_items = [];
if (isset($_SESSION['wishlist']) && !empty($_SESSION['wishlist'])) {
    $all = get_all_products();
    foreach ($_SESSION['wishlist'] as $id) {
        foreach ($all as $p) {
            if ($p['id'] == $id) {
                $wishlist_items[] = $p;
                break;
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="fw-bold mb-1" style="color: #374151;"><i class="bi bi-heart-fill text-danger me-2"></i> My Wishlist</h1>
            <p class="mb-0 fs-5" style="color: #6B7280;">Products you've saved for later</p>
        </div>
    </div>

    <?php if (empty($wishlist_items)): ?>
        <!-- EMPTY STATE (White Card as requested) -->
        <div class="text-center py-5 rounded-5 shadow-sm border" style="background-color: #FFFFFF; border-color: #374151;">
            <div class="display-1 mb-4" style="color: #6B7280; opacity: 0.3;">
                <i class="bi bi-heart"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2">Your wishlist is empty</h3>
            <p class="text-secondary mb-4 fs-5">Explore our products and save your favorites!</p>
            <a href="products.php" class="btn rounded-pill px-5 fw-bold py-2 shadow" style="background-color: #7C3AED; color: #FFFFFF; border: none;">
                Start Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="global-grid">
            <?php foreach ($wishlist_items as $product): ?>
                <!-- Global Product Card -->
                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="global-product-card">
                    <!-- Wishlist Btn (Acts as Remove) -->
                    <button type="button" class="wishlist-btn active" data-id="<?php echo $product['id']; ?>">
                        <i class="bi bi-heart-fill"></i>
                    </button>
                    
                    <!-- Image Area -->
                    <div class="img-wrapper">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                    </div>
                    
                    <!-- Content -->
                    <div class="card-content">
                        <span class="unit-pill">1 Unit</span>
                        <h3 class="product-title" title="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h3>
                        <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                        
                        <div class="action-row">
                            <div class="price">
                                ₹<?php echo number_format($product['price'], 0); ?>
                            </div>
                            <button class="btn-add" onclick="event.preventDefault(); window.location.href='products.php?add_to_cart=<?php echo $product['id']; ?>'">
                                ADD
                            </button>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Recently Viewed Section -->
    <?php $recently_viewed = get_recently_viewed_products(); ?>
    <?php if (!empty($recently_viewed)): ?>
        <div class="mt-5 pt-5" style="border-top: 1px solid #E5E7EB;">
            <h4 class="fw-bold mb-4" style="color: #374151;">Recently Viewed</h4>
            <div class="global-grid">
                <?php foreach (array_slice($recently_viewed, 0, 6) as $product): ?>
                    <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="global-product-card">
                        <button type="button" class="wishlist-btn <?php echo is_in_wishlist($product['id']) ? 'active' : ''; ?>" 
                                data-id="<?php echo $product['id']; ?>">
                            <i class="bi <?php echo is_in_wishlist($product['id']) ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                        </button>
                        <div class="img-wrapper">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                        </div>
                        <div class="card-content">
                            <span class="unit-pill">1 Unit</span>
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-category"><?php echo htmlspecialchars($product['category']); ?></div>
                            <div class="action-row">
                                <div class="price">₹<?php echo number_format($product['price'], 0); ?></div>
                                <button class="btn-add" onclick="event.preventDefault(); window.location.href='products.php?add_to_cart=<?php echo $product['id']; ?>'">ADD</button>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
