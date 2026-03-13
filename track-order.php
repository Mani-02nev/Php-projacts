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
if (is_array($items)) {
    foreach ($items as $pid => $qty) {
        foreach ($all_products as $p) {
            if ($p['id'] == $pid) {
                $p['qty'] = $qty;
                $order_products[] = $p;
                break;
            }
        }
    }
}
?>
<style>
/* Custom track order styles */
.saas-glass-card {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
}

.timeline-horizontal {
    display: flex;
    flex-direction: column;
}
@media (min-width: 768px) {
    .timeline-horizontal {
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-start;
        position: relative;
    }
    .timeline-horizontal::before {
        content: '';
        position: absolute;
        top: 24px;
        left: 40px;
        right: 40px;
        height: 2px;
        background: #E5E7EB;
        z-index: 0;
    }
}
.timeline-step {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: row;
    align-items: center;
    margin-bottom: 2rem;
}
@media (min-width: 768px) {
    .timeline-step {
        flex-direction: column;
        align-items: center;
        text-align: center;
        margin-bottom: 0;
        width: 20%;
    }
    .timeline-step .track-line-mobile {
        display: none;
    }
}
@media (max-width: 767.98px) {
    .timeline-step::before {
        content: '';
        position: absolute;
        top: 50px;
        left: 24px;
        width: 2px;
        height: calc(100% + 10px);
        background: #E5E7EB;
        z-index: -1;
    }
    .timeline-step:last-child::before {
        display: none;
    }
}
</style>

<div class="saas-container py-5">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="profile.php" class="text-decoration-none" style="color: #6366F1;">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="profile.php#orders" class="text-decoration-none" style="color: #6366F1;">Orders</a></li>
                    <li class="breadcrumb-item active text-secondary" aria-current="page">Track #<?php echo $order['id']; ?></li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0" style="color: #312E81; letter-spacing: -0.5px;">Order Tracking</h2>
        </div>
        <a href="profile.php#orders" class="btn rounded-pill px-4 py-2 fw-medium mt-3 mt-md-0 d-inline-flex align-items-center" 
           style="background-color: #ffffff; color: #4338CA; border: 1px solid rgba(99, 102, 241, 0.2); box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: all 0.2s;" onmouseover="this.style.background='rgba(99, 102, 241, 0.05)'" onmouseout="this.style.background='#ffffff'">
            <i class="bi bi-arrow-left me-2"></i> Back to Orders
        </a>
    </div>

    <!-- 1. Order Summary Card -->
    <div class="saas-glass-card p-4 p-md-5 mb-5 border-0">
        <h5 class="fw-bold mb-4" style="color: #312E81;"><i class="bi bi-receipt me-2" style="color: #6366F1;"></i>Order Summary</h5>
        
        <div class="row g-4 d-flex">
            <div class="col-md-4 col-lg-2">
                <p class="text-secondary small fw-bold text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Order ID</p>
                <p class="fw-bold text-dark fs-5 mb-0">#<?php echo $order['id']; ?></p>
            </div>
            <div class="col-md-4 col-lg-2">
                <p class="text-secondary small fw-bold text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Order Date</p>
                <p class="fw-semibold text-dark mb-0"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
            </div>
            <?php
$item_count_total = 0;
foreach ($order_products as $op) {
    $item_count_total += $op['qty'];
}
?>
            <div class="col-md-4 col-lg-2">
                <p class="text-secondary small fw-bold text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Items</p>
                <p class="fw-semibold text-dark mb-0"><?php echo $item_count_total; ?> <?php echo $item_count_total === 1 ? 'item' : 'items'; ?></p>
            </div>
            <div class="col-md-4 col-lg-2">
                <p class="text-secondary small fw-bold text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Price</p>
                <p class="fw-bold fs-5 mb-0" style="color: #4338CA;">₹<?php echo number_format($order['total_amount'], 2); ?></p>
            </div>
            <div class="col-md-4 col-lg-2">
                <p class="text-secondary small fw-bold text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Payment Method</p>
                <p class="fw-semibold text-dark mb-0 d-flex align-items-center">
                    <i class="bi bi-credit-card-2-front me-2" style="color: #6366F1;"></i> Online Secure
                </p>
            </div>
            <div class="col-md-4 col-lg-2">
                <p class="text-secondary small fw-bold text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Shipping To</p>
                <p class="text-dark small mb-0 fw-medium line-clamp-2" title="<?php echo htmlspecialchars($order['shipping_address']); ?>">
                    <?php echo htmlspecialchars(explode(',', $order['shipping_address'])[0]); ?>, <?php echo htmlspecialchars($order['city']); ?>
                </p>
            </div>
        </div>
    </div>

    <div class="row g-5">
        <!-- Delivery Progress and Details (Left Side / Full Width) -->
        <div class="col-12 col-xl-8">
            
            <!-- 2. Delivery Progress Timeline -->
            <div class="saas-glass-card p-4 p-md-5 mb-5 border-0">
                <h5 class="fw-bold mb-5" style="color: #312E81;"><i class="bi bi-truck me-2" style="color: #6366F1;"></i>Delivery Progress</h5>
                
                <?php
// Enhanced steps including 'out_for_delivery'
$tracking_steps = [
    'pending' => ['title' => 'Order Placed', 'icon' => 'bi-clipboard-check', 'date' => $order['created_at']],
    'processing' => ['title' => 'Processing', 'icon' => 'bi-box-seam', 'date' => null],
    'shipped' => ['title' => 'Shipped', 'icon' => 'bi-truck', 'date' => null],
    'out_for_delivery' => ['title' => 'Out for Delivery', 'icon' => 'bi-bicycle', 'date' => null],
    'delivered' => ['title' => 'Delivered', 'icon' => 'bi-house-check', 'date' => null]
];

// Map current db status to internal tracking steps
$mapped_index = 0;
if ($order['status'] === 'processing')
    $mapped_index = 1;
else if ($order['status'] === 'shipped') {
    // Artificially show out for delivery based on time elapsed maybe? 
    // Let's just hardcode 2 for shipped.
    $mapped_index = 2;
}
else if ($order['status'] === 'delivered')
    $mapped_index = 4;

if ($order['status'] === 'cancelled') {
    $mapped_index = -1;
}
?>
                
                <?php if ($order['status'] === 'cancelled'): ?>
                    <div class="alert alert-danger mb-0 rounded-4 border-0 p-4 text-center">
                        <i class="bi bi-x-circle display-4 mb-3 d-block text-danger"></i>
                        <h4 class="fw-bold text-danger">Order Cancelled</h4>
                        <p class="mb-0 text-dark">This order has been cancelled and will not be delivered.</p>
                    </div>
                <?php
else: ?>
                    <div class="timeline-horizontal">
                        <!-- Progress fill line for desktop -->
                        <div class="d-none d-md-block position-absolute" style="top: 24px; left: 40px; height: 2px; border-radius: 2px; background: #6366F1; z-index: 0; width: <?php echo($mapped_index / 4) * 100; ?>%; transition: width 1s ease-in-out; box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);"></div>

                        <?php
    $keys = array_keys($tracking_steps);
    foreach ($keys as $index => $key):
        $step = $tracking_steps[$key];
        $isCompleted = $index <= $mapped_index;
        $isCurrent = $index === $mapped_index;
        $isFuture = $index > $mapped_index;

        $nodeBg = $isCompleted ? 'linear-gradient(135deg, #6366F1, #8B5CF6)' : '#FFFFFF';
        $nodeBorder = $isCompleted ? 'transparent' : '#E5E7EB';
        $iconClass = $step['icon'];
        $iconColor = $isCompleted ? '#FFFFFF' : '#9CA3AF';
        $textColor = $isCompleted ? '#1F2937' : '#9CA3AF';

        if ($isCompleted && !$isCurrent) {
            // Show check icons for completed past steps
            $iconClass = 'bi-check-lg';
        }

        $glowEffect = $isCurrent ? 'box-shadow: 0 0 0 6px rgba(99, 102, 241, 0.2), 0 10px 20px rgba(99, 102, 241, 0.3); transform: scale(1.1);' : 'box-shadow: 0 4px 6px rgba(0,0,0,0.05);';
        if ($isFuture)
            $glowEffect = '';
?>
                            <div class="timeline-step">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                                     style="width: 50px; height: 50px; background: <?php echo $nodeBg; ?>; border: 2px solid <?php echo $nodeBorder; ?>; <?php echo $glowEffect; ?> transition: all 0.3s ease;">
                                    <i class="bi <?php echo $iconClass; ?>" style="color: <?php echo $iconColor; ?>; font-size: 1.3rem;"></i>
                                </div>
                                <div class="ms-4 ms-md-0 mt-md-3 pt-1 pt-md-0 d-flex flex-column align-items-start align-items-md-center">
                                    <h6 class="fw-bold mb-1" style="color: <?php echo $textColor; ?>; font-size: 0.95rem;"><?php echo $step['title']; ?></h6>
                                    <?php if ($step['date']): ?>
                                        <small class="text-secondary font-monospace" style="font-size: 0.8rem;"><?php echo date('M d, H:i', strtotime($step['date'])); ?></small>
                                    <?php
        elseif ($isCurrent): ?>
                                        <small class="fw-bold" style="color: #6366F1; font-size: 0.8rem;">IN PROGRESS</small>
                                    <?php
        else: ?>
                                        <small class="text-secondary" style="font-size: 0.8rem; visibility: hidden;">TBD</small>
                                    <?php
        endif; ?>
                                </div>
                            </div>
                        <?php
    endforeach; ?>
                    </div>
                <?php
endif; ?>
            </div>

            <!-- 4. Order Item List -->
            <div class="saas-glass-card p-0 mb-5 border-0 overflow-hidden">
                <div class="p-4 p-md-5 border-bottom" style="border-color: rgba(0,0,0,0.05) !important;">
                    <h5 class="fw-bold mb-0" style="color: #312E81;"><i class="bi bi-cart3 me-2" style="color: #6366F1;"></i>Purchased Items</h5>
                </div>
                
                <div class="p-4 p-md-5 bg-white bg-opacity-50">
                    <div class="row g-4">
                        <?php foreach ($order_products as $product): ?>
                            <div class="col-md-6 col-lg-12 col-xl-6">
                                <div class="d-flex p-3 rounded-4 bg-white shadow-sm" style="border: 1px solid rgba(0,0,0,0.05); transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                                    <div class="rounded-3 overflow-hidden bg-white me-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; flex-shrink: 0; background-color: #f8f9fa;">
                                        <?php if (!empty($product['image']) && file_exists('assets/images/' . $product['image'])): ?>
                                            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" class="w-100 h-100 object-fit-contain p-2" alt="Product">
                                        <?php
    elseif (!empty($product['image']) && strpos($product['image'], 'http') === 0): ?>
                                            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="w-100 h-100 object-fit-contain p-2" alt="Product">
                                        <?php
    else: ?>
                                            <i class="bi bi-image text-secondary fs-3"></i>
                                        <?php
    endif; ?>
                                    </div>
                                    <div class="flex-grow-1 d-flex flex-column justify-content-center">
                                        <h6 class="fw-bold mb-1 text-dark text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <p class="text-secondary small mb-2"><?php echo htmlspecialchars($product['category'] ?? 'Category'); ?></p>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <span class="badge rounded-pill fw-medium px-2 py-1" style="background: rgba(99, 102, 241, 0.1); color: #4338CA;">Qty: <?php echo $product['qty']; ?></span>
                                            <span class="fw-bold text-dark fs-6" style="color: #312E81 !important;">₹<?php echo number_format($product['price'] * $product['qty'], 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Side column (Delivery details & Support) -->
        <div class="col-12 col-xl-4">
            <!-- 3. Delivery Details Section -->
            <div class="saas-glass-card p-4 p-md-4 mb-4 border-0 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-4 opacity-10" style="z-index: 0;">
                    <i class="bi bi-box2-heart display-1 text-primary"></i>
                </div>
                
                <h5 class="fw-bold mb-4 position-relative z-1" style="color: #312E81;">Delivery Details</h5>
                
                <div class="position-relative z-1">
                    <div class="mb-4">
                        <p class="text-secondary small fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Courier Partner</p>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 36px; height: 36px; background: linear-gradient(135deg, #F59E0B, #D97706);">
                                <i class="bi bi-lightning-fill small"></i>
                            </div>
                            <span class="fw-bold text-dark fs-6">Swift Logistics Inc.</span>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-secondary small fw-bold text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tracking ID</p>
                        <div class="d-flex align-items-center justify-content-between bg-white bg-opacity-75 px-3 py-2 rounded-3 border" style="border-color: rgba(99, 102, 241, 0.2) !important;">
                            <span class="fw-bold font-monospace" style="color: #4338CA; letter-spacing: 1px;">UNI-<?php echo str_pad($order['id'], 8, "0", STR_PAD_LEFT); ?>-X</span>
                            <button class="btn btn-sm text-secondary p-1" title="Copy Tracking ID" onclick="alert('Copied to clipboard!')" style="border: none; background: transparent;"><i class="bi bi-copy border border-secondary border-opacity-25 rounded p-1"></i></button>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <p class="text-secondary small fw-bold text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Estimated Delivery</p>
                        <?php
// Dummy estimate: order date + 3 days
$est_date = date('l, M d, Y', strtotime($order['created_at'] . ' + 3 days'));
?>
                        <p class="fw-bold fs-5 mb-0" style="color: #10B981;"><?php echo $est_date; ?></p>
                        <p class="text-secondary small fw-medium mt-1"><i class="bi bi-clock me-1"></i> By 9:00 PM</p>
                    </div>
                </div>
            </div>

            <!-- Support Card -->
            <div class="saas-glass-card p-4 p-md-4 border-0 text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(139, 92, 246, 0.05));">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 60px; height: 60px; background: rgba(255,255,255,0.9); color: #6366F1;">
                    <i class="bi bi-headset fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2" style="color: #312E81;">Need Help?</h5>
                <p class="text-secondary small mb-4" style="line-height: 1.6;">Have issues with your delivery or want to return an item? We are here 24/7.</p>
                <a href="ai-assistant.php" class="btn w-100 rounded-pill py-2 fw-medium text-white shadow-sm" style="background: linear-gradient(135deg, #6366F1, #8B5CF6); transition: all 0.2s;" onmouseover="this.style.boxShadow='0 4px 15px rgba(99,102,241,0.4)'" onmouseout="this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)'">Chat with AI Support</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
