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

<div class="admin-layout">
    
    <!-- ADMIN SIDEBAR -->
    <aside class="admin-sidebar saas-glass-card border-0 rounded-0 shadow-sm" style="position: sticky; top: 0; padding-top: 2rem;">
        <div class="mb-5 px-3">
            <span class="badge rounded-pill fw-bold mb-2" style="background: rgba(124, 58, 237, 0.1); color: var(--saas-primary);">
                ADMINISTRATION
            </span>
            <h5 class="fw-bold text-heading mb-0">Univault Control</h5>
            <small class="text-secondary">System version 2.4.0</small>
        </div>

        <nav class="d-flex flex-column gap-2 px-2">
            <a href="index.php" class="sidebar-menu-link active">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
            <a href="products.php" class="sidebar-menu-link">
                <i class="bi bi-box-seam"></i> Products
            </a>
            <a href="orders.php" class="sidebar-menu-link">
                <i class="bi bi-receipt"></i> Orders
            </a>
            <a href="users.php" class="sidebar-menu-link">
                <i class="bi bi-people"></i> Users
            </a>
            <a href="#" class="sidebar-menu-link text-muted">
                <i class="bi bi-tags"></i> Categories
            </a>
            <a href="#" class="sidebar-menu-link text-muted">
                <i class="bi bi-boxes"></i> Inventory
            </a>
            <a href="#" class="sidebar-menu-link text-muted">
                <i class="bi bi-graph-up"></i> Analytics
            </a>
            <a href="#" class="sidebar-menu-link text-muted">
                <i class="bi bi-cpu"></i> AI Logs
            </a>
            <div class="my-3 border-top" style="border-color: var(--saas-border-light) !important;"></div>
            <a href="#" class="sidebar-menu-link text-muted">
                <i class="bi bi-gear"></i> Settings
            </a>
            <a href="../" class="sidebar-menu-link mt-4" style="color: var(--saas-accent);">
                <i class="bi bi-box-arrow-up-right"></i> View Store
            </a>
        </nav>
    </aside>

    <!-- ADMIN MAIN CONTENT -->
    <main class="admin-main">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold text-heading mb-1">Overview Dashboard</h2>
                <p class="text-secondary mb-0">System performance and sales metrics</p>
            </div>
            <div>
                <button class="saas-btn-primary">
                    <i class="bi bi-download me-2"></i> Export Report
                </button>
            </div>
        </div>
        
        <!-- Stats Grid -->
        <div class="row g-4 mb-5">
            <!-- Revenue Stat -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-glass-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <p class="text-secondary fw-semibold text-uppercase small mb-0">Total Sales</p>
                        <div class="p-2 rounded" style="background: rgba(139, 92, 246, 0.1); color: #8B5CF6;">
                            <i class="bi bi-currency-rupee"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-heading mb-1 display-6">₹<?php echo number_format($stats['total_revenue'] / 1000, 1); ?>k</h2>
                    <p class="text-success small mb-0 fw-medium"><i class="bi bi-arrow-up-right"></i> +12% from last month</p>
                </div>
            </div>

            <!-- Orders Stat -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-glass-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <p class="text-secondary fw-semibold text-uppercase small mb-0">Total Orders</p>
                        <div class="p-2 rounded" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                            <i class="bi bi-cart-check"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-heading mb-1 display-6"><?php echo $stats['total_orders']; ?></h2>
                    <p class="text-success small mb-0 fw-medium"><i class="bi bi-arrow-up-right"></i> +8% from last month</p>
                </div>
            </div>

            <!-- Users Stat -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-glass-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <p class="text-secondary fw-semibold text-uppercase small mb-0">Active Users</p>
                        <div class="p-2 rounded" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-heading mb-1 display-6"><?php echo $stats['total_users']; ?></h2>
                    <p class="text-secondary small mb-0 fw-medium">Registered accounts</p>
                </div>
            </div>

            <!-- Products Sold Stat -->
            <div class="col-md-6 col-lg-3">
                <div class="saas-glass-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <p class="text-secondary fw-semibold text-uppercase small mb-0">Products Sold</p>
                        <div class="p-2 rounded" style="background: rgba(59, 130, 246, 0.1); color: #3B82F6;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-heading mb-1 display-6"><?php echo $stats['total_products'] * 3; /* Dummy multiplier for visual */?></h2>
                    <p class="text-secondary small mb-0 fw-medium">Items moving from inventory</p>
                </div>
            </div>
        </div>
        
        <!-- Charts Dashboard Placeholder Row -->
        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="saas-glass-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-heading mb-0">Revenue Analytics</h5>
                        <select class="form-select saas-input w-auto p-2 py-1">
                            <option>This Year</option>
                            <option>This Month</option>
                        </select>
                    </div>
                    <!-- Chart Graphic Placeholder -->
                    <div class="w-100 rounded d-flex align-items-center justify-content-center" style="height: 300px; background: rgba(0,0,0,0.02); border: 1px dashed var(--saas-border-light);">
                        <p class="text-secondary mb-0"><i class="bi bi-graph-up text-primary fs-3 d-block text-center mb-2"></i> Revenue Chart Graphic</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="saas-glass-card p-4 h-100">
                    <h5 class="fw-bold text-heading mb-4">Traffic Sources</h5>
                    <div class="w-100 rounded d-flex align-items-center justify-content-center" style="height: 200px; background: rgba(0,0,0,0.02); border: 1px dashed var(--saas-border-light);">
                        <p class="text-secondary mb-0"><i class="bi bi-pie-chart text-info fs-3 d-block text-center mb-2"></i> Traffic Pie Chart</p>
                    </div>
                    
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary small fw-medium text-uppercase">Organic</span>
                            <span class="fw-bold text-heading small">65%</span>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 65%"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary small fw-medium text-uppercase">Direct</span>
                            <span class="fw-bold text-heading small">35%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 35%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
