<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

$page_title = 'My Profile';

// Get user data
$user_id = $_SESSION['user_id'];
$users = read_csv(USERS_CSV);
$user = null;

foreach ($users as $u) {
    if ($u['id'] == $user_id) {
        $user = $u;
        // Ensure session email is synced if it was missing
        if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
            $_SESSION['user_email'] = $u['email'];
        }
        break;
    }
}

// Safety check: if user data not found in CSV but session exists
if (!$user) {
    session_destroy();
    redirect('login.php');
}

// Get user's orders
$all_orders = get_all_orders();
$user_orders = array_filter($all_orders, function ($order) use ($user_id) {
    return $order['user_id'] == $user_id;
});

// Sort orders by date (newest first)
usort($user_orders, function ($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

$success = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone'] ?? '');

    if (empty($name) || empty($email)) {
        $error = 'Please fill in all fields';
    }
    else {
        foreach ($users as &$u) {
            if ($u['id'] == $user_id) {
                $u['name'] = $name;
                $u['email'] = $email;
                // Add phone update logic if needed
                break;
            }
        }

        if (write_csv(USERS_CSV, $users)) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $user['name'] = $name;
            $user['email'] = $email;
            $success = 'Profile updated successfully!';
        }
        else {
            $error = 'Failed to update profile';
        }
    }
}

include 'includes/header.php';
?>

<div class="saas-container" style="margin-top: 4rem; margin-bottom: 4rem;">
    <div class="profile-dashboard-layout">
        
        <!-- SIDEBAR -->
        <aside class="profile-sidebar">
            <div class="saas-glass-card profile-sidebar-card">
                <div class="profile-avatar-section">
                    <div class="profile-avatar-wrapper shadow-sm">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                    
                    <h5 class="profile-card-user-name" title="<?php echo htmlspecialchars($user['name']); ?>"><?php echo htmlspecialchars($user['name']); ?></h5>
                    <p class="profile-card-user-email" title="<?php echo htmlspecialchars($user['email']); ?>"><?php echo htmlspecialchars($user['email']); ?></p>
                    <span class="verified-badge">
                        Verified Customer
                    </span>
                </div>

                <div class="profile-nav-section">
                    <nav class="d-flex flex-column gap-1">
                        <a href="profile.php" class="sidebar-menu-link active">
                            <i class="bi bi-grid-1x2"></i> <span>Dashboard</span>
                        </a>
                        <a href="#orders" class="sidebar-menu-link">
                            <i class="bi bi-box-seam"></i> <span>Orders</span>
                        </a>
                        <a href="wishlist.php" class="sidebar-menu-link">
                            <i class="bi bi-heart"></i> <span>Wishlist</span>
                        </a>
                        <a href="#settings" class="sidebar-menu-link">
                            <i class="bi bi-gear"></i> <span>Account Settings</span>
                        </a>
                        <a href="#" class="sidebar-menu-link">
                            <i class="bi bi-geo-alt"></i> <span>Saved Addresses</span>
                        </a>
                        <a href="#" class="sidebar-menu-link">
                            <i class="bi bi-credit-card"></i> <span>Payment Methods</span>
                        </a>
                        <div class="my-3 border-top" style="border-color: var(--saas-border-light) !important;"></div>
                        <a href="logout.php" class="sidebar-menu-link text-danger">
                            <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
                        </a>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="profile-main-content">
            
            <div class="mb-5 d-flex justify-content-between align-items-end">
                <div>
                    <h1 class="display-5 fw-bold mb-1">Welcome back, <?php echo htmlspecialchars($user['name']); ?></h1>
                    <p class="text-secondary"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="text-end d-none d-md-block">
                    <span class="badge rounded-pill bg-white text-primary border px-3 py-2 shadow-sm">
                        <i class="bi bi-shield-check me-1"></i> Account Verified
                    </span>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="saas-glass-card p-4 d-flex align-items-center">
                    <div class="p-3 rounded-circle me-3" style="background: rgba(124, 58, 237, 0.1); color: var(--saas-primary); width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-bag-check fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo count($user_orders); ?></h3>
                        <p class="text-secondary small fw-medium mb-0 text-uppercase">Total Orders</p>
                    </div>
                </div>
                
                <div class="saas-glass-card p-4 d-flex align-items-center">
                    <div class="p-3 rounded-circle me-3" style="background: rgba(59, 130, 246, 0.1); color: var(--saas-accent); width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-heart fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo get_wishlist_count(); ?></h3>
                        <p class="text-secondary small fw-medium mb-0 text-uppercase">Wishlist Items</p>
                    </div>
                </div>

                <?php
$active_deliveries = 0;
foreach ($user_orders as $uo) {
    if ($uo['status'] === 'processing' || $uo['status'] === 'shipped') {
        $active_deliveries++;
    }
}
?>
                <div class="saas-glass-card p-4 d-flex align-items-center">
                    <div class="p-3 rounded-circle me-3" style="background: rgba(16, 185, 129, 0.1); color: #10B981; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-truck fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo $active_deliveries; ?></h3>
                        <p class="text-secondary small fw-medium mb-0 text-uppercase">Active Deliveries</p>
                    </div>
                </div>
            </div>

            <!-- Account Settings Card -->
            <div id="settings" class="saas-glass-card p-4 p-lg-5 mb-5">
                <h4 class="fw-bold mb-4">Account Settings</h4>

                <?php if ($success): ?>
                    <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center p-3" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                        <i class="bi bi-check-circle-fill me-3 fs-5"></i> <span class="fw-medium"><?php echo $success; ?></span>
                    </div>
                <?php
endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center p-3" style="background: rgba(239, 68, 68, 0.1); color: #DC2626;">
                        <i class="bi bi-exclamation-triangle-fill me-3 fs-5"></i> <span class="fw-medium"><?php echo $error; ?></span>
                    </div>
                <?php
endif; ?>

                <form method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="saas-label">Full Name</label>
                            <input type="text" name="name" class="form-control saas-input" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="saas-label">Email Address</label>
                            <input type="email" name="email" class="form-control saas-input" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="saas-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control saas-input" placeholder="+1 (555) 000-0000">
                        </div>
                        <div class="col-md-6">
                            <label class="saas-label">Password</label>
                            <input type="password" name="password" class="form-control saas-input" placeholder="••••••••">
                        </div>
                        <div class="col-12 text-end mt-4">
                            <button type="submit" name="update_profile" class="saas-btn-primary px-5 rounded-pill shadow">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Recent Orders Table -->
            <div id="orders" class="saas-glass-card p-0 mb-5 overflow-hidden">
                <div class="p-4 p-lg-5 border-bottom" style="border-color: var(--saas-border-light) !important;">
                    <h4 class="fw-bold mb-0">Recent Orders</h4>
                </div>

                <?php if (!empty($user_orders)): ?>
                    <div class="table-responsive">
                        <table class="saas-table mb-0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($user_orders as $order): ?>
                                    <?php
        $items = json_decode($order['items'], true);
        $item_count = is_array($items) ? array_sum($items) : 0;

        $status_class = 'bg-secondary';
        $status_text = ucfirst($order['status']);

        if ($order['status'] === 'processing')
            $status_class = 'bg-warning';
        else if ($order['status'] === 'shipped')
            $status_class = 'bg-primary';
        else if ($order['status'] === 'delivered')
            $status_class = 'bg-success';
        else if ($order['status'] === 'cancelled')
            $status_class = 'bg-danger';
?>
                                    <tr>
                                        <td><span class="fw-bold text-dark">#<?php echo $order['id']; ?></span></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td><span class="text-secondary"><?php echo $item_count; ?> Items</span></td>
                                        <td><span class="fw-bold">₹<?php echo number_format($order['total_amount'], 2); ?></span></td>
                                        <td>
                                            <span class="badge rounded-pill <?php echo $status_class; ?> bg-opacity-10 text-<?php echo str_replace('bg-', '', $status_class); ?> px-3 py-2 fw-medium">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="track-order.php?id=<?php echo $order['id']; ?>" class="saas-btn-outline btn-sm py-2 px-4 shadow-sm">
                                                Track Order
                                            </a>
                                        </td>
                                    </tr>
                                <?php
    endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php
else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-box2 text-muted display-4 d-block mb-3"></i>
                        <h5 class="fw-bold">No orders yet</h5>
                        <p class="text-secondary mb-4">Start shopping to see your orders here.</p>
                        <a href="products.php" class="saas-btn-primary rounded-pill">Explore Store</a>
                    </div>
                <?php
endif; ?>
            </div>

        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
