<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Products';

// Fetch all products from CSV
$all_products = get_all_products();

// Get filter parameters
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : null;
$sort = isset($_GET['sort']) ? clean_input($_GET['sort']) : 'name_asc';
$in_stock_only = isset($_GET['in_stock']) && $_GET['in_stock'] === '1';

// Filter products
$products = array_filter($all_products, function($product) use ($search, $min_price, $max_price, $in_stock_only) {
    $match_search = empty($search) || stripos($product['name'], $search) !== false;
    $match_min = $min_price === null || $product['price'] >= $min_price;
    $match_max = $max_price === null || $product['price'] <= $max_price;
    $match_stock = !$in_stock_only || $product['stock'] > 0;
    return $match_search && $match_min && $match_max && $match_stock;
});

// Sort products
usort($products, function($a, $b) use ($sort) {
    switch($sort) {
        case 'price_asc':
            return $a['price'] <=> $b['price'];
        case 'price_desc':
            return $b['price'] <=> $a['price'];
        case 'name_desc':
            return strcmp($b['name'], $a['name']);
        case 'stock_asc':
            return $a['stock'] <=> $b['stock'];
        case 'stock_desc':
            return $b['stock'] <=> $a['stock'];
        case 'name_asc':
        default:
            return strcmp($a['name'], $b['name']);
    }
});

include 'includes/header.php';
?>

<style>
.products-layout {
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: 2rem;
    margin: 2rem 0;
}

.products-main {
    min-width: 0;
}

.sidebar-filters {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    padding: 1.5rem;
    height: fit-content;
    position: sticky;
    top: 80px;
}

.sidebar-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-group {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.filter-group:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.filter-group-title {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: var(--black);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-input {
    width: 100%;
    padding: 0.625rem;
    font-size: 0.875rem;
    border: 1px solid var(--gray-300);
    border-radius: 4px;
    transition: var(--transition);
    margin-bottom: 0.5rem;
}

.filter-input:focus {
    outline: none;
    border-color: var(--black);
    box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: var(--transition);
}

.checkbox-label:hover {
    background: var(--gray-100);
}

.checkbox-label input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
}

.filter-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 1rem;
}

.filter-btn {
    width: 100%;
    padding: 0.75rem;
    font-size: 0.875rem;
    font-weight: 600;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: var(--transition);
    text-align: center;
    text-decoration: none;
    display: block;
}

.filter-btn-primary {
    background: var(--black);
    color: var(--white);
}

.filter-btn-primary:hover {
    background: var(--gray-800);
}

.filter-btn-secondary {
    background: var(--gray-200);
    color: var(--black);
}

.filter-btn-secondary:hover {
    background: var(--gray-300);
}

.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: var(--gray-100);
    border-radius: 4px;
}

.active-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.filter-tag {
    background: var(--black);
    color: var(--white);
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

@media (max-width: 968px) {
    .products-layout {
        grid-template-columns: 1fr;
    }
    
    .sidebar-filters {
        position: static;
        order: -1;
    }
}
</style>

<div class="container">
    <h1 class="section-title"><i class="bi bi-grid"></i> All Products</h1>
    
    <div class="products-layout">
        <!-- Main Products Area -->
        <div class="products-main">
            <!-- Results Header -->
            <div class="results-header">
                <div>
                    <strong><i class="bi bi-box-seam"></i> <?php echo count($products); ?> of <?php echo count($all_products); ?> products</strong>
                    <?php if ($search || $min_price !== null || $max_price !== null || $in_stock_only): ?>
                        <div class="active-filters">
                            <?php if ($search): ?>
                                <span class="filter-tag">🔍 "<?php echo htmlspecialchars($search); ?>"</span>
                            <?php endif; ?>
                            <?php if ($min_price !== null): ?>
                                <span class="filter-tag">Min: ₹<?php echo number_format($min_price, 0); ?></span>
                            <?php endif; ?>
                            <?php if ($max_price !== null): ?>
                                <span class="filter-tag">Max: ₹<?php echo number_format($max_price, 0); ?></span>
                            <?php endif; ?>
                            <?php if ($in_stock_only): ?>
                                <span class="filter-tag">✅ In Stock</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="products-grid">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'150\' height=\'150\'%3E%3Crect fill=\'%23f5f5f5\' width=\'150\' height=\'150\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'12\'%3E📦%3C/text%3E%3C/svg%3E'">
                                <?php endif; ?>
                                
                                <?php if ($product['stock'] == 0): ?>
                                    <div class="badge" style="background: #999;">Out of Stock</div>
                                <?php elseif ($product['stock'] < 20): ?>
                                    <div class="badge">⚠️ Low</div>
                                <?php elseif ($product['stock'] > 80): ?>
                                    <div class="badge badge-sale">✅</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-info">
                                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <div class="product-rating">
                                    <span class="stars">⭐⭐⭐⭐☆</span>
                                    <span>(<?php echo rand(100, 9999); ?>)</span>
                                </div>
                                <div class="product-price">₹<?php echo number_format($product['price'], 0); ?></div>
                                <div class="product-meta">
                                    <span>📦 <?php echo $product['stock']; ?></span>
                                    <span>🆔 #<?php echo $product['id']; ?></span>
                                </div>
                                <div class="product-actions">
                                    <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-black">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <button onclick="quickAddToCart(<?php echo $product['id']; ?>)" 
                                            class="btn btn-icon" 
                                            style="background: var(--gray-800); color: var(--white);"
                                            <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>>
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; background: var(--gray-100); border-radius: 8px;">
                        <p style="font-size: 3rem; margin-bottom: 1rem;">🔍</p>
                        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">No products found</h3>
                        <p style="color: var(--gray-600); margin-bottom: 1.5rem;">Try adjusting your filters</p>
                        <a href="products.php" class="btn btn-black">Clear Filters</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Right Sidebar Filters -->
        <aside class="sidebar-filters">
            <h2 class="sidebar-title"><i class="bi bi-funnel"></i> Filters</h2>
            
            <form method="GET">
                <!-- Search Filter -->
                <div class="filter-group">
                    <div class="filter-group-title"><i class="bi bi-search"></i> Search</div>
                    <input type="text" 
                           name="search" 
                           class="filter-input" 
                           placeholder="Search products..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <!-- Price Range Filter -->
                <div class="filter-group">
                    <div class="filter-group-title"><i class="bi bi-currency-rupee"></i> Price Range</div>
                    <input type="number" 
                           name="min_price" 
                           class="filter-input" 
                           placeholder="Min Price"
                           min="0"
                           value="<?php echo $min_price !== null ? $min_price : ''; ?>">
                    <input type="number" 
                           name="max_price" 
                           class="filter-input" 
                           placeholder="Max Price"
                           min="0"
                           value="<?php echo $max_price !== null ? $max_price : ''; ?>">
                </div>
                
                <!-- Sort Filter -->
                <div class="filter-group">
                    <div class="filter-group-title"><i class="bi bi-sort-down"></i> Sort By</div>
                    <select name="sort" class="filter-input">
                        <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                        <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                        <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Price: Low → High</option>
                        <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Price: High → Low</option>
                        <option value="stock_asc" <?php echo $sort == 'stock_asc' ? 'selected' : ''; ?>>Stock: Low → High</option>
                        <option value="stock_desc" <?php echo $sort == 'stock_desc' ? 'selected' : ''; ?>>Stock: High → Low</option>
                    </select>
                </div>
                
                <!-- Stock Filter -->
                <div class="filter-group">
                    <div class="filter-group-title"><i class="bi bi-box-seam"></i> Availability</div>
                    <label class="checkbox-label">
                        <input type="checkbox" name="in_stock" value="1" <?php echo $in_stock_only ? 'checked' : ''; ?>>
                        <span>In Stock Only</span>
                    </label>
                </div>
                
                <!-- Action Buttons -->
                <div class="filter-actions">
                    <button type="submit" class="filter-btn filter-btn-primary">
                        <i class="bi bi-check-circle"></i> Apply Filters
                    </button>
                    <a href="products.php" class="filter-btn filter-btn-secondary">
                        <i class="bi bi-x-circle"></i> Clear All
                    </a>
                </div>
            </form>
        </aside>
    </div>
</div>

<script>
function quickAddToCart(productId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'product-detail.php?id=' + productId;
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'add_to_cart';
    input.value = '1';
    
    const quantity = document.createElement('input');
    quantity.type = 'hidden';
    quantity.name = 'quantity';
    quantity.value = '1';
    
    form.appendChild(input);
    form.appendChild(quantity);
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php include 'includes/footer.php'; ?>
