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
        $error = 'Please fill in all fields';
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
            $success = 'Order placed successfully! Order ID: #' . $order_id;
        } else {
            $error = 'Failed to place order. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <h1 class="section-title">Checkout</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
            <p style="margin-top: 1rem;"><a href="index.php" class="btn btn-black">Continue Shopping</a></p>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem; margin: 2rem 0;">
            <div>
                <h2 style="margin-bottom: 1.5rem;">Shipping Information</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Shipping Address</label>
                        <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="pincode">Pincode</label>
                            <input type="text" id="pincode" name="pincode" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-black" style="width: 100%; margin-top: 1rem; padding: 1rem;">
                        Place Order
                    </button>
                </form>
            </div>
            
            <div>
                <h2 style="margin-bottom: 1.5rem;">Order Summary</h2>
                <div style="background: var(--gray-100); padding: 1.5rem; border-radius: 8px;">
                    <?php 
                    $total = 0;
                    foreach ($_SESSION['cart'] as $product_id => $quantity): 
                        $product = get_product_by_id($product_id);
                        if ($product):
                            $subtotal = $product['price'] * $quantity;
                            $total += $subtotal;
                    ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--gray-300);">
                            <div>
                                <strong><?php echo htmlspecialchars($product['name']); ?></strong><br>
                                <small>Qty: <?php echo $quantity; ?></small>
                            </div>
                            <div><?php echo format_price($subtotal); ?></div>
                        </div>
                    <?php endif; endforeach; ?>
                    
                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid var(--black);">
                        <div style="display: flex; justify-content: space-between; font-size: 1.5rem; font-weight: 700;">
                            <span>Total:</span>
                            <span><?php echo format_price($total); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
