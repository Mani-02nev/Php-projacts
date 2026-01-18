<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check login
if (!is_logged_in()) {
    redirect('login.php');
}

// Get Order ID
if (!isset($_GET['id'])) {
    redirect('profile.php');
}

$order_id = $_GET['id'];
$orders = get_all_orders();
$order = null;

// Find order
foreach ($orders as $o) {
    if ($o['id'] == $order_id) {
        $order = $o;
        break;
    }
}

// Security: Check if order exists and belongs to user (unless admin)
if (!$order || ($order['user_id'] != $_SESSION['user_id'] && !is_admin())) {
    redirect('profile.php');
}

$page_title = 'Track Order #' . $order['id'];
include 'includes/header.php';

// Calculate Progress
$status_steps = ['pending', 'processing', 'shipped', 'delivered'];
$current_status_index = array_search($order['status'], $status_steps);
$progress_width = ($current_status_index / (count($status_steps) - 1)) * 100;

// Decode items
$items = json_decode($order['items'], true);
$all_products = get_all_products();
$order_products = [];
foreach ($items as $pid => $qty) {
    foreach ($all_products as $p) {
        if ($p['id'] == $pid) {
            $p['qty'] = $qty;
            $order_products[] = $p;
            break;
        }
    }
}
?>

<div class="container-fluid px-3 px-lg-5 py-5" style="background-color: #0B0B0E; min-height: 100vh;">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-5">
        <div class="mb-3 mb-md-0">
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="profile.php" class="text-decoration-none" style="color: #6B7280;">My Orders</a></li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: #9CA3AF;">#<?php echo $order['id']; ?></li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0" style="color: #E5E7EB; letter-spacing: -0.5px;">Tracking Details</h2>
        </div>
        <a href="profile.php" class="btn rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center justify-content-center" 
           style="background-color: rgba(255, 255, 255, 0.05); color: #E5E7EB; border: 1px solid #2D2D35; transition: all 0.2s;">
            <i class="bi bi-arrow-left me-2"></i> Back to Orders
        </a>
    </div>

    <div class="row g-4">
        <!-- LEFT COLUMN: Tracking Timeline -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg h-100" style="background-color: #14161A; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
                <div class="card-body p-4 p-md-5 position-relative">
                    <h5 class="fw-bold mb-5 pb-3 border-bottom" style="color: #E5E7EB; border-color: #2D2D35 !important;">
                        <i class="bi bi-geo-alt-fill me-2" style="color: #7C3AED;"></i> Shipment Status
                    </h5>
                    
                    <!-- Vertical Timeline Container -->
                    <div class="position-relative ms-2 ms-md-4" style="min-height: 400px;">
                        <!-- The Connecting Line -->
                        <div class="position-absolute top-0 bottom-0 start-0 border-start border-2" 
                             style="border-color: #2D2D35; left: 24px; z-index: 0;"></div>
                        
                        <?php 
                        $steps = [
                            'pending' => [
                                'title' => 'Order Placed',
                                'desc' => 'We have received your order.',
                                'icon' => 'bi-clipboard-check',
                                'date' => $order['created_at'] // Approximate date logic
                            ],
                            'processing' => [
                                'title' => 'Processing',
                                'desc' => 'Your order is being prepared.',
                                'icon' => 'bi-box-seam',
                                'date' => null
                            ],
                            'shipped' => [
                                'title' => 'Shipped',
                                'desc' => 'Your order is on the way.',
                                'icon' => 'bi-truck',
                                'date' => null
                            ],
                            'delivered' => [
                                'title' => 'Delivered',
                                'desc' => 'Package delivered safely.',
                                'icon' => 'bi-house-check',
                                'date' => null
                            ]
                        ];
                        
                        $keys = array_keys($steps);
                        foreach ($keys as $index => $key):
                            $step = $steps[$key];
                            $isCompleted = $index <= $current_status_index;
                            $isCurrent = $index === $current_status_index;
                            
                            // Visual States
                            $nodeBg = $isCompleted ? '#7C3AED' : '#14161A';
                            $nodeBorder = $isCompleted ? '#7C3AED' : '#2D2D35';
                            $iconColor = $isCompleted ? '#FFFFFF' : '#6B7280';
                            $titleColor = $isCompleted ? '#E5E7EB' : '#6B7280';
                            
                            if ($isCurrent) {
                                $nodeBg = '#14161A'; // Dark center
                                $nodeBorder = '#7C3AED';
                                $iconColor = '#7C3AED';
                                $titleColor = '#FFFFFF';
                            }
                        ?>
                            <!-- Timeline Step -->
                            <div class="d-flex align-items-start mb-5 position-relative" style="z-index: 1;">
                                <!-- Icon Node -->
                                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-lg flex-shrink-0" 
                                     style="width: 50px; height: 50px; background-color: <?php echo $nodeBg; ?>; border: 2px solid <?php echo $nodeBorder; ?>; margin-right: 1.5rem; <?php echo $isCurrent ? 'box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.2);' : ''; ?>">
                                    <i class="bi <?php echo $step['icon']; ?>" style="color: <?php echo $iconColor; ?>; font-size: 1.2rem;"></i>
                                </div>
                                
                                <!-- Content -->
                                <div class="pt-1 flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <h6 class="fw-bold mb-0 me-3" style="color: <?php echo $titleColor; ?>; font-size: 1.1rem;">
                                            <?php echo $step['title']; ?>
                                        </h6>
                                        <?php if ($isCurrent): ?>
                                            <span class="badge rounded-pill px-2 py-1" style="background-color: #7C3AED; font-size: 0.7rem; letter-spacing: 0.5px;">CURRENT STATUS</span>
                                        <?php elseif ($isCompleted): ?>
                                            <i class="bi bi-check-circle-fill" style="color: #10B981;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mb-0 small" style="color: #9CA3AF; max-width: 300px;"><?php echo $step['desc']; ?></p>
                                    <?php if ($step['date']): ?>
                                        <div class="mt-2 small" style="color: #6B7280;">
                                            <i class="bi bi-clock me-1"></i> <?php echo date('M d, Y h:i A', strtotime($step['date'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Info & Support -->
        <div class="col-lg-4">
            <!-- Delivery Address -->
            <div class="card border-0 mb-4" style="background-color: #14161A; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4 text-uppercase" style="color: #9CA3AF; font-size: 0.75rem; letter-spacing: 1px;">Delivery Address</h6>
                    <div class="d-flex gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                             style="width: 48px; height: 48px; background-color: rgba(124, 58, 237, 0.1); color: #7C3AED;">
                            <i class="bi bi-geo-alt-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold mb-1" style="color: #E5E7EB; font-size: 1rem;"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                            <div style="color: #9CA3AF; font-size: 0.9rem; line-height: 1.5;">
                                <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                                <?php echo htmlspecialchars($order['city']); ?> - <?php echo htmlspecialchars($order['pincode']); ?>
                            </div>
                            <div class="mt-3 pt-3 border-top" style="border-color: #2D2D35 !important;">
                                <div class="d-flex align-items-center" style="color: #9CA3AF;">
                                    <i class="bi bi-telephone me-2" style="color: #6B7280;"></i> 
                                    <span class="small"><?php echo htmlspecialchars($order['customer_phone']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Card -->
            <div class="card border-0 overflow-hidden" style="background-color: #14161A; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
                 <div class="card-body p-4 position-relative overflow-hidden">
                    <!-- Background Decoration -->
                    <div class="position-absolute top-0 end-0 p-3 opacity-25" style="transform: translate(20%, -20%);">
                        <i class="bi bi-headset" style="font-size: 8rem; color: #2D2D35;"></i>
                    </div>
                    
                    <h6 class="fw-bold mb-3" style="color: #E5E7EB;">Need Assistance?</h6>
                    <p class="small mb-4 position-relative" style="color: #9CA3AF; z-index: 1;">
                        If you have any issues with your delivery, our support team is available 24/7.
                    </p>
                    <a href="#" class="btn w-100 py-2 rounded-pill fw-bold position-relative" 
                       style="background-color: #2D2D35; color: #E5E7EB; border: 1px solid #374151; z-index: 1;">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTTOM SECTION: Order Items -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background-color: #14161A; border-radius: 20px;">
                <div class="card-header border-bottom py-3 px-4" style="background-color: transparent; border-color: #2D2D35 !important;">
                    <h5 class="fw-bold mb-0" style="color: #E5E7EB;">Items in this Order</h5>
                </div>
                <div class="card-body px-4">
                    <div class="row g-4">
                        <?php foreach ($order_products as $product): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center p-3 rounded-4" style="background-color: #0B0B0E; border: 1px solid #2D2D35;">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center me-3 overflow-hidden bg-white" 
                                         style="width: 70px; height: 70px; flex-shrink: 0;">
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="w-100 h-100 object-fit-contain" alt="Product">
                                        <?php else: ?>
                                            <i class="bi bi-image text-secondary fs-4"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="fw-bold mb-1 text-truncate" style="color: #E5E7EB;"><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge rounded-pill" style="background-color: #2D2D35; color: #9CA3AF;">Qty: <?php echo $product['qty']; ?></span>
                                            <span class="fw-bold" style="color: #7C3AED;">₹<?php echo number_format($product['price'] * $product['qty'], 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="d-flex justify-content-end align-items-center pt-4 mt-2 border-top" style="border-color: #2D2D35 !important;">
                        <span class="me-3" style="color: #9CA3AF;">Total Amount:</span>
                        <span class="display-6 fw-bold" style="color: #E5E7EB; font-size: 1.5rem;">₹<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
