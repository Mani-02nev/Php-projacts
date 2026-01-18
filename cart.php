<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Shopping Cart';

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id] = intval($quantity);
            } else {
                remove_from_cart($product_id);
            }
        }
        $success_msg = 'Cart updated successfully!';
    }
    
    if (isset($_POST['remove_item'])) {
        $product_id = intval($_POST['product_id']);
        remove_from_cart($product_id);
        $success_msg = 'Item removed from cart!';
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fw-bold mb-0" style="color: #E5E7EB;"><i class="bi bi-cart3 me-2"></i> Shopping Cart</h1>
        <span class="badge border px-3 py-2 rounded-pill" style="background-color: #14161A; color: #9CA3AF; border-color: #2D2D35 !important;"><?php echo get_cart_count(); ?> items</span>
    </div>

    <?php if (isset($success_msg)): ?>
        <script>window.onload = () => showToast("<?php echo $success_msg; ?>", "success");</script>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <form method="POST" id="cartForm">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background-color: #14161A;">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" style="border-color: #2D2D35;">
                                <thead style="background-color: rgba(255,255,255,0.02); color: #9CA3AF;">
                                    <tr class="small text-uppercase fw-bold">
                                        <th class="ps-4 py-3" style="border-bottom-color: #2D2D35;">Product</th>
                                        <th class="py-3" style="border-bottom-color: #2D2D35;">Price</th>
                                        <th class="py-3 text-center" style="width: 150px; border-bottom-color: #2D2D35;">Quantity</th>
                                        <th class="py-3 text-end" style="border-bottom-color: #2D2D35;">Subtotal</th>
                                        <th class="pe-4 py-3 text-end" style="width: 80px; border-bottom-color: #2D2D35;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total = 0;
                                    foreach ($_SESSION['cart'] as $product_id => $quantity): 
                                        $product = get_product_by_id($product_id);
                                        if ($product):
                                            $subtotal = $product['price'] * $quantity;
                                            $total += $subtotal;
                                    ?>
                                        <tr>
                                            <td class="ps-4 py-4" style="border-bottom-color: #2D2D35;">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="rounded-3 d-flex align-items-center justify-content-center p-2" style="width: 80px; height: 80px; background-color: #FFFFFF;">
                                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold mb-1" style="color: #E5E7EB;"><?php echo htmlspecialchars($product['name']); ?></h6>
                                                        <span class="badge border fw-normal mb-0" style="background-color: rgba(255,255,255,0.05); color: #9CA3AF; border-color: #2D2D35 !important;"><?php echo htmlspecialchars($product['category']); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 fw-medium" style="color: #D1D5DB; border-bottom-color: #2D2D35;">₹<?php echo number_format($product['price'], 0); ?></td>
                                            <td class="py-4" style="border-bottom-color: #2D2D35;">
                                                <div class="input-group input-group-sm rounded-pill overflow-hidden border mx-auto" style="max-width: 120px; border-color: #2D2D35 !important;">
                                                    <button type="button" class="btn px-2" style="color: #9CA3AF; border-color: #2D2D35;" onclick="decrementQty(<?php echo $product_id; ?>)">-</button>
                                                    <input type="number" 
                                                           name="quantity[<?php echo $product_id; ?>]" 
                                                           id="qty-<?php echo $product_id; ?>"
                                                           value="<?php echo $quantity; ?>" 
                                                           min="1" 
                                                           max="<?php echo $product['stock']; ?>"
                                                           class="form-control border-0 text-center fw-bold px-0 shadow-none"
                                                           style="background-color: transparent; color: #E5E7EB;">
                                                    <button type="button" class="btn px-2" style="color: #9CA3AF; border-color: #2D2D35;" onclick="incrementQty(<?php echo $product_id; ?>, <?php echo $product['stock']; ?>)">+</button>
                                                </div>
                                            </td>
                                            <td class="py-4 text-end fw-bold" style="color: #7C3AED; border-bottom-color: #2D2D35;">₹<?php echo number_format($subtotal, 0); ?></td>
                                            <td class="pe-4 py-4 text-end" style="border-bottom-color: #2D2D35;">
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                                    <button type="submit" name="remove_item" class="btn btn-sm border-0 rounded-circle p-2" style="color: #EF4444; background: rgba(239, 68, 68, 0.1);">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endif; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center px-2">
                        <a href="products.php" class="btn rounded-pill px-4 fw-bold" style="background-color: #1F2937; color: #E5E7EB; border: 1px solid #374151;">
                            <i class="bi bi-arrow-left me-2"></i> Continue Shopping
                        </a>
                        <button type="submit" name="update_cart" class="btn rounded-pill px-4 fw-bold shadow" style="background-color: #7C3AED; color: #FFFFFF; border: none;">
                            <i class="bi bi-arrow-clockwise me-1"></i> Update Cart
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Summary Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px; background-color: #14161A;">
                    <h5 class="fw-bold mb-4" style="color: #E5E7EB;">Order Summary</h5>
                    
                    <div class="d-flex justify-content-between mb-3" style="color: #9CA3AF;">
                        <span>Subtotal</span>
                        <span class="fw-medium" style="color: #E5E7EB;">₹<?php echo number_format($total, 0); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3" style="color: #9CA3AF;">
                        <span>Shipping</span>
                        <span class="fw-bold" style="color: #10B981;">FREE</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4 border-bottom pb-3" style="color: #9CA3AF; border-color: #2D2D35 !important;">
                        <span>Tax (Estimated)</span>
                        <span class="fw-medium" style="color: #E5E7EB;">₹0.00</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0" style="color: #E5E7EB;">Total</h5>
                        <h4 class="fw-bold mb-0" style="color: #7C3AED;">₹<?php echo number_format($total, 0); ?></h4>
                    </div>
                    
                    <div class="d-grid gap-3">
                        <a href="checkout.php" class="btn btn-lg rounded-pill fw-bold py-3 shadow border-0" style="background-color: #7C3AED; color: #FFFFFF;">
                            <i class="bi bi-shield-lock-fill me-2"></i> Secure Checkout
                        </a>
                        <div class="text-center small" style="color: #6B7280;">
                            <i class="bi bi-lock me-1"></i> SSL Encrypted Payment
                        </div>
                    </div>
                    
                    <div class="mt-4 p-3 rounded-4 border small" style="background-color: rgba(255,255,255,0.02); border-color: #2D2D35 !important; color: #9CA3AF;">
                        <div class="d-flex gap-2 mb-2">
                            <i class="bi bi-truck fs-5" style="color: #7C3AED;"></i>
                            <span>Fast delivery within 3-5 business days.</span>
                        </div>
                        <div class="d-flex gap-2">
                            <i class="bi bi-arrow-counterclockwise fs-5" style="color: #7C3AED;"></i>
                            <span>Easy 30-day return policy.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5 rounded-5 shadow-sm border" style="background-color: #14161A; border-color: #2D2D35;">
            <div class="display-1 mb-4" style="color: #374151; opacity: 0.5;">
                <i class="bi bi-cart-x"></i>
            </div>
            <h3 class="fw-bold mb-2" style="color: #E5E7EB;">Your cart is empty</h3>
            <p class="mb-4" style="color: #9CA3AF;">Look like you haven't added anything to your cart yet.</p>
            <a href="products.php" class="btn rounded-pill px-5 fw-bold py-2 shadow" style="background-color: #7C3AED; color: #FFFFFF; border: none;">
                Start Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function incrementQty(id, max) {
    const input = document.getElementById('qty-' + id);
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}
function decrementQty(id) {
    const input = document.getElementById('qty-' + id);
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
