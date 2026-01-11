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
$user_orders = array_filter($all_orders, function($order) use ($user_id) {
    return $order['user_id'] == $user_id;
});

// Sort orders by date (newest first)
usort($user_orders, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

$success = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    
    if (empty($name) || empty($email)) {
        $error = 'Please fill in all fields';
    } else {
        foreach ($users as &$u) {
            if ($u['id'] == $user_id) {
                $u['name'] = $name;
                $u['email'] = $email;
                break;
            }
        }
        
        if (write_csv(USERS_CSV, $users)) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $user['name'] = $name;
            $user['email'] = $email;
            $success = 'Profile updated successfully!';
        } else {
            $error = 'Failed to update profile';
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row g-4">
        <!-- Profile Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="bg-primary p-4 text-center">
                    <div class="bg-white text-primary d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow-sm border border-4 border-white-50" style="width: 100px; height: 100px;">
                        <span class="display-4 fw-bold"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></span>
                    </div>
                    <h4 class="fw-bold text-white mb-1"><?php echo htmlspecialchars($user['name']); ?></h4>
                    <p class="text-white-50 small mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                        <div class="list-group-item p-4 border-0 border-bottom bg-transparent">
                            <label class="text-secondary small fw-bold text-uppercase d-block mb-1">Account Role</label>
                            <span class="badge bg-primary text-white border-0 px-3 py-2 rounded-pill fw-normal shadow-sm">
                                <?php echo $user['role'] === 'admin' ? '👑 Administrator' : '👤 Customer'; ?>
                            </span>
                        </div>
                        <div class="list-group-item p-4 border-0 bg-transparent">
                            <div class="row text-center g-0">
                                <div class="col-6 border-end border-secondary-subtle">
                                    <h3 class="fw-bold text-primary mb-0"><?php echo count($user_orders); ?></h3>
                                    <small class="text-secondary text-uppercase fw-bold" style="font-size: 0.65rem;">Orders</small>
                                </div>
                                <div class="col-6">
                                    <h3 class="fw-bold text-primary mb-0"><?php echo get_wishlist_count(); ?></h3>
                                    <small class="text-secondary text-uppercase fw-bold" style="font-size: 0.65rem;">Wishlist</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Edit Profile -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-body">
                <div class="card-body p-4 p-md-5">
                    <h4 class="fw-bold mb-4 border-bottom pb-3"><i class="bi bi-person-gear me-2 text-primary"></i> Account Settings</h4>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success rounded-4 border-0 shadow-sm small py-3 mb-4 animate__animated animate__fadeIn">
                            <i class="bi bi-check-circle me-2"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-4 border-0 shadow-sm small py-3 mb-4">
                            <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase ms-1">Full Name</label>
                            <input type="text" name="name" class="form-control rounded-pill border-light-subtle shadow-sm px-4 bg-body-tertiary" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase ms-1">Email Address</label>
                            <input type="email" name="email" class="form-control rounded-pill border-light-subtle shadow-sm px-4 bg-body-tertiary" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" name="update_profile" class="btn btn-primary rounded-pill px-5 fw-bold py-2 shadow transition-hover">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="card border-0 shadow-sm rounded-4 bg-body">
                <div class="card-body p-4 p-md-5">
                    <h4 class="fw-bold mb-4 border-bottom pb-3"><i class="bi bi-box-seam me-2 text-primary"></i> Order History</h4>
                    
                    <?php if (!empty($user_orders)): ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($user_orders as $order): ?>
                                <?php 
                                $items = json_decode($order['items'], true);
                                $item_count = is_array($items) ? array_sum($items) : 0;
                                ?>
                                <div class="card border border-light-subtle rounded-4 p-4 transition-hover bg-body-tertiary shadow-sm">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                        <div>
                                            <h6 class="fw-bold mb-1">Order #<?php echo $order['id']; ?></h6>
                                            <p class="text-secondary small mb-0"><i class="bi bi-calendar3 me-1"></i> <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                                        </div>
                                        <div class="text-md-end">
                                            <h5 class="fw-bold text-primary mb-1">₹<?php echo number_format($order['total_amount'], 0); ?></h5>
                                            <span class="badge rounded-pill <?php 
                                                echo $order['status'] === 'delivered' ? 'bg-success-subtle text-success' : 
                                                    ($order['status'] === 'shipped' ? 'bg-primary-subtle text-primary' : 
                                                    ($order['status'] === 'processing' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger')); 
                                            ?> px-3 py-2 text-uppercase" style="font-size: 0.65rem;">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 p-3 bg-body rounded-3 border border-light-subtle small text-secondary">
                                        <div class="row g-3">
                                            <div class="col-sm-6 col-md-3">
                                                <span class="d-block fw-bold mb-0">Qty</span>
                                                <span class="opacity-75">🛍️ <?php echo $item_count; ?> item(s)</span>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <span class="d-block fw-bold mb-0">City</span>
                                                <span class="opacity-75"><?php echo htmlspecialchars($order['city']); ?></span>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="d-block fw-bold mb-0">Shipping Address</span>
                                                <span class="opacity-75 text-truncate d-block"><?php echo htmlspecialchars($order['shipping_address']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="display-3 text-muted mb-4 opacity-25"><i class="bi bi-cart"></i></div>
                            <h5 class="fw-bold">No orders found</h5>
                            <p class="text-secondary mb-4">You haven't placed any orders yet.</p>
                            <a href="products.php" class="btn btn-outline-primary rounded-pill px-4 fw-bold">Start Shopping</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
