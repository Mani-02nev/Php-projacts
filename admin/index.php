<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is admin
if (!is_admin()) {
    redirect('../login.php');
}

$page_title = 'Admin Dashboard';

// Get statistics
$stats = get_stats();

include '../includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-bold mb-1 text-dark">Admin Console</h1>
            <p class="text-secondary mb-0">System overview and management</p>
        </div>
        <div class="text-end">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold">
                <i class="bi bi-shield-check me-1"></i> Admin Authorized
            </span>
        </div>
    </div>
    
    <!-- Stats Grid -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center transition-hover">
                <div class="bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-box-seam fs-3 text-primary"></i>
                </div>
                <h2 class="fw-bold text-dark mb-1"><?php echo $stats['total_products']; ?></h2>
                <p class="text-secondary small text-uppercase fw-bold mb-0">Products</p>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center transition-hover">
                <div class="bg-success-subtle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-cart-check fs-3 text-success"></i>
                </div>
                <h2 class="fw-bold text-dark mb-1"><?php echo $stats['total_orders']; ?></h2>
                <p class="text-secondary small text-uppercase fw-bold mb-0">Total Orders</p>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center transition-hover">
                <div class="bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-people fs-3 text-warning"></i>
                </div>
                <h2 class="fw-bold text-dark mb-1"><?php echo $stats['total_users']; ?></h2>
                <p class="text-secondary small text-uppercase fw-bold mb-0">Total Users</p>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center transition-hover">
                <div class="bg-info-subtle rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-currency-rupee fs-3 text-info"></i>
                </div>
                <h2 class="fw-bold text-dark mb-1">₹<?php echo number_format($stats['total_revenue'], 0); ?></h2>
                <p class="text-secondary small text-uppercase fw-bold mb-0">Total Revenue</p>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-md-6">
            <a href="products.php" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center transition-hover bg-dark text-white">
                    <i class="bi bi-grid-3x3-gap display-4 mb-3"></i>
                    <h4 class="fw-bold mb-0">Product Inventory</h4>
                    <p class="opacity-75 small">Add, edit, or remove products</p>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="orders.php" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center transition-hover bg-primary text-white">
                    <i class="bi bi-receipt display-4 mb-3"></i>
                    <h4 class="fw-bold mb-0">Order Tracking</h4>
                    <p class="opacity-75 small">Manage customers and fulfill orders</p>
                </div>
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
