<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Checkout';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    redirect('cart.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $address = clean_input($_POST['address']);
    $city = clean_input($_POST['city']);
    $pincode = clean_input($_POST['pincode']);
    
    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($pincode)) {
        $error = 'Please fill in all mandatory fields';
    } else {
        $total = get_cart_total();
        $user_id = is_logged_in() ? $_SESSION['user_id'] : 0;
        
        $order_id = create_order(
            $user_id,
            $name,
            $email,
            $phone,
            $address,
            $city,
            $pincode,
            $total,
            $_SESSION['cart']
        );
        
        if ($order_id) {
            unset($_SESSION['cart']);
            $success = 'Order #' . $order_id . ' placed successfully!';
        } else {
            $error = 'Failed to process order. Please check your data.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5" style="min-height: 80vh;">
    <style>
        .checkout-input::placeholder { color: #9CA3AF !important; opacity: 1; }
        .checkout-input:focus { border-color: #7C3AED !important; box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.15); }
        .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(124, 58, 237, 0.4) !important; }
    </style>

    <?php if ($success): ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center py-5 rounded-5 shadow-lg border animate__animated animate__zoomIn" style="background-color: #141821; border-color: #374151;">
                    <div class="mb-4 d-inline-flex p-3 rounded-circle" style="background-color: rgba(16, 185, 129, 0.1);">
                        <i class="bi bi-check-circle-fill display-3" style="color: #10B981;"></i>
                    </div>
                    <h1 class="fw-bold mb-3 display-5 text-white">Order Placed Successfully!</h1>
                    <p class="fs-5 mb-5 px-4" style="color: #9CA3AF;">
                        Order #<span class="fw-bold text-white"><?php echo $order_id; ?></span> has been confirmed.<br>
                        A confirmation email has been sent to <span class="fw-bold text-white"><?php echo htmlspecialchars($email); ?></span>
                    </p>
                    
                    <div class="row justify-content-center g-4 mb-5">
                        <div class="col-md-5">
                            <div class="p-4 rounded-4 border" style="background: rgba(255,255,255,0.03); border-color: #374151 !important;">
                                <i class="bi bi-truck fs-2 mb-2 d-block" style="color: #7C3AED;"></i>
                                <h6 class="text-white mb-1 fw-bold">Estimated Delivery</h6>
                                <small style="color: #9CA3AF;">3-5 Business Days</small>
                            </div>
                        </div>
                    </div>
        
                    <div class="d-flex gap-3 justify-content-center flex-wrap px-4">
                        <a href="index.php" class="btn btn-lg rounded-pill px-5 fw-bold py-3 shadow-lg hover-lift" style="background-color: #7C3AED; color: #FFFFFF; border: none; min-width: 200px;">
                            Continue Shopping
                        </a>
                        <a href="track-order.php?id=<?php echo $order_id; ?>" class="btn btn-lg rounded-pill px-5 fw-bold py-3 hover-lift" style="background-color: transparent; border: 1px solid #4B5563; color: #E5E7EB; min-width: 200px;">
                            Track Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Breadcrumb & Title -->
        <div class="row mb-5">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="cart.php" class="text-decoration-none fw-medium" style="color: #9CA3AF;">Cart</a></li>
                        <li class="breadcrumb-item active fw-bold" aria-current="page" style="color: #D1D5DB;">Checkout</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 48px; height: 48px; background: rgba(124, 58, 237, 0.2); color: #8B5CF6;">
                        <i class="bi bi-lock-fill fs-4"></i>
                    </div>
                    <h1 class="fw-bold display-6 mb-0" style="color: #E5E7EB;">Secure Checkout</h1>
                </div>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert rounded-4 shadow-sm mb-4 border-0 text-white fw-bold d-flex align-items-center animate__animated animate__shakeX" style="background-color: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4);">
                <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="row g-5">
            <!-- LEFT PANEL -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden animate__animated animate__fadeInUp" style="background-color: #FFFFFF;">
                    <div class="card-body p-4 p-md-5">
                        
                        <!-- Header -->
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom" style="border-color: #E5E7EB !important;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #F3F4F6; color: #7C3AED;">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <h4 class="fw-bold mb-0" style="color: #111827;">Shipping Information</h4>
                        </div>

                        <form method="POST" class="row g-4">
                            <!-- Name -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-uppercase" style="color: #374151;">Full Name</label>
                                <div class="input-group">
                                    <input type="text" name="name" class="form-control form-control-lg rounded-pill px-4 checkout-input" 
                                           style="background-color: #1F2937; border: 1px solid #374151; color: #E5E7EB; font-size: 1rem;" 
                                           placeholder="Enter your full name" required>
                                </div>
                            </div>
                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase" style="color: #374151;">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg rounded-pill px-4 checkout-input" 
                                       style="background-color: #1F2937; border: 1px solid #374151; color: #E5E7EB; font-size: 1rem;"
                                       placeholder="your@email.com" required>
                            </div>
                            <!-- Phone -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase" style="color: #374151;">Phone Number</label>
                                <input type="tel" name="phone" class="form-control form-control-lg rounded-pill px-4 checkout-input" 
                                       style="background-color: #1F2937; border: 1px solid #374151; color: #E5E7EB; font-size: 1rem;"
                                       placeholder="+91 00000 00000" required>
                            </div>
                            <!-- Address -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-uppercase" style="color: #374151;">Shipping Address</label>
                                <textarea name="address" rows="2" class="form-control rounded-4 px-4 py-3 checkout-input" 
                                          style="background-color: #1F2937; border: 1px solid #374151; color: #E5E7EB; font-size: 1rem; resize: none;"
                                          placeholder="Flat / House No / Street Name" required></textarea>
                            </div>
                            <!-- City -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase" style="color: #374151;">City</label>
                                <input type="text" name="city" class="form-control form-control-lg rounded-pill px-4 checkout-input" 
                                       style="background-color: #1F2937; border: 1px solid #374151; color: #E5E7EB; font-size: 1rem;"
                                       placeholder="City Name" required>
                            </div>
                            <!-- Pincode -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase" style="color: #374151;">Pincode</label>
                                <input type="text" name="pincode" class="form-control form-control-lg rounded-pill px-4 checkout-input" 
                                       style="background-color: #1F2937; border: 1px solid #374151; color: #E5E7EB; font-size: 1rem;"
                                       placeholder="000000" required>
                            </div>

                             <!-- Payment Section -->
                            <div class="col-12 mt-5">
                                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom" style="border-color: #E5E7EB !important;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #F3F4F6; color: #7C3AED;">
                                        <i class="bi bi-wallet-fill"></i>
                                    </div>
                                    <h4 class="fw-bold mb-0" style="color: #111827;">Payment Method</h4>
                                </div>
                                
                                <div class="p-3 rounded-4 border-2 position-relative cursor-pointer hover-lift" 
                                     style="background-color: #F9FAFB; border: 2px solid #7C3AED;">
                                    <div class="form-check d-flex align-items-center gap-3 m-0">
                                        <input class="form-check-input fs-5" type="radio" name="payment" id="cod" checked 
                                               style="background-color: #7C3AED; border-color: #7C3AED; box-shadow: none;">
                                        <label class="form-check-label flex-grow-1" for="cod">
                                            <span class="d-block fw-bold fs-5" style="color: #111827;">Cash on Delivery (COD)</span>
                                            <span class="small fw-bold" style="color: #6B7280;">Pay securely at your doorstep</span>
                                        </label>
                                        <i class="bi bi-cash-stack fs-3" style="color: #7C3AED;"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- CTA -->
                            <div class="col-12 mt-4 pt-2">
                                <button type="submit" class="btn btn-lg w-100 rounded-pill fw-bold py-3 text-uppercase shadow-lg hover-lift" 
                                        style="background-color: #7C3AED; color: white; letter-spacing: 0.5px; border: none;">
                                    Confirm & Place Order <i class="bi bi-arrow-right-short fs-4 ms-1 align-middle"></i>
                                </button>
                                <div class="text-center mt-3">
                                    <div class="d-flex align-items-center justify-content-center gap-2 small fw-bold" style="color: #4B5563;">
                                        <i class="bi bi-lock-fill"></i> Secure checkout encrypted by 256-bit SSL
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL (Summary) -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden sticky-top animate__animated animate__fadeInUp animate__delay-1s" style="top: 2rem; background-color: #FFFFFF;">
                    <div class="card-header bg-white border-bottom py-4 px-4" style="border-color: #E5E7EB !important;">
                        <h5 class="fw-bold mb-0" style="color: #111827;">Order Summary</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-3 mb-4">
                            <?php 
                            foreach ($_SESSION['cart'] as $product_id => $quantity): 
                                $product = get_product_by_id($product_id);
                                if ($product):
                                    $subtotal = $product['price'] * $quantity;
                            ?>
                            <div class="d-flex gap-3 align-items-center">
                                <div class="rounded-3 d-flex align-items-center justify-content-center p-2 border" 
                                     style="width: 64px; height: 64px; background-color: #F9FAFB; border-color: #E5E7EB !important;">
                                    <?php if(!empty($product['image'])): ?>
                                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                    <?php else: ?>
                                        <i class="bi bi-box-seam fs-4 text-muted"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1 text-truncate" style="color: #111827; max-width: 180px;">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h6>
                                    <div class="small fw-bold" style="color: #6B7280;">
                                        Qty: <?php echo $quantity; ?>
                                    </div>
                                </div>
                                <div class="fw-bold text-end" style="color: #111827;">
                                    ₹<?php echo number_format($subtotal, 0); ?>
                                </div>
                            </div>
                            <?php endif; endforeach; ?>
                        </div>

                        <!-- Computations -->
                        <div class="border-top pt-3 mb-4" style="border-color: #E5E7EB !important;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-medium" style="color: #4B5563;">Subtotal</span>
                                <span class="fw-bold" style="color: #111827;">₹<?php echo number_format(get_cart_total(), 0); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-medium" style="color: #4B5563;">Shipping</span>
                                <span class="fw-bold" style="color: #10B981;">FREE</span>
                            </div>
                            <div class="d-flex justify-content-between pt-3 border-top" style="border-color: #E5E7EB !important;">
                                <span class="fs-5 fw-bold" style="color: #111827;">Total</span>
                                <span class="fs-3 fw-bold" style="color: #7C3AED;">₹<?php echo number_format(get_cart_total(), 0); ?></span>
                            </div>
                        </div>
                        
                        <!-- Trust Badge -->
                        <div class="rounded-3 p-3 d-flex align-items-start gap-3" style="background-color: #F3F4F6;">
                            <i class="bi bi-shield-check fs-2" style="color: #4B5563;"></i>
                            <div>
                                <h6 class="fw-bold mb-1" style="color: #374151;">SSL Secured Payment</h6>
                                <p class="small mb-0 fw-medium" style="color: #6B7280; line-height: 1.4;">
                                    Your personal data is encrypted and secure. We do not store card details.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
