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
/* THREE.JS HERO STYLES */
#hero-canvas-container {
    position: absolute;
    top: 0;
    right: 0;
    width: 60%; /* Take up more space on the right */
    height: 100%;
    z-index: 1;
    overflow: hidden;
}

@media (max-width: 991px) {
    #hero-canvas-container {
        width: 100%;
        opacity: 0.5; /* Fade background 3d on mobile for legibility */
    }
}

.hero-3d-wrapper {
    position: relative;
    height: 90vh;
    min-height: 700px;
    background: #0B0B0E;
    overflow: hidden;
    display: flex;
    align-items: center;
}

.hero-content-layer {
    z-index: 10;
    position: relative;
    pointer-events: none;
}

.hero-content-layer .btn, 
.hero-content-layer a {
    pointer-events: auto; 
}

.hero-title {
    font-size: clamp(3rem, 8vw, 5.5rem);
    font-weight: 900;
    line-height: 0.95;
    letter-spacing: -2px;
    color: #FFFFFF;
    text-shadow: 0 20px 50px rgba(0,0,0,0.3);
}

.hero-subtitle {
    font-size: 1.15rem;
    color: #9CA3AF;
    font-weight: 400;
    line-height: 1.6;
}

.hero-badge {
    background: rgba(124, 58, 237, 0.1);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(124, 58, 237, 0.2);
    color: #C084FC;
    font-weight: 600;
    text-transform: uppercase;
}

.btn-glow {
    transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
}
.btn-glow:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 20px 40px rgba(124, 58, 237, 0.5);
}

.hero-fade-bottom {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 200px;
    background: linear-gradient(to top, #0B0B0E 0%, transparent 100%);
    z-index: 5;
    pointer-events: none;
}

/* PRODUCT CARD ANIMATIONS */
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

<!-- 3D HERO SECTION: GLOBAL COMMERCE -->
<div class="hero-3d-wrapper">
    <!-- background cinematic globe -->
    <div id="hero-canvas-container" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;"></div>

    <!-- Content Overlay -->
    <div class="container-fluid hero-content-layer h-100 d-flex align-items-center justify-content-center text-center" style="position: relative; z-index: 10;">
        <div class="col-lg-8 animate__animated animate__fadeInUp">
            
            <!-- AI Label -->
            <div class="d-flex justify-content-center mb-4">
                <span class="badge rounded-pill px-4 py-2 hero-badge font-monospace" style="letter-spacing: 3px; background: rgba(124, 58, 237, 0.1); border: 1px solid rgba(124, 58, 237, 0.3); color: #A78BFA;">
                    AI-POWERED COMMERCE
                </span>
            </div>
            
            <h1 class="hero-title mb-4" style="font-size: clamp(3rem, 8vw, 5rem); font-weight: 800; letter-spacing: -1px; line-height: 1.1;">
                Future of <span style="background: linear-gradient(90deg, #A78BFA, #06B6D4); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Shopping</span>
            </h1>
            
            <p class="hero-subtitle mb-5 text-secondary mx-auto" style="max-width: 700px; font-size: 1.25rem; line-height: 1.6; color: rgba(255,255,255,0.7) !important;">
                Discover smarter shopping with AI-powered recommendations, premium products, and seamless delivery experiences designed for the modern customer.
            </p>
            
            <div class="d-flex flex-wrap justify-content-center gap-4 mb-5">
                <a href="products.php" class="btn btn-lg rounded-pill px-5 fw-bold btn-glow-purple" style="background: #7C3AED; color: white; border: none; box-shadow: 0 0 20px rgba(124, 58, 237, 0.4);">
                    Shop Smarter
                </a>
                <a href="#categories" class="btn btn-lg btn-outline-light rounded-pill px-5 fw-bold" style="border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.05); backdrop-filter: blur(10px);">
                    Explore Categories
                </a>
            </div>
            
            <!-- Micro Trust Row -->
            <div class="d-flex justify-content-center gap-4 align-items-center opacity-75 font-monospace" style="font-size: 0.8rem; letter-spacing: 2px; color: #9CA3AF;">
                <span>PERSONALIZED</span>
                <span style="color: #7C3AED;">•</span>
                <span>FAST</span>
                <span style="color: #7C3AED;">•</span>
                <span>INTELLIGENT</span>
            </div>
        </div>
    </div>
    
    <div class="hero-fade-bottom"></div>
</div>

<style>
/* Global Hero Styles */
.hero-3d-wrapper {
    position: relative;
    height: 100vh;
    width: 100%;
    background: #0B0B0E;
    overflow: hidden;
}

.btn-glow-purple:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 40px rgba(124, 58, 237, 0.6) !important;
    transition: all 0.3s ease;
}

.hero-fade-bottom {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 300px;
    background: linear-gradient(to top, #0B0B0E, transparent);
    z-index: 5;
}
</style>

<!-- CATEGORY SECTIONS -->
<div id="categories" class="container-fluid px-0 py-5" style="background-color: #0B0B0E;">
    
    <?php foreach ($categories_data as $category_name => $products): ?>
        <?php if (count($products) > 0): // Only show categories with items ?>
        <section class="mb-5 px-lg-5 px-3 category-scroll-trigger">
            <!-- Section Header -->
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3 section-header-animate" style="border-color: #1F2937 !important;">
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
                        <div class="reveal-on-scroll" style="min-width: 280px; max-width: 280px; scroll-snap-align: start;">
                            <!-- Global Product Card Component -->
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="global-product-card">
                                <!-- Wishlist Btn -->
                                <button class="wishlist-btn <?php echo is_in_wishlist($product['id']) ? 'active' : ''; ?>" 
                                        data-id="<?php echo $product['id']; ?>">
                                    <i class="bi <?php echo is_in_wishlist($product['id']) ? 'bi-heart-fill' : 'bi-heart'; ?>"></i>
                                </button>
                                
                                <!-- Image Area -->
                                <div class="img-wrapper">
                                    <?php if ($product['stock'] == 0): ?>
                                        <div class="sold-out-overlay">Sold Out</div>
                                    <?php endif; ?>
                                    
                                    <img src="<?php echo (strpos($product['image'], 'http') === 0) ? htmlspecialchars($product['image']) : 'assets/images/' . htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
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
                                        <button class="btn-add" data-id="<?php echo $product['id']; ?>">
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

<!-- Three.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<!-- Custom 3D Hero Script -->
<script src="js/hero-scene.js"></script>

<?php include 'includes/footer.php'; ?>
