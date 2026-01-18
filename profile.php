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

<div class="container-fluid px-3 px-lg-5 py-5" style="background-color: #0B0B0E; min-height: 100vh;">
    <div class="row g-4 justify-content-center">
        <!-- 
            LEFT COLUMN: User Profile Card 
            Premium Dark Card | Vertically Centered Content
        -->
        <div class="col-lg-3 col-md-5">
            <div class="card border-0 h-100" style="background-color: #14161A; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
                <div class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center">
                    
                    <!-- Avatar -->
                    <div class="position-relative mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-lg" 
                             style="width: 120px; height: 120px; background: linear-gradient(135deg, #7C3AED, #A78BFA); color: #fff; font-size: 3rem; font-weight: 700; border: 4px solid #14161A;">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                        <span class="position-absolute bottom-0 end-0 p-2 rounded-circle border border-dark" style="background-color: #10B981; width: 20px; height: 20px;"></span>
                    </div>

                    <!-- User Details -->
                    <h3 class="mb-1" style="color: #E5E7EB; font-weight: 700; letter-spacing: -0.5px;"><?php echo htmlspecialchars($user['name']); ?></h3>
                    <p class="mb-3" style="color: #9CA3AF; font-size: 0.95rem;"><?php echo htmlspecialchars($user['email']); ?></p>

                    <!-- Role Badge -->
                    <div class="mb-4">
                        <span class="px-3 py-1 rounded-pill" style="background-color: rgba(124, 58, 237, 0.15); color: #A78BFA; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                            <?php echo $user['role'] === 'admin' ? 'Administrator' : 'Verified Customer'; ?>
                        </span>
                    </div>

                    <!-- Stats Divider -->
                    <div class="w-100 my-3" style="height: 1px; background-color: #2D2D35;"></div>

                    <!-- Stats Row -->
                    <div class="row w-100 text-center mt-2">
                        <div class="col-6 border-end" style="border-color: #2D2D35 !important;">
                            <h4 class="mb-0" style="color: #E5E7EB; font-weight: 700;"><?php echo count($user_orders); ?></h4>
                            <small style="color: #6B7280; text-transform: uppercase; font-size: 0.7rem; font-weight: 600;">Orders</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0" style="color: #E5E7EB; font-weight: 700;"><?php echo get_wishlist_count(); ?></h4>
                            <small style="color: #6B7280; text-transform: uppercase; font-size: 0.7rem; font-weight: 600;">Wishlist</small>
                        </div>
                    </div>

                    <!-- Logout Button (Mobile Only) -->
                    <div class="d-md-none w-100 mt-4">
                        <a href="logout.php" class="btn w-100" style="background-color: rgba(239, 68, 68, 0.1); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.2); font-weight: 600;">
                            Sign Out
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 
            RIGHT COLUMN: Settings & History 
            Stacked Layout | Consistent styling
        -->
        <div class="col-lg-8 col-md-7">
            
            <!-- SECTION 1: ACCOUNT SETTINGS -->
            <div class="card border-0 mb-4" style="background-color: #14161A; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom" style="border-color: #2D2D35 !important; padding-bottom: 20px;">
                        <h4 class="mb-0" style="color: #E5E7EB; font-weight: 700;">Account Settings</h4>
                        <i class="bi bi-gear-fill" style="color: #6B7280; font-size: 1.5rem;"></i>
                    </div>

                    <?php if ($success): ?>
                        <div class="alert border-0 d-flex align-items-center rounded-3 mb-4" style="background-color: rgba(16, 185, 129, 0.1); color: #10B981;">
                            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert border-0 d-flex align-items-center rounded-3 mb-4" style="background-color: rgba(239, 68, 68, 0.1); color: #EF4444;">
                            <i class="bi bi-exclamation-circle-fill me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase" style="color: #9CA3AF; font-weight: 600; letter-spacing: 0.5px;">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text border-0" style="background-color: #0B0B0E; color: #6B7280;">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" name="name" class="form-control border-0 px-3 py-2" 
                                       style="background-color: #0B0B0E; color: #E5E7EB; font-weight: 500;" 
                                       value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-uppercase" style="color: #9CA3AF; font-weight: 600; letter-spacing: 0.5px;">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text border-0" style="background-color: #0B0B0E; color: #6B7280;">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" name="email" class="form-control border-0 px-3 py-2" 
                                       style="background-color: #0B0B0E; color: #E5E7EB; font-weight: 500;" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>
                        <div class="col-12 text-end mt-4">
                            <button type="submit" name="update_profile" class="btn px-5 py-2 rounded-pill shadow-lg" 
                                    style="background-color: #7C3AED; color: white; font-weight: 600; letter-spacing: 0.5px; border: none; transition: all 0.2s;">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SECTION 2: ORDER HISTORY -->
            <div class="card border-0" style="background-color: #14161A; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom" style="border-color: #2D2D35 !important; padding-bottom: 20px;">
                        <h4 class="mb-0" style="color: #E5E7EB; font-weight: 700;">Order History</h4>
                        <span class="badge rounded-pill" style="background-color: rgba(124, 58, 237, 0.1); color: #A78BFA;">
                            <?php echo count($user_orders); ?> Orders
                        </span>
                    </div>

                    <?php if (!empty($user_orders)): ?>
                        <div class="table-responsive">
                            <table class="table align-middle" style="background: transparent;">
                                <thead>
                                    <tr>
                                        <th class="border-0 pb-3" style="color: #6B7280; font-size: 0.8rem; text-transform: uppercase;">Order ID</th>
                                        <th class="border-0 pb-3" style="color: #6B7280; font-size: 0.8rem; text-transform: uppercase;">Date</th>
                                        <th class="border-0 pb-3" style="color: #6B7280; font-size: 0.8rem; text-transform: uppercase;">Items</th>
                                        <th class="border-0 pb-3 text-end" style="color: #6B7280; font-size: 0.8rem; text-transform: uppercase;">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($user_orders as $order): ?>
                                        <?php 
                                        $items = json_decode($order['items'], true);
                                        $item_count = is_array($items) ? array_sum($items) : 0;
                                        ?>
                                        <tr style="border-color: #2D2D35;">
                                            <td class="py-3">
                                                <span style="color: #E5E7EB; font-weight: 600; font-family: monospace;">#<?php echo $order['id']; ?></span>
                                            </td>
                                            <td class="py-3">
                                                <div style="color: #9CA3AF; font-size: 0.9rem;">
                                                    <i class="bi bi-calendar3 me-2" style="color: #6B7280;"></i>
                                                    <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <span class="px-3 py-1 rounded-pill" style="background-color: #0B0B0E; color: #9CA3AF; border: 1px solid #2D2D35; font-size: 0.85rem;">
                                                    <?php echo $item_count; ?> Items
                                                </span>
                                            </td>
                                            <td class="text-end py-3">
                                                <a href="track-order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm px-3 rounded-pill" 
                                                   style="background-color: rgba(124, 58, 237, 0.1); color: #A78BFA; font-weight: 600; border: none;">
                                                    Track
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="mb-3" style="color: #2D2D35; font-size: 4rem;"><i class="bi bi-bag-x"></i></div>
                            <h5 style="color: #E5E7EB; font-weight: 700;">No orders found</h5>
                            <p class="mb-4" style="color: #6B7280;">You haven't placed any orders yet.</p>
                            <a href="products.php" class="btn px-4 py-2 rounded-pill" style="background-color: #7C3AED; color: white; font-weight: 600; border: none;">
                                Start Shopping
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
