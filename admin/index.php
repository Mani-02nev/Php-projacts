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

            <a href="../" class="sidebar-menu-link mt-4" style="color: var(--saas-accent);">
                <i class="bi bi-box-arrow-up-right"></i> View Store
            </a>
        </nav>
    </aside>

    <!-- ADMIN MAIN CONTENT -->
    <main class="admin-main">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold text-heading mb-1">Management Admin Dashboard</h2>
                <p class="text-secondary mb-0">Visual dashboard data analytics perfect clean</p>
            </div>
            <div>
                <a href="export_sales_report.php" target="_blank" class="saas-btn-primary text-decoration-none d-inline-flex align-items-center">
                    <i class="bi bi-file-earmark-pdf me-2"></i> Export Sales Report
                </a>
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
                        <div>
                            <h5 class="fw-bold text-heading mb-0">Company Level Analytics</h5>
                            <p class="text-secondary small mb-0">Visual dashboard data analytics perfect clean</p>
                        </div>
                        <select class="form-select saas-input w-auto p-2 py-1">
                            <option>This Year</option>
                            <option>This Quarter</option>
                            <option>This Month</option>
                        </select>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background: rgba(139, 92, 246, 0.05); border: 1px solid rgba(139, 92, 246, 0.1);">
                                <p class="text-secondary small fw-bold text-uppercase mb-1">Gross Volume</p>
                                <h4 class="fw-bold mb-0" style="color: #8B5CF6;">₹<?php echo number_format($stats['total_revenue'] * 1.5, 2); ?></h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.1);">
                                <p class="text-secondary small fw-bold text-uppercase mb-1">Net Margin</p>
                                <h4 class="fw-bold mb-0" style="color: #10B981;">24.8%</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.1);">
                                <p class="text-secondary small fw-bold text-uppercase mb-1">Growth Rate</p>
                                <h4 class="fw-bold mb-0" style="color: #3B82F6;">+18.2%</h4>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Interactive Financial Chart -->
                    <div class="w-100 position-relative" style="height: 250px;">
                        <canvas id="financialChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="saas-glass-card p-4 h-100 d-flex flex-column">
                    <h5 class="fw-bold text-heading mb-4">User Demographics</h5>
                    
                    <div class="flex-grow-1 d-flex flex-column justify-content-center">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary small fw-medium text-uppercase d-flex align-items-center"><i class="bi bi-circle-fill me-2" style="color: #7C3AED; font-size: 0.5rem;"></i> Enterprise</span>
                            <span class="fw-bold text-heading small">45%</span>
                        </div>
                        <div class="progress mb-4" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar" role="progressbar" style="width: 45%; background-color: #7C3AED;"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary small fw-medium text-uppercase d-flex align-items-center"><i class="bi bi-circle-fill me-2" style="color: #3B82F6; font-size: 0.5rem;"></i> Business</span>
                            <span class="fw-bold text-heading small">35%</span>
                        </div>
                        <div class="progress mb-4" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar" role="progressbar" style="width: 35%; background-color: #3B82F6;"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary small fw-medium text-uppercase d-flex align-items-center"><i class="bi bi-circle-fill me-2" style="color: #10B981; font-size: 0.5rem;"></i> Retail</span>
                            <span class="fw-bold text-heading small">20%</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar" role="progressbar" style="width: 20%; background-color: #10B981;"></div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top" style="border-color: var(--saas-border-light) !important;">
                        <button class="btn btn-sm w-100 text-primary fw-bold" style="background: rgba(124, 58, 237, 0.1);">
                            View Full Report <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Load Chart.js for Financial Data Visuals -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('financialChart').getContext('2d');
    
    // Create gradient for the line chart
    const gradient = ctx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(124, 58, 237, 0.4)'); // saas-primary
    gradient.addColorStop(1, 'rgba(124, 58, 237, 0.0)');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'Gross Volume (₹)',
                data: [42000, 48000, 45000, 62000, 58000, 75000, 92000],
                borderColor: '#7C3AED',
                backgroundColor: gradient,
                borderWidth: 3,
                tension: 0.4, // smooth curves
                fill: true,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#7C3AED',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1F2937',
                    titleFont: { size: 13, family: 'Inter' },
                    bodyFont: { size: 14, weight: 'bold', family: 'Inter' },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                    ticks: {
                        callback: function(value) {
                            return '₹' + (value / 1000) + 'k';
                        },
                        font: { family: 'Inter', size: 11 },
                        color: '#6B7280'
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        font: { family: 'Inter', size: 12 },
                        color: '#6B7280'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            }
        }
    });
});
</script>
