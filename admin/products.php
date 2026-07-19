<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../login.php');
}

$page_title = 'Manage Products';
$success = '';
$error = '';

// Handle product addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = clean_input($_POST['name']);
    $description = clean_input($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image = clean_input($_POST['image']);
    $category = clean_input($_POST['category'] ?? 'General');

    if (add_product($name, $description, $price, $stock, $image, $category)) {
        $success = 'Product added successfully!';
    }
    else {
        $error = 'Failed to add product.';
    }
}

// Handle product deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (delete_product($id)) {
        $success = 'Product deleted successfully!';
    }
    else {
        $error = 'Failed to delete product.';
    }
}

$products = get_all_products();

// Sort by ID descending
usort($products, function ($a, $b) {
    return $b['id'] - $a['id'];
});

include '../includes/header.php';
?>

<!-- Admin Products -->
<style>
    body { background-color: #F8F9FA !important; }
    .admin-card { background-color: #FFFFFF; border: 1px solid #E5E7EB; }
    .table-dark-custom { --bs-table-bg: transparent; --bs-table-color: #6B7280; --bs-table-border-color: #E5E7EB; }
    .table-dark-custom th { color: #374151; background-color: #E5E7EB; border-bottom: 1px solid #D1D5DB; }
    .table-dark-custom td { vertical-align: middle; border-bottom: 1px solid #E5E7EB; color: #6B7280; }
    .form-control-dark { background-color: #F8F9FA; border: 1px solid #D1D5DB; color: #374151; }
    .form-control-dark:focus { background-color: #F8F9FA; border-color: #7C3AED; color: #374151; box-shadow: 0 0 0 0.25rem rgba(124, 58, 237, 0.25); }
    .form-select-dark { background-color: #F8F9FA; border: 1px solid #D1D5DB; color: #374151; }
    .form-select-dark:focus { border-color: #7C3AED; color: #374151; box-shadow: 0 0 0 0.25rem rgba(124, 58, 237, 0.25); }
    .hover-row:hover td { background-color: rgba(0,0,0,0.02); color: #1F2937; }
</style>

<div class="container-fluid px-4 py-5" style="min-height: 100vh;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-3 mb-1">
                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background-color: #D1D5DB; color: #374151; border: 1px solid #4B5563;">
                    <i class="bi bi-box-seam me-1 text-primary"></i> INVENTORY
                </span>
            </div>
            <h1 class="fw-bold mb-0 text-body display-6">Manage Products</h1>
            <p class="text-secondary mb-0">Add, edit, and remove store items</p>
        </div>
        <div>
            <a href="./" class="btn btn-outline-secondary rounded-pill px-4 bg-white text-dark border-light-subtle fw-bold">
                <i class="bi bi-arrow-left me-2"></i> Dashboard
            </a>
        </div>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success rounded-4 border-0 shadow-sm animate__animated animate__fadeIn mb-4 bg-success-subtle border-success text-success fw-bold">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success; ?>
        </div>
    <?php
endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger rounded-4 border-0 shadow-sm animate__animated animate__fadeIn mb-4 bg-danger-subtle border-danger text-danger fw-bold">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
        </div>
    <?php
endif; ?>

    <div class="row g-4">
        <!-- Add Product Form -->
        <div class="col-lg-4">
            <div class="card admin-card rounded-4 h-100 shadow-lg">
                <div class="card-header border-bottom border-light-subtle border-opacity-25 py-3 px-4" style="background-color: transparent;">
                    <h5 class="fw-bold mb-0 text-body"><i class="bi bi-plus-circle me-2 text-primary"></i>Add New Product</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label small fw-bold text-uppercase" style="color: #6B7280;">Product Name</label>
                            <input type="text" id="name" name="name" class="form-control form-control-dark rounded-3 px-3 py-2" placeholder="e.g. Wireless Headset" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label small fw-bold text-uppercase" style="color: #6B7280;">Category</label>
                            <select id="category" name="category" class="form-select form-select-dark rounded-3 px-3 py-2">
                                <option value="Electronics">Electronics</option>
                                <option value="Fashion">Fashion</option>
                                <option value="Home">Home</option>
                                <option value="Beauty">Beauty</option>
                                <option value="Sports">Sports</option>
                                <option value="General" selected>General</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label small fw-bold text-uppercase" style="color: #6B7280;">Description</label>
                            <textarea id="description" name="description" class="form-control form-control-dark px-3 py-2" rows="4" style="border-radius: 0.75rem;" placeholder="Product details..." required></textarea>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="price" class="form-label small fw-bold text-uppercase" style="color: #6B7280;">Price (₹)</label>
                                <input type="number" id="price" name="price" class="form-control form-control-dark rounded-3 px-3 py-2" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            <div class="col-6">
                                <label for="stock" class="form-label small fw-bold text-uppercase" style="color: #6B7280;">Stock</label>
                                <input type="number" id="stock" name="stock" class="form-control form-control-dark rounded-3 px-3 py-2" min="0" placeholder="0" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="image" class="form-label small fw-bold text-uppercase" style="color: #6B7280;">Image Filename</label>
                            <input type="text" id="image" name="image" class="form-control form-control-dark rounded-3 px-3 py-2" placeholder="e.g. product.jpg">
                            <div class="form-text small ms-1" style="color: #6B7280;"><i class="bi bi-info-circle me-1"></i>File must exist in assets/images/</div>
                        </div>
                        <button type="submit" name="add_product" class="btn btn-primary rounded-3 w-100 fw-bold py-2 shadow-sm transition-hover" 
                                style="background-color: #3B82F6; border: none;">
                            <i class="bi bi-save me-2"></i> Add Product
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Product List -->
        <div class="col-lg-8">
            <div class="card admin-card rounded-4 h-100 overflow-hidden shadow-lg">
                <div class="card-header border-bottom border-light-subtle border-opacity-25 py-3 px-4 d-flex justify-content-between align-items-center" style="background-color: transparent;">
                    <h5 class="fw-bold mb-0 text-body"><i class="bi bi-list-ul me-2 text-primary"></i>Product Inventory</h5>
                    <span class="badge rounded-pill px-3" style="background-color: #D1D5DB; color: #374151; border: 1px solid #4B5563;"><?php echo count($products); ?> Items</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark-custom align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="py-3 ps-4 text-uppercase small fw-bold" style="width: 50px;">ID</th>
                                    <th class="py-3 text-uppercase small fw-bold">Product Details</th>
                                    <th class="py-3 text-uppercase small fw-bold">Price</th>
                                    <th class="py-3 text-uppercase small fw-bold">Stock Status</th>
                                    <th class="py-3 text-uppercase small fw-bold text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr class="hover-row transition-hover">
                                        <td class="ps-4 small" style="color: #6B7280;">#<?php echo $product['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center me-3 border border-light-subtle border-opacity-25 bg-white" style="width: 48px; height: 48px; min-width: 48px;">
                                                    <?php
    $is_url = strpos($product['image'], 'http') === 0;
    if (!empty($product['image']) && ($is_url || file_exists('../assets/images/' . $product['image']))):
?>
                                                        <img src="<?php echo $is_url ? htmlspecialchars($product['image']) : '../assets/images/' . htmlspecialchars($product['image']); ?>" alt="Product" class="rounded-3" style="width: 100%; height: 100%; object-fit: cover;">
                                                    <?php
    else: ?>
                                                        <i class="bi bi-box text-secondary"></i>
                                                    <?php
    endif; ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-body mb-0 text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($product['name']); ?></div>
                                                    <div class="small text-truncate" style="color: #6B7280; max-width: 200px;">
                                                        <?php echo htmlspecialchars($product['category'] ?? '-'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-bold text-body">₹<?php echo number_format($product['price'], 2); ?></td>
                                        <td>
                                            <?php if ($product['stock'] > 10): ?>
                                                <span class="badge rounded-pill px-2" style="background-color: rgba(16, 185, 129, 0.2); color: #10B981; border: 1px solid rgba(16, 185, 129, 0.3);">
                                                    <?php echo $product['stock']; ?> in stock
                                                </span>
                                            <?php
    elseif ($product['stock'] > 0): ?>
                                                <span class="badge rounded-pill px-2" style="background-color: rgba(245, 158, 11, 0.2); color: #F59E0B; border: 1px solid rgba(245, 158, 11, 0.3);">
                                                    Low: <?php echo $product['stock']; ?>
                                                </span>
                                            <?php
    else: ?>
                                                <span class="badge rounded-pill px-2" style="background-color: rgba(239, 68, 68, 0.2); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.3);">Out of Stock</span>
                                            <?php
    endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="?delete=<?php echo $product['id']; ?>" 
                                               class="btn btn-outline-danger btn-sm rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm hover-scale"
                                               style="width: 32px; height: 32px; padding: 0;"
                                               onclick="return confirm('Are you sure you want to delete this product?')"
                                               title="Delete Product">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php
endforeach; ?>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="bi bi-inbox display-4 d-block mb-3" style="color: #D1D5DB;"></i>
                                            <span class="text-secondary">No products found. Add one to get started!</span>
                                        </td>
                                    </tr>
                                <?php
endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

