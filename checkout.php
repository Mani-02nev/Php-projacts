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

<div class="container py-5">
    <?php if ($success): ?>
        <div class="text-center py-5 bg-white rounded-5 shadow-sm border animate__animated animate__zoomIn">
            <div class="display-1 text-success mb-4 text-center">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h1 class="fw-bold text-dark mb-3">Order Placed!</h1>
            <p class="text-secondary fs-5 mb-4 px-4"><?php echo $success; ?><br>A confirmation email has been sent to your inbox.</p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="index.php" class="btn btn-primary rounded-pill px-5 fw-bold py-2 shadow">Back to Home</a>
                <a href="profile.php" class="btn btn-outline-dark rounded-pill px-5 fw-bold py-2">Track Order</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row mb-5">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="cart.php" class="text-secondary text-decoration-none">Cart</a></li>
                        <li class="breadcrumb-item active fw-bold text-primary">Checkout</li>
                        <li class="breadcrumb-item text-muted">Success</li>
                    </ol>
                </nav>
                <h1 class="fw-bold text-dark"><i class="bi bi-shield-lock me-2"></i> Secure Checkout</h1>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger rounded-4 shadow-sm mb-4 border-0">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="row g-5">
            <!-- Shipping Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4">
                    <h4 class="fw-bold mb-4 border-bottom pb-3"><i class="bi bi-geo-alt me-2 text-primary"></i> Shipping Information</h4>
                    <form method="POST" class="row g-4">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Full Name</label>
                            <input type="text" name="name" class="form-control form-control-lg rounded-3 border-light-subtle shadow-sm px-4" placeholder="Enter your full name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg rounded-3 border-light-subtle shadow-sm px-4" placeholder="your@email.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Phone Number</label>
                            <input type="tel" name="phone" class="form-control form-control-lg rounded-3 border-light-subtle shadow-sm px-4" placeholder="+91 00000 00000" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Shipping Address</label>
                            <textarea name="address" rows="3" class="form-control rounded-3 border-light-subtle shadow-sm px-4" placeholder="Flat / House No / Street Name" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase">City</label>
                            <input type="text" name="city" class="form-control form-control-lg rounded-3 border-light-subtle shadow-sm px-4" placeholder="City Name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary text-uppercase">Pincode</label>
                            <input type="text" name="pincode" class="form-control form-control-lg rounded-3 border-light-subtle shadow-sm px-4" placeholder="000 000" required>
                        </div>

                        <div class="col-12 mt-5">
                            <h4 class="fw-bold mb-4 border-bottom pb-3"><i class="bi bi-credit-card me-2 text-primary"></i> Payment Method</h4>
                            <div class="form-check p-3 bg-light rounded-4 border border-primary-subtle shadow-sm d-flex align-items-center gap-3">
                                <input class="form-check-input ms-0" type="radio" name="payment" id="cod" checked>
                                <label class="form-check-label flex-grow-1" for="cod">
                                    <span class="d-block fw-bold text-dark">Cash on Delivery (COD)</span>
                                    <span class="small text-secondary">Pay securely at your doorstep</span>
                                </label>
                                <i class="bi bi-wallet2 fs-4 text-primary"></i>
                            </div>
                        </div>

                        <div class="col-12 mt-5">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 px-5 shadow border-0 transition-hover w-100">
                                Confirm & Place Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                    <h4 class="fw-bold text-dark mb-4 border-bottom pb-3">Order Summary</h4>
                    
                    <div class="mb-4">
                        <?php 
                        foreach ($_SESSION['cart'] as $product_id => $quantity): 
                            $product = get_product_by_id($product_id);
                            if ($product):
                                $subtotal = $product['price'] * $quantity;
                        ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center p-1" style="width: 50px; height: 50px;">
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                    </div>
                                    <div style="max-width: 150px;">
                                        <h6 class="fw-bold mb-0 text-truncate"><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <small class="text-secondary">Qty: <?php echo $quantity; ?></small>
                                    </div>
                                </div>
                                <span class="fw-medium text-dark">₹<?php echo number_format($subtotal, 0); ?></span>
                            </div>
                        <?php endif; endforeach; ?>
                    </div>
                    
                    <div class="border-top pt-3 mb-4">
                        <div class="d-flex justify-content-between mb-2 text-secondary">
                            <span>Merchandise Total</span>
                            <span class="text-dark fw-medium">₹<?php echo number_format(get_cart_total(), 0); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-secondary">
                            <span>Standard Shipping</span>
                            <span class="text-success fw-bold">FREE</span>
                        </div>
                        <div class="d-flex justify-content-between mt-3">
                            <h5 class="fw-bold text-dark">Order Total</h5>
                            <h4 class="fw-bold text-primary">₹<?php echo number_format(get_cart_total(), 0); ?></h4>
                        </div>
                    </div>

                    <div class="p-3 bg-primary-subtle rounded-4 text-primary small d-flex gap-3 align-items-center">
                        <i class="bi bi-shield-check fs-4"></i>
                        <span>Your data is protected by industry standard encryption. (SSL)</span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
