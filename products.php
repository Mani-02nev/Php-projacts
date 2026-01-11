<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Products';

// Fetch all products from CSV
$all_products = get_all_products();

// Quick Add to Cart
if (isset($_GET['add_to_cart'])) {
    $p_id = intval($_GET['add_to_cart']);
    add_to_cart($p_id);
    $script_toast = 'showToast("Product added to cart!", "success")';
}

// Get filter parameters
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? clean_input($_GET['category']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : null;
$sort = isset($_GET['sort']) ? clean_input($_GET['sort']) : 'name_asc';
$in_stock_only = isset($_GET['in_stock']) && $_GET['in_stock'] === '1';

// Handle Wishlist Toggle via AJAX or Page Reload
if (isset($_GET['wishlist_toggle'])) {
    $p_id = intval($_GET['wishlist_toggle']);
    if (is_in_wishlist($p_id)) {
        remove_from_wishlist($p_id);
        $script_toast = 'showToast("Removed from wishlist", "info")';
    } else {
        add_to_wishlist($p_id);
        $script_toast = 'showToast("Added to wishlist!", "danger")';
    }
}

// Filter products
$products = array_filter($all_products, function($product) use ($search, $category_filter, $min_price, $max_price, $in_stock_only) {
    $match_search = empty($search) || stripos($product['name'], $search) !== false;
    $match_category = empty($category_filter) || $product['category'] === $category_filter;
    $match_min = $min_price === null || $product['price'] >= $min_price;
    $match_max = $max_price === null || $product['price'] <= $max_price;
    $match_stock = !$in_stock_only || $product['stock'] > 0;
    return $match_search && $match_category && $match_min && $match_max && $match_stock;
});

// Get unique categories for filter
$all_categories = array_unique(array_column($all_products, 'category'));
sort($all_categories);

// Sort products
usort($products, function($a, $b) use ($sort) {
    switch($sort) {
        case 'price_asc': return $a['price'] <=> $b['price'];
        case 'price_desc': return $b['price'] <=> $a['price'];
        case 'name_desc': return strcmp($b['name'], $a['name']);
        case 'stock_asc': return $a['stock'] <=> $b['stock'];
        case 'stock_desc': return $b['stock'] <=> $a['stock'];
        case 'name_asc': default: return strcmp($a['name'], $b['name']);
    }
});

include 'includes/header.php';
if (isset($script_toast)) echo "<script>window.onload = () => $script_toast;</script>";
?>

<div class="container py-5">
    <div class="row mb-5 align-items-center">
        <div class="col-md-6">
            <h1 class="fw-bold mb-2 text-indigo" style="letter-spacing: -1.5px;">Curated <span class="text-gold">Collections</span></h1>
            <p class="text-muted mb-0">Discover digital craftsmanship inspired by tradition.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-md-end mb-0">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                    <li class="breadcrumb-item active text-indigo fw-bold" aria-current="page">Collections</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 sticky-top overflow-visible" style="top: 100px; background: #ffffff;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-indigo">Refine Search</h5>
                    
                    <form method="GET">
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Search</label>
                            <div class="input-group overflow-hidden rounded-3 border">
                                <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-0 px-2" placeholder="Product name..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Category</label>
                            <select name="category" class="form-select rounded-3">
                                <option value="">All Categories</option>
                                <?php foreach ($all_categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category_filter == $cat ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control rounded-3" placeholder="Min" value="<?php echo $min_price !== null ? $min_price : ''; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control rounded-3" placeholder="Max" value="<?php echo $max_price !== null ? $max_price : ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Sort By</label>
                            <select name="sort" class="form-select rounded-3">
                                <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                                <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                                <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low → High</option>
                                <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High → Low</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2">Apply Changes</button>
                            <a href="products.php" class="btn btn-link text-muted btn-sm text-decoration-none">Clear All</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Main Products Area -->
        <div class="col-lg-9">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4 flow-in">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-card transition-hover position-relative">
                                <div class="position-absolute top-0 end-0 m-3 z-2">
                                    <a href="?wishlist_toggle=<?php echo $product['id']; ?>&<?php echo http_build_query($_GET); ?>" class="btn btn-white btn-sm rounded-circle shadow-sm p-2">
                                        <i class="bi <?php echo is_in_wishlist($product['id']) ? 'bi-heart-fill text-danger' : 'bi-heart'; ?>"></i>
                                    </a>
                                </div>
                                
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="text-decoration-none h-100 d-flex flex-column text-dark">
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center p-4" style="height: 220px;">
                                        <?php if ($product['stock'] == 0): ?>
                                            <span class="badge bg-white text-dark position-absolute top-50 start-50 translate-middle shadow z-1 py-1 px-3 rounded-pill fw-bold border">Sold Out</span>
                                        <?php endif; ?>
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid transition-all" alt="" style="max-height: 100%; object-fit: contain;">
                                    </div>
                                    
                                    <div class="card-body p-4 d-flex flex-column">
                                        <div class="mb-2">
                                            <h6 class="card-title fw-bold mb-1 text-dark text-truncate"><?php echo htmlspecialchars($product['name']); ?></h6>
                                            <small class="text-secondary opacity-75"><?php echo htmlspecialchars($product['category']); ?></small>
                                        </div>
                                        
                                        <div class="mt-auto">
                                            <h5 class="fw-bold text-dark mb-0">₹<?php echo number_format($product['price'], 0); ?></h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="bg-white rounded-5 p-5 shadow-sm border">
                            <h3 class="fw-bold">No results found</h3>
                            <a href="products.php" class="btn btn-primary rounded-pill px-5 fw-bold py-2 mt-3">Reset Filters</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
