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

<div class="container">
    <h1 class="section-title">Admin Dashboard</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin: 3rem 0;">
        <div style="background: var(--black); color: var(--white); padding: 2rem; border-radius: 8px; text-align: center;">
            <h3 style="font-size: 2.5rem; margin-bottom: 0.5rem;"><?php echo $stats['total_products']; ?></h3>
            <p style="color: var(--gray-300);">Total Products</p>
        </div>
        
        <div style="background: var(--black); color: var(--white); padding: 2rem; border-radius: 8px; text-align: center;">
            <h3 style="font-size: 2.5rem; margin-bottom: 0.5rem;"><?php echo $stats['total_orders']; ?></h3>
            <p style="color: var(--gray-300);">Total Orders</p>
        </div>
        
        <div style="background: var(--black); color: var(--white); padding: 2rem; border-radius: 8px; text-align: center;">
            <h3 style="font-size: 2.5rem; margin-bottom: 0.5rem;"><?php echo $stats['total_users']; ?></h3>
            <p style="color: var(--gray-300);">Total Users</p>
        </div>
        
        <div style="background: var(--black); color: var(--white); padding: 2rem; border-radius: 8px; text-align: center;">
            <h3 style="font-size: 2.5rem; margin-bottom: 0.5rem;"><?php echo format_price($stats['total_revenue']); ?></h3>
            <p style="color: var(--gray-300);">Total Revenue</p>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin: 3rem 0;">
        <a href="products.php" class="btn btn-black" style="padding: 2rem; text-align: center; font-size: 1.2rem;">
            Manage Products
        </a>
        <a href="orders.php" class="btn btn-black" style="padding: 2rem; text-align: center; font-size: 1.2rem;">
            Manage Orders
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
