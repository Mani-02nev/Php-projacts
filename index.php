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
    }
    else {
        add_to_wishlist($p_id);
    }
    // Maintain scroll position
    $redirect_url = strtok($_SERVER["REQUEST_URI"], '?') . '?' . http_build_query(array_diff_key($_GET, ['wishlist_toggle' => '']));
    header("Location: $redirect_url");
    exit;
}

include 'includes/header.php';
?>

<!-- HERO SLIDER SECTION -->
<link rel="stylesheet" href="css/hero-slider.css">
<section class="premium-hero-container">
    <div id="hero-3d-canvas" class="hero-3d-bg"></div>

    <!-- Slide 1: Electronics -->
    <div class="hero-slide active">
        <div class="container h-100 position-relative z-2">
            <div class="row h-100 align-items-center">
                <div class="col-lg-6 hero-text-col">
                    <span class="hero-label">ELECTRONICS</span>
                    <h1 class="hero-title">Next-Generation<br>Smart Devices</h1>
                    <p class="hero-description">Discover powerful smartphones, premium smartwatches, and intelligent accessories designed for your digital life.</p>
                    <div class="hero-buttons">
                        <a href="products.php?category=Electronics" class="btn-hero-primary">Shop Electronics</a>
                        <a href="#categories" class="btn-hero-secondary">Explore Smart Devices</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Slide 2: Shoes -->
    <div class="hero-slide">
        <div class="container h-100 position-relative z-2">
            <div class="row h-100 align-items-center">
                <div class="col-lg-6 hero-text-col">
                    <span class="hero-label">FOOTWEAR</span>
                    <h1 class="hero-title">Move With<br>Confidence</h1>
                    <p class="hero-description">Performance footwear designed for comfort, speed, and everyday style. Elevate your every step.</p>
                    <div class="hero-buttons">
                        <a href="products.php?category=Footwear" class="btn-hero-primary">Shop Shoes</a>
                        <a href="#categories" class="btn-hero-secondary">View Collections</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Slide 3: Groceries -->
    <div class="hero-slide">
        <div class="container h-100 position-relative z-2">
            <div class="row h-100 align-items-center">
                <div class="col-lg-6 hero-text-col">
                    <span class="hero-label">ESSENTIALS</span>
                    <h1 class="hero-title">Fresh Essentials<br>Delivered</h1>
                    <p class="hero-description">Your daily groceries delivered fast, fresh, and reliable. Stock up your pantry with premium goods.</p>
                    <div class="hero-buttons">
                        <a href="products.php?category=Groceries" class="btn-hero-primary">Shop Groceries</a>
                        <a href="#categories" class="btn-hero-secondary">View Offers</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Slide 4: Vegetables -->
    <div class="hero-slide">
        <div class="container h-100 position-relative z-2">
            <div class="row h-100 align-items-center">
                <div class="col-lg-6 hero-text-col">
                    <span class="hero-label">ORGANIC</span>
                    <h1 class="hero-title">Farm Fresh<br>Vegetables</h1>
                    <p class="hero-description">Healthy, organic vegetables sourced directly from trusted farms. Taste the nature in every bite.</p>
                    <div class="hero-buttons">
                        <a href="products.php?category=Vegetables" class="btn-hero-primary">Shop Vegetables</a>
                        <a href="#categories" class="btn-hero-secondary">View Fresh Picks</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="hero-nav-controls container">
        <div class="slider-dots">
            <span class="dot active" onclick="gotoSlide(1)"></span>
            <span class="dot" onclick="gotoSlide(2)"></span>
            <span class="dot" onclick="gotoSlide(3)"></span>
            <span class="dot" onclick="gotoSlide(4)"></span>
        </div>
        <div class="slider-arrows d-none d-md-flex">
            <button onclick="prevSlide()" class="btn-arrow"><i class="bi bi-chevron-left"></i></button>
            <button onclick="nextSlide()" class="btn-arrow"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
</section>

<!-- CATEGORY SECTIONS -->
<div id="categories" class="container-fluid px-0 py-5" style="background-color: #F8F9FA;">
    
    <?php foreach ($categories_data as $category_name => $products): ?>
        <?php if (count($products) > 0): // Only show categories with items ?>
        <section class="mb-5 px-lg-5 px-3 category-scroll-trigger">
            <!-- Section Header -->
            <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3 section-header-animate" style="border-color: #E5E7EB !important;">
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
                       style="border-color: #D1D5DB; color: #6B7280;">
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
                                    <?php
            endif; ?>
                                    
                                    <img src="<?php echo(strpos($product['image'], 'http') === 0) ? htmlspecialchars($product['image']) : 'assets/images/' . htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
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
                                            <?php
            endif; ?>
                                        </div>
                                        <button class="btn-add" data-id="<?php echo $product['id']; ?>">
                                            ADD
                                        </button>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php
        endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    endif; ?>
    <?php
endforeach; ?>

    <!-- Bottom Newsletter -->
    <section class="container my-5">
        <div class="p-5 rounded-4 text-center position-relative overflow-hidden" 
             style="background: linear-gradient(135deg, #E5E7EB, #F1F5F9); border: 1px solid #D1D5DB;">
            <div class="position-relative z-1">
                <h2 class="fw-bold text-body mb-3">Join the Future</h2>
                <p class="text-secondary mb-4 mx-auto" style="max-width: 500px;">Subscribe to our newsletter for exclusive drops and early access to new collections.</p>
                <form class="d-flex justify-content-center gap-2 max-w-md mx-auto" style="max-width: 400px;">
                    <input type="email" class="form-control bg-white border-light-subtle text-body" placeholder="Enter your email">
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

<!-- Three.js for Hero Background Effects -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<!-- Custom Hero Slider Logic -->
<script src="js/hero-slider.js"></script>

<?php include 'includes/footer.php'; ?>
