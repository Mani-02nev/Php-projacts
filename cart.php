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
        $_SESSION['success_message'] = 'Cart updated successfully!';
    }
    
    if (isset($_POST['remove_item'])) {
        $product_id = intval($_POST['product_id']);
        remove_from_cart($product_id);
        $_SESSION['success_message'] = 'Item removed from cart!';
    }
}

include 'includes/header.php';
?>

<style>
.cart-container {
    max-width: 1200px;
    margin: 2rem auto;
}

.cart-items {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    overflow: hidden;
}

.cart-item {
    display: grid;
    grid-template-columns: 120px 1fr auto auto auto;
    gap: 1.5rem;
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    align-items: center;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item-image {
    width: 120px;
    height: 120px;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 0.5rem;
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.cart-item-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.cart-item-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--black);
    margin-bottom: 0.25rem;
}

.cart-item-meta {
    font-size: 0.875rem;
    color: var(--gray-600);
    display: flex;
    gap: 1rem;
}

.cart-item-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--black);
    min-width: 120px;
    text-align: right;
}

.cart-item-quantity {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quantity-input {
    width: 80px;
    padding: 0.5rem;
    border: 1px solid var(--gray-300);
    border-radius: 4px;
    text-align: center;
    font-weight: 600;
}

.cart-item-subtotal {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--black);
    min-width: 140px;
    text-align: right;
}

.cart-item-remove {
    background: transparent;
    border: 1px solid var(--gray-300);
    color: var(--gray-600);
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    transition: var(--transition);
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.cart-item-remove:hover {
    background: #ff4444;
    color: var(--white);
    border-color: #ff4444;
}

.cart-summary {
    background: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    padding: 2rem;
    margin-top: 2rem;
}

.cart-summary-row {
    display: flex;
    justify-content: space-between;
    padding: 1rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.cart-summary-row:last-child {
    border-bottom: none;
    padding-top: 1.5rem;
    font-size: 1.5rem;
    font-weight: 700;
}

.cart-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
}

.empty-cart {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--gray-100);
    border-radius: 8px;
}

@media (max-width: 768px) {
    .cart-item {
        grid-template-columns: 80px 1fr;
        gap: 1rem;
    }
    
    .cart-item-price,
    .cart-item-quantity,
    .cart-item-subtotal,
    .cart-item-remove {
        grid-column: 2;
    }
}
</style>

<div class="container">
    <h1 class="section-title"><i class="bi bi-cart3"></i> Shopping Cart</h1>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            <?php 
            echo $_SESSION['success_message']; 
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
        <form method="POST">
            <div class="cart-items">
                <?php 
                $total = 0;
                $item_count = 0;
                foreach ($_SESSION['cart'] as $product_id => $quantity): 
                    $product = get_product_by_id($product_id);
                    
                    if ($product):
                        $subtotal = $product['price'] * $quantity;
                        $total += $subtotal;
                        $item_count += $quantity;
                ?>
                    <div class="cart-item">
                        <!-- Product Image -->
                        <div class="cart-item-image">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'%3E%3Crect fill=\'%23f5f5f5\' width=\'100\' height=\'100\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-size=\'12\'%3E📦%3C/text%3E%3C/svg%3E'">
                            <?php else: ?>
                                <i class="bi bi-image" style="font-size: 3rem; color: var(--gray-400);"></i>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Product Details -->
                        <div class="cart-item-details">
                            <div class="cart-item-name"><?php echo htmlspecialchars($product['name']); ?></div>
                            <div class="cart-item-meta">
                                <span><i class="bi bi-tag"></i> ₹<?php echo number_format($product['price'], 0); ?> each</span>
                                <span><i class="bi bi-box-seam"></i> Stock: <?php echo $product['stock']; ?></span>
                                <span><i class="bi bi-hash"></i> ID: <?php echo $product['id']; ?></span>
                            </div>
                        </div>
                        
                        <!-- Quantity -->
                        <div class="cart-item-quantity">
                            <label style="font-size: 0.875rem; font-weight: 600;">Qty:</label>
                            <input type="number" 
                                   name="quantity[<?php echo $product_id; ?>]" 
                                   value="<?php echo $quantity; ?>" 
                                   min="0" 
                                   max="<?php echo $product['stock']; ?>"
                                   class="quantity-input">
                        </div>
                        
                        <!-- Subtotal -->
                        <div class="cart-item-subtotal">
                            ₹<?php echo number_format($subtotal, 0); ?>
                        </div>
                        
                        <!-- Remove Button -->
                        <button type="submit" 
                                name="remove_item" 
                                class="cart-item-remove">
                            <i class="bi bi-trash"></i> Remove
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        </button>
                    </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
            
            <!-- Cart Summary -->
            <div class="cart-summary">
                <div class="cart-summary-row">
                    <span><i class="bi bi-box-seam"></i> Total Items</span>
                    <strong><?php echo $item_count; ?> item(s)</strong>
                </div>
                <div class="cart-summary-row">
                    <span><i class="bi bi-truck"></i> Shipping</span>
                    <strong>FREE</strong>
                </div>
                <div class="cart-summary-row">
                    <span><i class="bi bi-currency-rupee"></i> Total Amount</span>
                    <strong>₹<?php echo number_format($total, 0); ?></strong>
                </div>
            </div>
            
            <!-- Cart Actions -->
            <div class="cart-actions">
                <a href="products.php" class="btn" style="background: var(--gray-200); color: var(--black);">
                    <i class="bi bi-arrow-left"></i> Continue Shopping
                </a>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="update_cart" class="btn" style="background: var(--gray-800); color: var(--white);">
                        <i class="bi bi-arrow-clockwise"></i> Update Cart
                    </button>
                    <a href="checkout.php" class="btn btn-black" style="font-size: 1.1rem; padding: 1rem 2.5rem;">
                        <i class="bi bi-credit-card"></i> Proceed to Checkout
                    </a>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="empty-cart">
            <i class="bi bi-cart-x" style="font-size: 5rem; color: var(--gray-400); margin-bottom: 1rem;"></i>
            <h2 style="margin-bottom: 1rem;">Your cart is empty</h2>
            <p style="color: var(--gray-600); margin-bottom: 2rem;">Add some products to get started!</p>
            <a href="products.php" class="btn btn-black">
                <i class="bi bi-grid"></i> Browse Products
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
