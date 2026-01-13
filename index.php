<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Home';

// Fetch all products
$all_products = get_all_products();

// Group products by category
$categories_data = [];
foreach ($all_products as $product) {
    $categories_data[$product['category']][] = $product;
}

// Priority order: New categories first, then Groceries & Clothing, Electronics last
$priority_order = [
    'Audio & Speakers',
    'Sports & Fitness', 
    'Footwear',
    'Men\'s Fashion',
    'Men\'s Shirts',
    'Travel & Accessories',
    'Groceries',
    'Clothing'
];

// Move Electronics to the end if it exists
if (isset($categories_data['Electronics'])) {
    $electronics = $categories_data['Electronics'];
    unset($categories_data['Electronics']);
}

// Apply priority order
foreach (array_reverse($priority_order) as $cat_name) {
    if (isset($categories_data[$cat_name])) {
        $temp = $categories_data[$cat_name];
        unset($categories_data[$cat_name]);
        $categories_data = [$cat_name => $temp] + $categories_data;
    }
}

// Add Electronics at the end
if (isset($electronics)) {
    $categories_data['Electronics'] = $electronics;
}

// Wishlist Toggle logic
if (isset($_GET['wishlist_toggle'])) {
    $p_id = intval($_GET['wishlist_toggle']);
    if (is_in_wishlist($p_id)) {
        remove_from_wishlist($p_id);
        $script_toast = 'showToast("Removed from wishlist", "info")';
    } else {
        add_to_wishlist($p_id);
        $script_toast = 'showToast("Added to wishlist! ❤️", "success")';
    }
    // Redirect to remove the query parameter and prevent re-submission
    header("Location: index.php#categories");
    exit();
}

// Quick Add to Cart logic
if (isset($_GET['add_to_cart'])) {
    $p_id = intval($_GET['add_to_cart']);
    add_to_cart($p_id);
    $script_toast = 'showToast("Product added to cart!", "success")';
}

include 'includes/header.php';
if (isset($script_toast)) echo "<script>window.onload = () => $script_toast;</script>";
?>

<!-- Fast Auto-Scrolling Hero Section (3 Slides) -->
<section class="hero-carousel p-0 m-0">
    <div class="container-fluid p-0">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            </div>
            
            <div class="carousel-inner">
                <!-- Slide 1: Republic Day Sale -->
                <div class="carousel-item active">
                    <div class="position-relative" style="height: 500px; overflow: hidden;">
                        <img src="data/images/hero/republic-day.png" class="w-100 h-100 object-fit-cover" alt="Republic Day Sale">
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: linear-gradient(to right, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);">
                            <div class="container">
                                <div class="col-lg-6 col-md-8">
                                    <span class="badge mb-3 py-2 px-3 fw-bold" style="background: rgba(255, 103, 31, 0.9); color: #fff; font-size: 0.9rem;">REPUBLIC DAY SALE</span>
                                    <h1 class="display-3 fw-black text-white mb-3" style="font-weight: 900; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">National Pride</h1>
                                    <p class="lead text-white mb-4 fw-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Massive savings up to <span style="color: #FFD700;">60% OFF</span> on heritage crafts and essentials.</p>
                                    <a href="products.php" class="btn btn-lg px-5 py-3 fw-bold" style="background: #FF671F; color: #fff; border: none;">Shop Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 2: New Year Luxury -->
                <div class="carousel-item">
                    <div class="position-relative" style="height: 500px; overflow: hidden;">
                        <img src="data/images/hero/luxury-watch.png" class="w-100 h-100 object-fit-cover" alt="New Year Luxury Sale">
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: linear-gradient(to right, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.3) 50%, transparent 100%);">
                            <div class="container">
                                <div class="col-lg-6 col-md-8">
                                    <span class="badge mb-3 py-2 px-3 fw-bold" style="background: rgba(230, 168, 85, 0.9); color: #000; font-size: 0.9rem;">2026 PREMIUM DEALS</span>
                                    <h1 class="display-3 fw-black text-white mb-3" style="font-weight: 900; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">New Year Luxury</h1>
                                    <p class="lead text-white mb-4 fw-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Exclusive <span style="color: #e6a855;">Flat ₹2000 OFF</span> on luxury watches and tech.</p>
                                    <a href="products.php?category=Electronics" class="btn btn-lg px-5 py-3 fw-bold" style="background: #2d3261; color: #fff; border: none;">Browse Luxury</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 3: Mega Festival Sale -->
                <div class="carousel-item">
                    <div class="position-relative" style="height: 500px; overflow: hidden;">
                        <img src="data/images/hero/shopping-sale.png" class="w-100 h-100 object-fit-cover" alt="Mega Festival Sale">
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: linear-gradient(to right, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.2) 50%, transparent 100%);">
                            <div class="container">
                                <div class="col-lg-6 col-md-8">
                                    <span class="badge mb-3 py-2 px-3 fw-bold" style="background: rgba(235, 64, 52, 0.9); color: #fff; font-size: 0.9rem;">EXCLUSIVE DISCOUNTS</span>
                                    <h1 class="display-3 fw-black text-white mb-3" style="font-weight: 900; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Mega Festival</h1>
                                    <p class="lead text-white mb-4 fw-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Unbeatable offers across all categories for a limited time.</p>
                                    <a href="products.php" class="btn btn-lg px-5 py-3 fw-bold" style="background: #eb4034; color: #fff; border: none;">Shop the Sale</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</section>

<style>
.hero-3d-container {
    perspective: 2000px;
    height: 600px; /* Increased height */
    width: 100%;
}
.hero-3d-slider {
    position: relative;
    width: 100%;
    height: 100%;
}
@keyframes heroBreath {
    0% { opacity: 0; transform: scale(1.02) translateY(5px); filter: blur(10px); }
    10% { opacity: 1; transform: scale(1) translateY(0); filter: blur(0); }
    33% { opacity: 1; transform: scale(1) translateY(0); filter: blur(0); }
    43% { opacity: 0; transform: scale(0.98) translateY(-5px); filter: blur(10px); }
    100% { opacity: 0; }
}

.hero-3d-slide {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    overflow: hidden;
    background: transparent !important; /* Emotional transparency */
    opacity: 0;
}

.hero-3d-slide:nth-child(1) { animation: heroBreath 18s infinite 0s; }
.hero-3d-slide:nth-child(2) { animation: heroBreath 18s infinite 6s; }
.hero-3d-slide:nth-child(3) { animation: heroBreath 18s infinite 12s; }
</style>



<!-- Category Sliders (The "Crocer" Sections) -->
<div class="container" id="categories">
    <?php foreach ($categories_data as $category_name => $products): ?>
        <div class="mb-5 section-category">
            <div class="d-flex justify-content-between align-items-end mb-4 px-1">
                <div>
                    <h2 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($category_name); ?></h2>
                    <p class="text-secondary small mb-0">Discover 50+ handpicked items in <?php echo $category_name; ?></p>
                </div>
                <a href="products.php?category=<?php echo urlencode($category_name); ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">View All <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            
            <!-- Horizontal Slider Container -->
            <div class="slider-wrapper position-relative">
                <button class="btn btn-white shadow-sm rounded-circle position-absolute top-50 start-0 translate-middle-y z-3 d-none d-lg-flex align-items-center justify-content-center slider-prev" style="width: 45px; height: 45px; left: -22px !important;">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div class="category-slider d-flex overflow-auto gap-4 pb-4 px-1" style="scrollbar-width: none; -ms-overflow-style: none; scroll-behavior: smooth;">
                    <?php foreach ($products as $product): ?>
                        <div class="slider-item" style="min-width: 280px; flex: 0 0 280px;">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-card transition-hover position-relative">
                                <div class="position-absolute top-0 end-0 m-3 z-2">
                                    <a href="?wishlist_toggle=<?php echo $product['id']; ?>" class="btn btn-white btn-sm rounded-circle shadow-sm">
                                        <i class="bi <?php echo is_in_wishlist($product['id']) ? 'bi-heart-fill text-danger' : 'bi-heart'; ?>"></i>
                                    </a>
                                </div>
                                
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="text-decoration-none h-100 d-flex flex-column">
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center p-0" style="height: 200px;">
                                        <?php if ($product['stock'] == 0): ?>
                                            <span class="badge position-absolute top-50 start-50 translate-middle shadow z-1 py-1 px-3 rounded-pill fw-bold border" style="background: white !important; color: black !important;">Sold Out</span>
                                        <?php endif; ?>
                                        <div class="img-wrapper w-100 h-100 overflow-hidden d-flex align-items-center justify-content-center">
                                            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid transition-all" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-height: 80%; object-fit: contain;" loading="lazy">
                                        </div>
                                    </div>
                                    <div class="card-body p-4 d-flex flex-column">
                                        <h6 class="card-title fw-bold mb-1 text-dark text-truncate"><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <div class="text-warning small mb-3">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fs-5 fw-bold text-dark">₹<?php echo number_format($product['price'], 0); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="btn btn-white shadow-sm rounded-circle position-absolute top-50 end-0 translate-middle-y z-3 d-none d-lg-flex align-items-center justify-content-center slider-next" style="width: 45px; height: 45px; right: -22px !important;">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Newsletter -->
<section class="container mb-5 mt-5 pt-3">
    <div class="bg-dark text-white p-5 rounded-5 shadow text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
        <div class="position-relative z-1">
            <h2 class="fw-bold mb-3">Join the Univaut Circle</h2>
            <p class="lead mb-4 opacity-75">Get early access to drops and exclusive content delivered to your inbox.</p>
            <form class="d-flex gap-2 mx-auto flex-column flex-md-row" style="max-width: 500px;">
                <input type="email" class="form-control rounded-pill px-4 border-0 shadow-sm py-3" placeholder="Enter your email address">
                <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold shadow py-3">Subscribe Now</button>
            </form>
        </div>
        <i class="bi bi-envelope-paper-fill position-absolute text-white-50" style="font-size: 15rem; bottom: -5rem; right: -2rem; opacity: 0.1; transform: rotate(-15deg);"></i>
    </div>
</section>

<style>
.category-slider::-webkit-scrollbar { display: none; }
.category-slider { scroll-snap-type: x mandatory; }
.slider-item { scroll-snap-align: start; }
.slider-prev, .slider-next {
    background: white !important;
    border: 1px solid #eee !important;
    color: #3b82f6 !important;
    transition: all 0.2s ease;
}
.slider-prev:hover, .slider-next:hover {
    background: #3b82f6 !important;
    color: white !important;
    transform: translateY(-50%) scale(1.1) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.slider-wrapper').forEach(wrapper => {
        const slider = wrapper.querySelector('.category-slider');
        const prevBtn = wrapper.querySelector('.slider-prev');
        const nextBtn = wrapper.querySelector('.slider-next');
        
        if (prevBtn && nextBtn) {
            prevBtn.addEventListener('click', () => {
                slider.scrollBy({ left: -600, behavior: 'smooth' });
            });
            
            nextBtn.addEventListener('click', () => {
                slider.scrollBy({ left: 600, behavior: 'smooth' });
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
