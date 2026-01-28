<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Shop Products';

// Fetch all products
$all_products = get_all_products();

// Toast Logic
if (isset($_GET['add_to_cart'])) {
    $p_id = intval($_GET['add_to_cart']);
    add_to_cart($p_id);
    $script_toast = 'showToast("Product added to cart", "success")';
}
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

// Filters logic
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? clean_input($_GET['category']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : null;
$sort = isset($_GET['sort']) ? clean_input($_GET['sort']) : 'name_asc';

// Filter Processing
$products = array_filter($all_products, function($product) use ($search, $category_filter, $min_price, $max_price) {
    if ($search && stripos($product['name'], $search) === false) return false;
    if ($category_filter && $product['category'] !== $category_filter) return false;
    if ($min_price !== null && $product['price'] < $min_price) return false;
    if ($max_price !== null && $product['price'] > $max_price) return false;
    return true;
});

// Categories
$all_categories = array_unique(array_column($all_products, 'category'));
sort($all_categories);

// Sorting
usort($products, function($a, $b) use ($sort) {
    switch($sort) {
        case 'price_asc': return $a['price'] <=> $b['price'];
        case 'price_desc': return $b['price'] <=> $a['price'];
        case 'stock_asc': return $a['stock'] <=> $b['stock'];
        default: return strcasecmp($a['name'], $b['name']);
    }
});

include 'includes/header.php';
if (isset($script_toast)) echo "<script>window.addEventListener('load', () => { $script_toast });</script>";
?>

<!-- FORCE FULL WIDTH LAYOUT -->
<style>
/* Page Specific Reset */
.main-content, .container, .container-fluid {
    max-width: 100% !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    margin: 0 !important;
}
/* Ensure the grid has padding on the edges */
.global-grid-wrapper {
    padding: 20px;
    background-color: #0B0B0E;
    min-height: 100vh;
}
</style>

<!-- ACTION TOOLBAR -->
<div class="sticky-top border-bottom border-secondary border-opacity-25" style="background: rgba(20, 22, 26, 0.95); backdrop-filter: blur(10px); z-index: 1000;">
    <div class="px-4 py-3 d-flex justify-content-between align-items-center">
        <!-- Filter Trigger -->
        <button class="btn btn-outline-secondary rounded-0 text-uppercase fw-bold d-flex align-items-center gap-2 px-4" 
                type="button" data-bs-toggle="offcanvas" data-bs-target="#filterSidebar"
                style="font-size: 0.8rem; letter-spacing: 1px;">
            <i class="bi bi-filter-left fs-5"></i> Filters
        </button>

        <!-- Product Count -->
        <span class="text-uppercase fw-bold text-muted small letter-spacing-2"><?php echo count($products); ?> Items Found</span>

        <!-- Sort -->
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted text-uppercase small fw-bold d-none d-md-block">Sort By</span>
            <form method="GET" class="m-0">
                <?php foreach($_GET as $key => $val) { if($key != 'sort') echo "<input type='hidden' name='$key' value='$val'>"; } ?>
                <select name="sort" class="form-select form-select-sm bg-dark text-white border-secondary rounded-0 text-uppercase fw-bold py-2 ps-3" 
                        onchange="this.form.submit()" style="min-width: 160px; font-size: 0.8rem;">
                    <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                    <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                    <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                </select>
            </form>
        </div>
    </div>
</div>

<!-- PRODUCT GRID -->
<div class="global-grid-wrapper">
    <div class="global-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
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
                        
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="text-secondary opacity-25"><i class="bi bi-image" style="font-size: 3rem;"></i></div>
                        <?php endif; ?>
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
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; min-height: 60vh; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #0B0B0E;">
                <i class="bi bi-search text-secondary opacity-25 mb-4" style="font-size: 4rem;"></i>
                <h3 class="fw-bold text-white mb-2">No Products Found</h3>
                <p class="text-secondary mb-4">We couldn't find matches for your search.</p>
                <a href="products.php" class="btn btn-outline-light rounded-0 px-5 text-uppercase fw-bold">View All Products</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- OFF CANVAS FILTERS -->
<div class="offcanvas offcanvas-start border-end border-secondary border-opacity-25" tabindex="-1" id="filterSidebar" style="background-color: #0B0B0E; width: 320px;">
    <div class="offcanvas-header border-bottom border-dark p-4">
        <h5 class="offcanvas-title fw-bold text-white text-uppercase letter-spacing-2">Filters</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-4">
         <form method="GET">
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
            
            <!-- Search -->
            <div class="mb-5">
                <label class="d-block text-secondary fw-bold text-uppercase small mb-3 letter-spacing-1">Search</label>
                <input type="text" name="search" class="form-control bg-dark border-secondary border-opacity-25 text-white rounded-0 p-3" 
                       placeholder="Type Keyword..." value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <!-- Categories -->
            <div class="mb-5">
                <label class="d-block text-secondary fw-bold text-uppercase small mb-3 letter-spacing-1">Category</label>
                <div class="d-flex flex-column gap-2">
                    <label class="custom-control-label d-flex align-items-center gap-2 cursor-pointer text-white">
                        <input type="radio" name="category" value="" class="form-check-input bg-dark border-secondary rounded-circle" 
                               <?php echo $category_filter == '' ? 'checked' : ''; ?>>
                        <span>All Categories</span>
                    </label>
                    <?php foreach ($all_categories as $cat): ?>
                        <label class="custom-control-label d-flex align-items-center gap-2 cursor-pointer text-gray-300">
                            <input type="radio" name="category" value="<?php echo htmlspecialchars($cat); ?>" class="form-check-input bg-dark border-secondary rounded-circle"
                                   <?php echo $category_filter == $cat ? 'checked' : ''; ?>>
                            <span class="<?php echo $category_filter == $cat ? 'text-white fw-bold' : 'text-secondary'; ?>"><?php echo htmlspecialchars($cat); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Price -->
            <div class="mb-5">
                <label class="d-block text-secondary fw-bold text-uppercase small mb-3 letter-spacing-1">Price Range</label>
                <div class="d-flex gap-2">
                    <input type="number" name="min_price" class="form-control bg-dark border-secondary border-opacity-25 text-white rounded-0" placeholder="Min" value="<?php echo $min_price; ?>">
                    <input type="number" name="max_price" class="form-control bg-dark border-secondary border-opacity-25 text-white rounded-0" placeholder="Max" value="<?php echo $max_price; ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-0 py-3 text-uppercase fw-bold letter-spacing-2" style="background-color: #7C3AED; border:none;">
                Apply Filters
            </button>
            <a href="products.php" class="btn btn-link text-secondary w-100 mt-2 text-decoration-none text-uppercase small">Reset All</a>
         </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
