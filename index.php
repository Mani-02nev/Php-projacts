<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Home';

// Fetch all products
$all_products = get_all_products();

// Group products by category
$categories_data = [];
foreach ($all_products as $product) {
    if ($product['stock'] > 0) { // Only show in-stock for homepage
        $categories_data[$product['category']][] = $product;
    }
}

// Custom Category Ordering
$priority_order = [
    'Audio & Speakers',
    'Sports & Fitness',
    'Men\'s Shirts',
    'Footwear',
    'Travel & Accessories'
];

// Handle Electronics specifically to move it to the last position
$electronics_data = [];
if (isset($categories_data['Electronics'])) {
    $electronics_data['Electronics'] = $categories_data['Electronics'];
    unset($categories_data['Electronics']);
}

// Reorder categories based on priority
$ordered_categories = [];
foreach ($priority_order as $cat) {
    if (isset($categories_data[$cat])) {
        $ordered_categories[$cat] = $categories_data[$cat];
        unset($categories_data[$cat]);
    }
}

// Compose final array: Priority + Remaining + Electronics (Last)
$categories_data = $ordered_categories + $categories_data + $electronics_data;

// Wishlist Logic
if (isset($_GET['wishlist_toggle'])) {
    $p_id = intval($_GET['wishlist_toggle']);
    if (is_in_wishlist($p_id)) {
        remove_from_wishlist($p_id);
    } else {
        add_to_wishlist($p_id);
    }
    // Maintain scroll position
    $redirect_url = strtok($_SERVER["REQUEST_URI"], '?') . '?' . http_build_query(array_diff_key($_GET, ['wishlist_toggle' => '']));
    header("Location: $redirect_url");
    exit;
}

include 'includes/header.php';
?>

<style>
/* GLOBAL HERO 3D STYLES */
.hero-3d-wrapper {
    position: relative;
    height: 70vh;
    min-height: 500px;
    max-height: 700px;
    background: radial-gradient(circle at center, #1A1D23 0%, #0B0B0E 100%);
    overflow: hidden;
    perspective: 1000px;
}

.hero-content-layer {
    z-index: 10;
    position: relative;
}

.hero-floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.float-item {
    position: absolute;
    opacity: 0.1;
    filter: blur(2px);
    animation: floatAnimation 20s infinite ease-in-out;
}

@keyframes floatAnimation {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-40px) rotate(5deg); }
}

/* PREMIUM PRODUCT CARD ANIMATIONS */
.premium-product-card {
    background-color: #14161A;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 16px;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    height: 100%;
    position: relative;
    overflow: hidden;
}

.premium-product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(124, 58, 237, 0.3);
    background-color: #1A1D23;
}

.product-img-zoom {
    transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));
}

.premium-product-card:hover .product-img-zoom {
    transform: scale(1.08) translateY(-5px);
}

.wishlist-btn-hover {
    opacity: 0;
    transform: translateX(10px);
    transition: all 0.3s ease;
}

.premium-product-card:hover .wishlist-btn-hover {
    opacity: 1;
    transform: translateX(0);
}

/* SECTION TYPOGRAPHY */
.section-title {
    color: #F3F4F6;
    font-weight: 800;
    letter-spacing: -0.5px;
    font-size: 1.75rem;
}

.section-subtitle {
    color: #9CA3AF;
    font-size: 0.95rem;
}

/* NAVIGATION BUTTONS */
.nav-btn-custom {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #2D2D35;
    border: 1px solid #374151;
    color: #E5E7EB;
    transition: all 0.2s;
}

.nav-btn-custom:hover {
    background: #7C3AED;
    border-color: #7C3AED;
    color: white;
    transform: scale(1.1);
}

/* SCROLLBAR HIDE */
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<!-- 3D HERO SECTION -->
<div class="hero-3d-wrapper d-flex align-items-center justify-content-center">
    <!-- Floating Background Elements -->
    <div class="hero-floating-elements">
        <div class="float-item" style="top: 20%; left: 10%; width: 100px; height: 100px; background: radial-gradient(circle, #7C3AED, transparent); animation-delay: 0s;"></div>
        <div class="float-item" style="top: 60%; right: 15%; width: 150px; height: 150px; background: radial-gradient(circle, #10B981, transparent); animation-delay: -5s;"></div>
        <div class="float-item" style="top: 10%; right: 25%; width: 80px; height: 80px; background: radial-gradient(circle, #F59E0B, transparent); animation-delay: -10s;"></div>
    </div>

    <!-- Main Content -->
    <div class="container hero-content-layer text-center position-relative">
        <span class="badge rounded-pill px-3 py-2 mb-4" 
              style="background: rgba(124, 58, 237, 0.15); color: #A78BFA; border: 1px solid rgba(124, 58, 237, 0.3); letter-spacing: 1px;">
            NEW COLLECTION 2026
        </span>
        <h1 class="display-3 fw-bold mb-4" style="color: #E5E7EB; letter-spacing: -2px; text-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            Future of <span style="color: #7C3AED;">Shopping</span>
        </h1>
        <p class="lead mb-5 mx-auto" style="color: #9CA3AF; max-width: 600px; font-weight: 500;">
            Experience the next generation of e-commerce. Premium products, secure transactions, and lightning-fast delivery.
        </p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="products.php" class="btn btn-lg rounded-pill px-5 fw-bold" 
               style="background-color: #7C3AED; color: white; border: none; box-shadow: 0 10px 20px rgba(124, 58, 237, 0.3);">
                Shop Now
            </a>
            <a href="#categories" class="btn btn-lg btn-outline-light rounded-pill px-4 fw-bold" 
               style="border-color: #374151; color: #E5E7EB;">
                Explore Categories
            </a>
        </div>
    </div>
    
    <!-- Bottom Fade -->
    <div class="position-absolute bottom-0 w-100" style="height: 100px; background: linear-gradient(to top, #0B0B0E, transparent);"></div>
</div>

<!-- CATEGORY SECTIONS -->
<div id="categories" class="container-fluid px-0 py-5" style="background-color: #0B0B0E;">
    
    <?php foreach ($categories_data as $category_name => $products): ?>
        <?php if (count($products) > 0): // Only show categories with items ?>
        <section class="mb-5 px-lg-5 px-3">
            <!-- Section Header -->
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3" style="border-color: #1F2937 !important;">
                <div>
                    <h2 class="section-title mb-1"><?php echo htmlspecialchars($category_name); ?></h2>
                    <p class="section-subtitle mb-0">Top picks for you</p>
                </div>
                
                <!-- Desktop Nav -->
                <div class="d-none d-md-flex gap-2">
                    <button class="nav-btn-custom d-flex align-items-center justify-content-center" onclick="scrollSlider('slider-<?php echo md5($category_name); ?>', -300)">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="nav-btn-custom d-flex align-items-center justify-content-center" onclick="scrollSlider('slider-<?php echo md5($category_name); ?>', 300)">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                    <a href="products.php?category=<?php echo urlencode($category_name); ?>" class="btn btn-outline-secondary rounded-pill px-4 ms-2 fw-bold" 
                       style="border-color: #374151; color: #9CA3AF;">
                        View All
                    </a>
                </div>
            </div>

            <!-- Horizontal Slider -->
            <div class="position-relative">
                <div id="slider-<?php echo md5($category_name); ?>" 
                     class="d-flex overflow-auto gap-4 py-4 px-1 scrollbar-hide" 
                     style="scroll-behavior: smooth; snap-type: x mandatory;">
                    
                    <?php foreach ($products as $product): ?>
                        <div style="min-width: 280px; max-width: 280px; scroll-snap-align: start;">
                            <!-- Global Product Card Component -->
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="global-product-card">
                                <!-- Wishlist Btn -->
                                <button class="wishlist-btn <?php echo is_in_wishlist($product['id']) ? 'active' : ''; ?>" 
                                        onclick="event.preventDefault(); window.location.href='?wishlist_toggle=<?php echo $product['id']; ?>'">
                                    <i class="bi <?php echo is_in_wishlist($product['id']) ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                                </button>
                                
                                <!-- Image Area -->
                                <div class="img-wrapper">
                                    <?php if ($product['stock'] == 0): ?>
                                        <div class="sold-out-overlay">Sold Out</div>
                                    <?php endif; ?>
                                    
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
                                            <?php if ($product['price'] > 500): ?>
                                                <span class="old-price">₹<?php echo number_format($product['price'] * 1.2, 0); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn-add" onclick="event.preventDefault(); window.location.href='products.php?add_to_cart=<?php echo $product['id']; ?>'">
                                            ADD
                                        </button>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- Bottom Newsletter -->
    <section class="container my-5">
        <div class="p-5 rounded-4 text-center position-relative overflow-hidden" 
             style="background: linear-gradient(135deg, #1F2937, #111827); border: 1px solid #374151;">
            <div class="position-relative z-1">
                <h2 class="fw-bold text-white mb-3">Join the Future</h2>
                <p class="text-secondary mb-4 mx-auto" style="max-width: 500px;">Subscribe to our newsletter for exclusive drops and early access to new collections.</p>
                <form class="d-flex justify-content-center gap-2 max-w-md mx-auto" style="max-width: 400px;">
                    <input type="email" class="form-control bg-dark border-secondary text-white" placeholder="Enter your email">
                    <button class="btn fw-bold" style="background: #7C3AED; color: white;">Subscribe</button>
                </form>
            </div>
        </div>
    </section>

</div>

<script>
function scrollSlider(id, amount) {
    const slider = document.getElementById(id);
    slider.scrollBy({ left: amount, behavior: 'smooth' });
}
</script>

<?php include 'includes/footer.php'; ?>
