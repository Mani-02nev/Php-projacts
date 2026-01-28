<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

// Only accept POST requests for state changes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Support both JSON body and Form Data
    $action = $input['action'] ?? $_POST['action'] ?? '';
    $product_id = intval($input['product_id'] ?? $_POST['product_id'] ?? 0);
    
    if ($action === 'add_to_cart' && $product_id > 0) {
        $quantity = intval($input['quantity'] ?? $_POST['quantity'] ?? 1);
        add_to_cart($product_id, $quantity);
        
        // Calculate new cart count
        $count = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $qty) {
                $count += $qty;
            }
        }
        
        echo json_encode(['success' => true, 'cart_count' => $count, 'message' => 'Product added to cart']);
        exit;
    }
    
    if ($action === 'toggle_wishlist' && $product_id > 0) {
        $status = 'removed';
        if (is_in_wishlist($product_id)) {
            remove_from_wishlist($product_id);
        } else {
            add_to_wishlist($product_id);
            $status = 'added';
        }
        
        echo json_encode(['success' => true, 'status' => $status, 'message' => 'Wishlist updated']);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
