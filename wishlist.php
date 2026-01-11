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
            <h1 class="fw-bold mb-0 text-dark"><i class="bi bi-heart-fill text-danger me-2"></i> My Wishlist</h1>
            <p class="text-secondary mb-0">Products you've saved for later</p>
        </div>
    </div>

    <?php if (empty($wishlist_items)): ?>
        <div class="text-center py-5 bg-white rounded-5 shadow-sm border">
            <div class="display-1 text-muted mb-4 opacity-25">
                <i class="bi bi-heart"></i>
            </div>
            <h3 class="fw-bold">Your wishlist is empty</h3>
            <p class="text-secondary mb-4">Explore our products and save your favorites!</p>
            <a href="products.php" class="btn btn-primary rounded-pill px-5 fw-bold py-2 shadow">
                Start Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php foreach ($wishlist_items as $product): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-card transition-hover position-relative">
                        <!-- Remove Button -->
                        <div class="position-absolute top-0 end-0 m-3 z-2">
                            <a href="?remove=<?php echo $product['id']; ?>" class="btn btn-light btn-sm rounded-circle shadow-sm p-2 text-danger">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        </div>
                        
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center p-4" style="height: 200px;">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 class="img-fluid" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 style="max-height: 100%; object-fit: contain;">
                        </div>
                        
                        <div class="card-body p-4 d-flex flex-column">
                            <h6 class="card-title fw-bold mb-1 text-dark text-truncate"><?php echo htmlspecialchars($product['name']); ?></h6>
                            <p class="text-secondary small mb-3"><?php echo htmlspecialchars($product['category']); ?></p>
                            
                            <div class="mt-auto">
                                <h5 class="fw-bold text-primary mb-3">₹<?php echo number_format($product['price'], 0); ?></h5>
                                <div class="d-grid gap-2">
                                    <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary rounded-pill fw-bold btn-sm py-2">
                                        View Product
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Recently Viewed Section -->
    <?php $recently_viewed = get_recently_viewed_products(); ?>
    <?php if (!empty($recently_viewed)): ?>
        <div class="mt-5 pt-5 border-top">
            <h4 class="fw-bold mb-4">Recently Viewed</h4>
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3">
                <?php foreach (array_slice($recently_viewed, 0, 6) as $p): ?>
                    <div class="col">
                        <a href="product-detail.php?id=<?php echo $p['id']; ?>" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden transition-hover">
                                <div class="bg-light p-2 text-center" style="height: 120px;">
                                    <img src="<?php echo htmlspecialchars($p['image']); ?>" class="img-fluid" alt="" style="max-height: 100%; object-fit: contain;">
                                </div>
                                <div class="card-body p-2">
                                    <p class="small fw-bold text-dark mb-0 text-truncate"><?php echo htmlspecialchars($p['name']); ?></p>
                                    <small class="text-primary">₹<?php echo number_format($p['price'], 0); ?></small>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
