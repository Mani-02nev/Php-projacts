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

<!-- Admin Dashboard -->
<style>
    body { background-color: #0E1116 !important; }
    .admin-card { background-color: #141821; border: 1px solid #1F2937; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5); }
    .stat-icon-wrapper { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.7); }
</style>

<div class="container-fluid px-4 py-5" style="min-height: 100vh;">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
        <div>
            <div class="d-flex align-items-center gap-3 mb-1">
                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background-color: #374151; color: #E5E7EB; border: 1px solid #4B5563;">
                    <i class="bi bi-shield-lock-fill me-1 text-warning"></i> ADMIN
                </span>
                <span class="text-secondary small">v2.4.0</span>
            </div>
            <h1 class="fw-bold mb-0 text-white display-6">Dashboard Overview</h1>
            <p class="text-secondary mb-0">System performance and management controls</p>
        </div>
        <div class="d-flex gap-3">
            <a href="../index.php" class="btn btn-outline-secondary rounded-pill fw-bold bg-dark text-white border-secondary">
                <i class="bi bi-box-arrow-up-right me-2"></i> View Store
            </a>
            <a href="../logout.php" class="btn btn-danger rounded-pill fw-bold">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </div>
    </div>
    
    <!-- Stats Grid -->
    <div class="row g-4 mb-5">
        <!-- Products Stat -->
        <div class="col-md-6 col-lg-3">
            <div class="card admin-card rounded-4 p-4 h-100 hover-lift">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="stat-icon-wrapper" style="background-color: rgba(59, 130, 246, 0.15); color: #3B82F6;">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <span class="badge fw-bold" style="background-color: rgba(59, 130, 246, 0.1); color: #3B82F6;">Inventory</span>
                </div>
                <h2 class="fw-bold text-white mb-1 display-5"><?php echo $stats['total_products']; ?></h2>
                <div class="d-flex align-items-center text-secondary small">
                    <span class="text-success fw-bold me-2"><i class="bi bi-arrow-up-short"></i> Live</span>
                    <span>Total Products</span>
                </div>
            </div>
        </div>
        
        <!-- Orders Stat -->
        <div class="col-md-6 col-lg-3">
            <div class="card admin-card rounded-4 p-4 h-100 hover-lift">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="stat-icon-wrapper" style="background-color: rgba(16, 185, 129, 0.15); color: #10B981;">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <span class="badge fw-bold" style="background-color: rgba(16, 185, 129, 0.1); color: #10B981;">Sales</span>
                </div>
                <h2 class="fw-bold text-white mb-1 display-5"><?php echo $stats['total_orders']; ?></h2>
                <div class="d-flex align-items-center text-secondary small">
                    <span class="text-white fw-bold me-2">All Time</span>
                    <span>Completed Orders</span>
                </div>
            </div>
        </div>
        
        <!-- Users Stat -->
        <div class="col-md-6 col-lg-3">
            <div class="card admin-card rounded-4 p-4 h-100 hover-lift">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="stat-icon-wrapper" style="background-color: rgba(245, 158, 11, 0.15); color: #F59E0B;">
                        <i class="bi bi-people"></i>
                    </div>
                    <span class="badge fw-bold" style="background-color: rgba(245, 158, 11, 0.1); color: #F59E0B;">Customers</span>
                </div>
                <h2 class="fw-bold text-white mb-1 display-5"><?php echo $stats['total_users']; ?></h2>
                <div class="d-flex align-items-center text-secondary small">
                    <span class="text-warning fw-bold me-2">Verified</span>
                    <span>Active Accounts</span>
                </div>
            </div>
        </div>
        
        <!-- Revenue Stat -->
        <div class="col-md-6 col-lg-3">
            <div class="card admin-card rounded-4 p-4 h-100 hover-lift">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="stat-icon-wrapper" style="background-color: rgba(139, 92, 246, 0.15); color: #8B5CF6;">
                        <i class="bi bi-currency-rupee"></i>
                    </div>
                    <span class="badge fw-bold" style="background-color: rgba(139, 92, 246, 0.1); color: #8B5CF6;">Revenue</span>
                </div>
                <h2 class="fw-bold text-white mb-1 display-5">₹<?php echo number_format($stats['total_revenue'] / 1000, 1); ?>k</h2>
                <div class="d-flex align-items-center text-secondary small">
                    <span class="text-white fw-bold me-2">Gross</span>
                    <span>Total Earnings</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <h4 class="fw-bold text-white mb-4">Management Modules</h4>
    <div class="row g-4">
        <div class="col-md-6 col-xl-4">
            <a href="products.php" class="text-decoration-none">
                <div class="card admin-card rounded-4 p-4 h-100 hover-lift border-0 position-relative overflow-hidden group">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                        <i class="bi bi-grid-3x3-gap display-1 text-primary"></i>
                    </div>
                    <div class="position-relative z-1">
                        <div class="mb-4">
                            <span class="badge bg-primary rounded-pill mb-2">Inventory</span>
                            <h3 class="fw-bold text-white mb-1">Product Management</h3>
                            <p class="text-secondary small mb-0">Add, edit, and categorize store items.</p>
                        </div>
                        <div class="d-flex align-items-center text-primary fw-bold">
                            Manage Products <i class="bi bi-arrow-right ms-2"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-6 col-xl-4">
            <a href="orders.php" class="text-decoration-none">
                <div class="card admin-card rounded-4 p-4 h-100 hover-lift border-0 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-10">
                        <i class="bi bi-receipt display-1 text-success"></i>
                    </div>
                    <div class="position-relative z-1">
                        <div class="mb-4">
                            <span class="badge bg-success rounded-pill mb-2">Orders</span>
                            <h3 class="fw-bold text-white mb-1">Order Fulfillment</h3>
                            <p class="text-secondary small mb-0">Track shipments and update statuses.</p>
                        </div>
                        <div class="d-flex align-items-center text-success fw-bold">
                            View Orders <i class="bi bi-arrow-right ms-2"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
