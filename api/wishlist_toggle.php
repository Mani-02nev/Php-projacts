<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Set JSON header
header('Content-Type: application/json');

// Check if product_id is provided
if (!isset($_GET['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit();
}

$product_id = intval($_GET['product_id']);

// Toggle wishlist
$is_in_wishlist = is_in_wishlist($product_id);

if ($is_in_wishlist) {
    $result = remove_from_wishlist($product_id);
    $message = 'Removed from wishlist';
    $in_wishlist = false;
} else {
    $result = add_to_wishlist($product_id);
    $message = 'Added to wishlist! ❤️';
    $in_wishlist = true;
}

echo json_encode([
    'success' => $result,
    'message' => $message,
    'in_wishlist' => $in_wishlist,
    'product_id' => $product_id
]);
