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
        // Update user in CSV
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

<div class="container">
    <h1 class="section-title">My Profile</h1>
    
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; margin: 2rem 0;">
        <!-- Profile Sidebar -->
        <div>
            <div style="background: var(--white); padding: 2rem; border-radius: 8px; border: 2px solid var(--gray-200); text-align: center;">
                <!-- Avatar -->
                <div style="width: 120px; height: 120px; margin: 0 auto 1rem; background: var(--black); color: var(--white); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700;">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                
                <h2 style="margin-bottom: 0.5rem; font-size: 1.5rem;"><?php echo htmlspecialchars($user['name']); ?></h2>
                <p style="color: var(--gray-600); margin-bottom: 1rem;"><?php echo htmlspecialchars($user['email']); ?></p>
                
                <div style="padding: 1rem; background: var(--gray-100); border-radius: 4px; margin-bottom: 1rem;">
                    <p style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.25rem;">Account Type</p>
                    <p style="font-weight: 700; text-transform: uppercase;">
                        <?php echo $user['role'] === 'admin' ? '👑 Admin' : '👤 Customer'; ?>
                    </p>
                </div>
                
                <div style="padding: 1rem; background: var(--gray-100); border-radius: 4px;">
                    <p style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.25rem;">Total Orders</p>
                    <p style="font-weight: 700; font-size: 1.5rem;"><?php echo count($user_orders); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div>
            <!-- Profile Edit Form -->
            <div style="background: var(--white); padding: 2rem; border-radius: 8px; border: 2px solid var(--gray-200); margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Edit Profile</h3>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($user['name']); ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($user['email']); ?>"
                               required>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn btn-black">
                        Update Profile
                    </button>
                </form>
            </div>
            
            <!-- Previous Orders -->
            <div style="background: var(--white); padding: 2rem; border-radius: 8px; border: 2px solid var(--gray-200);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.25rem;">📦 Previous Orders</h3>
                    <span style="background: var(--black); color: var(--white); padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.875rem; font-weight: 600;">
                        <?php echo count($user_orders); ?> Orders
                    </span>
                </div>
                
                <?php if (!empty($user_orders)): ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php foreach ($user_orders as $order): ?>
                            <?php 
                            $items = json_decode($order['items'], true);
                            $item_count = is_array($items) ? array_sum($items) : 0;
                            ?>
                            <div style="border: 1px solid var(--gray-200); border-radius: 4px; padding: 1.5rem; transition: var(--transition);" 
                                 onmouseover="this.style.borderColor='var(--black)'" 
                                 onmouseout="this.style.borderColor='var(--gray-200)'">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                                    <div>
                                        <h4 style="font-size: 1rem; margin-bottom: 0.5rem;">
                                            Order #<?php echo $order['id']; ?>
                                        </h4>
                                        <p style="font-size: 0.875rem; color: var(--gray-600);">
                                            📅 <?php echo date('M d, Y - h:i A', strtotime($order['created_at'])); ?>
                                        </p>
                                    </div>
                                    <div style="text-align: right;">
                                        <p style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem;">
                                            <?php echo format_price($order['total_amount']); ?>
                                        </p>
                                        <span style="background: <?php 
                                            echo $order['status'] === 'delivered' ? '#00a650' : 
                                                ($order['status'] === 'shipped' ? '#2874f0' : 
                                                ($order['status'] === 'processing' ? '#ffa500' : '#ff4444')); 
                                        ?>; color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div style="padding: 1rem; background: var(--gray-100); border-radius: 4px; margin-bottom: 1rem;">
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.875rem;">
                                        <div>
                                            <p style="color: var(--gray-600); margin-bottom: 0.25rem;">Customer</p>
                                            <p style="font-weight: 600;"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                                        </div>
                                        <div>
                                            <p style="color: var(--gray-600); margin-bottom: 0.25rem;">Phone</p>
                                            <p style="font-weight: 600;"><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                                        </div>
                                        <div>
                                            <p style="color: var(--gray-600); margin-bottom: 0.25rem;">Items</p>
                                            <p style="font-weight: 600;">🛍️ <?php echo $item_count; ?> item(s)</p>
                                        </div>
                                        <div>
                                            <p style="color: var(--gray-600); margin-bottom: 0.25rem;">Delivery</p>
                                            <p style="font-weight: 600;"><?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['pincode']); ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="font-size: 0.875rem; color: var(--gray-600);">
                                    <strong>Address:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: var(--gray-600);">
                        <p style="font-size: 3rem; margin-bottom: 1rem;">🛒</p>
                        <p style="font-size: 1.25rem; margin-bottom: 1rem;">No orders yet</p>
                        <p style="margin-bottom: 1.5rem;">Start shopping to see your orders here!</p>
                        <a href="products.php" class="btn btn-black">Browse Products</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
