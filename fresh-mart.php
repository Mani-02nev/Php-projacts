<?php
$page_title = 'Fresh Mart - Daily Grocery';
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Fetch grocery products (Simulated filtering for demo)
$all_products = get_all_products();
$grocery_products = array_filter($all_products, function($p) {
    return in_array($p['category'], ['Groceries', 'Fruits', 'Vegetables', 'Snacks', 'Beverages']);
});

// Category Data
$fresh_categories = [
    ['name' => 'Vegetables', 'icon' => 'bi-flower1', 'color' => '#10B981'],
    ['name' => 'Fruits', 'icon' => 'bi-apple', 'color' => '#F59E0B'],
    ['name' => 'Dairy & Eggs', 'icon' => 'bi-egg-fried', 'color' => '#3B82F6'],
    ['name' => 'Beverages', 'icon' => 'bi-cup-straw', 'color' => '#EC4899'],
    ['name' => 'Snacks', 'icon' => 'bi-cookie', 'color' => '#8B5CF6'],
    ['name' => 'Meat & Fish', 'icon' => 'bi-moisture', 'color' => '#EF4444'],
];

include 'includes/header.php';
?>

<style>
/* Fresh Mart Specific Overrides */
:root {
    --fresh-green: #10B981;
    --fresh-green-dark: #059669;
    --fresh-bg: #061109; /* Very dark green hint */
}

.fresh-mart-hero {
    background: radial-gradient(circle at top center, rgba(16, 185, 129, 0.15), #0B0B0E 70%);
    position: relative;
    overflow: hidden;
    padding: 4rem 0;
}

.cat-circle-card {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #14161A;
    border: 1px solid rgba(255,255,255,0.05);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    cursor: pointer;
    margin: 0 auto;
}

.cat-circle-card:hover {
    transform: translateY(-5px);
    border-color: var(--fresh-green);
    background: rgba(16, 185, 129, 0.1);
    box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
}

/* PREMIUM GROCERY CARD STYLE */
.grocery-card {
    background: #14161A;
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,0.05);
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    overflow: hidden;
    height: 100%;
    position: relative;
    cursor: pointer;
}

.grocery-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 24px rgba(16, 185, 129, 0.15);
    border-color: rgba(16, 185, 129, 0.4);
}

.grocery-card-img-wrapper {
    background: #fff;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.grocery-card-img {
    max-height: 80%;
    max-width: 80%;
    object-fit: contain;
    transition: transform 0.4s ease;
}

.grocery-card:hover .grocery-card-img {
    transform: scale(1.08);
}

.add-btn {
    background: transparent;
    color: var(--fresh-green);
    border: 1px solid var(--fresh-green);
    font-weight: 700;
    font-size: 0.85rem;
    padding: 6px 20px;
    border-radius: 50px;
    transition: all 0.2s ease;
    letter-spacing: 0.5px;
}

.add-btn:hover {
    background: var(--fresh-green);
    color: #fff;
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    transform: translateY(-1px);
}

/* Wishlist Icon on Card */
.card-wishlist-icon {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255,255,255,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9CA3AF;
    opacity: 0;
    transform: translateX(10px);
    transition: all 0.3s ease;
    z-index: 2;
}

.grocery-card:hover .card-wishlist-icon {
    opacity: 1;
    transform: translateX(0);
}

.card-wishlist-icon:hover {
    color: #EF4444;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.trust-badge-strip {
    border-top: 1px solid #2D2D35;
    border-bottom: 1px solid #2D2D35;
    background: rgba(255,255,255,0.02);
}
</style>

<!-- HERO SECTION -->
<div class="fresh-mart-hero text-center">
    <div class="container position-relative z-2">
        <span class="badge rounded-pill bg-success px-3 py-2 mb-3 bg-opacity-25 text-success border border-success">
            <i class="bi bi-shop me-2"></i> FRESH MART OPEN
        </span>
        <h1 class="display-4 fw-bold text-white mb-3">Daily Grocery, <span style="color: var(--fresh-green);">Delivered Fresh</span></h1>
        <p class="lead text-secondary mb-4 mx-auto" style="max-width: 600px;">
            Get farm-fresh vegetables, fruits, and daily essentials delivered to your doorstep in minutes.
        </p>
        
        <!-- Category Circles -->
        <div class="d-flex flex-wrap justify-content-center gap-4 mt-5">
            <?php foreach($fresh_categories as $cat): ?>
            <a href="products.php?category=Groceries" class="text-decoration-none">
                <div class="cat-circle-card">
                    <i class="bi <?php echo $cat['icon']; ?> fs-2 mb-2" style="color: <?php echo $cat['color']; ?>;"></i>
                    <span class="small fw-bold text-muted"><?php echo $cat['name']; ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- OFFERS STRIP -->
<div class="container my-5">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="p-4 rounded-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(45deg, #064E3B, #065F46);">
                <div>
                    <span class="badge bg-warning text-dark mb-2 fw-bold">LIMITED TIME</span>
                    <h3 class="fw-bold text-white mb-1">50% OFF</h3>
                    <p class="text-white opacity-75 mb-0">On Fresh Fruits & Veg</p>
                </div>
                <i class="bi bi-basket3-fill text-white opacity-25" style="font-size: 5rem;"></i>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-4 rounded-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(45deg, #1E3A8A, #1D4ED8);">
                <div>
                    <span class="badge bg-info text-dark mb-2 fw-bold">NEW ARRIVAL</span>
                    <h3 class="fw-bold text-white mb-1">Dairy Special</h3>
                    <p class="text-white opacity-75 mb-0">Milk, Curd & Cheese</p>
                </div>
                <i class="bi bi-egg-fried text-white opacity-25" style="font-size: 5rem;"></i>
            </div>
        </div>
    </div>
</div>

<!-- GROCERY GRID -->
<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4 px-2">
        <div>
            <h3 class="fw-bold text-white mb-1">Fresh Essentials</h3>
            <p class="text-secondary mb-0">Best prices on your daily needs</p>
        </div>
        <a href="products.php?category=Groceries" class="btn btn-outline-success btn-sm rounded-pill px-4 fw-bold">View All</a>
    </div>

    <div class="global-grid">
        <?php foreach(array_slice($grocery_products, 0, 15) as $product): ?>
            <!-- Global Product Card -->
            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="global-product-card">
                <!-- Wishlist Btn -->
                <button class="wishlist-btn <?php echo is_in_wishlist($product['id']) ? 'active' : ''; ?>" 
                        onclick="event.preventDefault(); window.location.href='?wishlist_toggle=<?php echo $product['id']; ?>'">
                    <i class="bi <?php echo is_in_wishlist($product['id']) ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
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
                        <button class="btn-add" onclick="event.preventDefault(); window.location.href='index.php?add_to_cart=<?php echo $product['id']; ?>'">
                            ADD
                        </button>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
        
        <?php if(empty($grocery_products)): ?>
            <div class="col-12 text-center py-5" style="grid-column: 1 / -1;">
                <p class="text-muted">No grocery items found. Please check back later!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- TRUST STRIP -->
<div class="trust-badge-strip py-4 mb-5">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-6 col-md-3 border-end border-secondary border-opacity-25">
                <i class="bi bi-clock-history fs-3 text-success mb-2 d-block"></i>
                <h6 class="text-white fw-bold mb-0">10 Min Delivery</h6>
            </div>
            <div class="col-6 col-md-3 border-end border-secondary border-opacity-25">
                <i class="bi bi-shield-check fs-3 text-success mb-2 d-block"></i>
                <h6 class="text-white fw-bold mb-0">Quality Check</h6>
            </div>
            <div class="col-6 col-md-3 border-end border-secondary border-opacity-25">
                <i class="bi bi-flower2 fs-3 text-success mb-2 d-block"></i>
                <h6 class="text-white fw-bold mb-0">Farm Fresh</h6>
            </div>
            <div class="col-6 col-md-3">
                <i class="bi bi-arrow-counterclockwise fs-3 text-success mb-2 d-block"></i>
                <h6 class="text-white fw-bold mb-0">Easy Returns</h6>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
