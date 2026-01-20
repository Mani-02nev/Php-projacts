<?php
// Helper Functions for Univaut

// Sanitize input data
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Format price with currency
function format_price($price) {
    return CURRENCY . number_format($price, 2);
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Redirect to a page
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Get cart item count
function get_cart_count() {
    if (!isset($_SESSION['cart'])) {
        return 0;
    }
    return array_sum($_SESSION['cart']);
}

// Add item to cart
function add_to_cart($product_id, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

// Remove item from cart
function remove_from_cart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

// Get cart total (CSV version)
function get_cart_total() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    
    $products = read_csv(PRODUCTS_CSV);
    $total = 0;
    
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        foreach ($products as $product) {
            if ($product['id'] == $product_id) {
                $total += $product['price'] * $quantity;
                break;
            }
        }
    }
    
    return $total;
}

// Get product by ID
function get_product_by_id($id) {
    $products = read_csv(PRODUCTS_CSV);
    
    foreach ($products as $product) {
        if ($product['id'] == $id) {
            return $product;
        }
    }
    
    return null;
}

// Get all products
function get_all_products() {
    return read_csv(PRODUCTS_CSV);
}

// Add new product
function add_product($name, $description, $price, $stock, $image = '', $category = 'General') {
    $products = read_csv(PRODUCTS_CSV);
    $new_id = get_next_id($products);
    
    $new_product = [
        'id' => $new_id,
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'stock' => $stock,
        'image' => $image,
        'category' => $category
    ];
    
    $products[] = $new_product;
    return write_csv(PRODUCTS_CSV, $products);
}

// Wishlist Functions
function add_to_wishlist($product_id) {
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = array();
    }
    if (!in_array($product_id, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $product_id;
        return true;
    }
    return false;
}

function remove_from_wishlist($product_id) {
    if (isset($_SESSION['wishlist'])) {
        $key = array_search($product_id, $_SESSION['wishlist']);
        if ($key !== false) {
            unset($_SESSION['wishlist'][$key]);
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
            return true;
        }
    }
    return false;
}

function get_wishlist_count() {
    return isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
}

function is_in_wishlist($product_id) {
    return isset($_SESSION['wishlist']) && in_array($product_id, $_SESSION['wishlist']);
}

// Recently Viewed Functions
function add_to_recently_viewed($product_id) {
    if (!isset($_SESSION['recently_viewed'])) {
        $_SESSION['recently_viewed'] = array();
    }
    
    // Remove if already exists to move to top
    if (($key = array_search($product_id, $_SESSION['recently_viewed'])) !== false) {
        unset($_SESSION['recently_viewed'][$key]);
    }
    
    array_unshift($_SESSION['recently_viewed'], $product_id);
    
    // Keep only last 10
    $_SESSION['recently_viewed'] = array_slice($_SESSION['recently_viewed'], 0, 10);
}

function get_recently_viewed_products() {
    if (!isset($_SESSION['recently_viewed']) || empty($_SESSION['recently_viewed'])) {
        return [];
    }
    
    $all = get_all_products();
    $result = [];
    
    foreach ($_SESSION['recently_viewed'] as $id) {
        foreach ($all as $p) {
            if ($p['id'] == $id) {
                $result[] = $p;
                break;
            }
        }
    }
    return $result;
}

// Delete product
function delete_product($id) {
    $products = read_csv(PRODUCTS_CSV);
    $filtered = array_filter($products, function($product) use ($id) {
        return $product['id'] != $id;
    });
    
    return write_csv(PRODUCTS_CSV, array_values($filtered));
}

// Update product stock
function update_product_stock($id, $quantity_change) {
    $products = read_csv(PRODUCTS_CSV);
    
    foreach ($products as &$product) {
        if ($product['id'] == $id) {
            $product['stock'] = max(0, $product['stock'] + $quantity_change);
            break;
        }
    }
    
    return write_csv(PRODUCTS_CSV, $products);
}

// Get user by email
function get_user_by_email($email) {
    $users = read_csv(USERS_CSV);
    
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return $user;
        }
    }
    
    return null;
}

// Add new user
function add_user($name, $email, $password, $role = 'customer') {
    $users = read_csv(USERS_CSV);
    $new_id = get_next_id($users);
    
    $new_user = [
        'id' => $new_id,
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $role
    ];
    
    $users[] = $new_user;
    return write_csv(USERS_CSV, $users);
}

// Create order
function create_order($user_id, $customer_name, $customer_email, $customer_phone, $shipping_address, $city, $pincode, $total_amount, $cart_items) {
    $orders = read_csv(ORDERS_CSV);
    $new_id = get_next_id($orders);
    
    // Serialize cart items
    $items_json = json_encode($cart_items);
    
    $new_order = [
        'id' => $new_id,
        'user_id' => $user_id,
        'customer_name' => $customer_name,
        'customer_email' => $customer_email,
        'customer_phone' => $customer_phone,
        'shipping_address' => $shipping_address,
        'city' => $city,
        'pincode' => $pincode,
        'total_amount' => $total_amount,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s'),
        'items' => $items_json
    ];
    
    $orders[] = $new_order;
    
    if (write_csv(ORDERS_CSV, $orders)) {
        // Update product stock
        foreach ($cart_items as $product_id => $quantity) {
            update_product_stock($product_id, -$quantity);
        }
        return $new_id;
    }
    
    return false;
}

// Get all orders
function get_all_orders() {
    return read_csv(ORDERS_CSV);
}

// Update order status
function update_order_status($order_id, $status) {
    $orders = read_csv(ORDERS_CSV);
    
    foreach ($orders as &$order) {
        if ($order['id'] == $order_id) {
            $order['status'] = $status;
            break;
        }
    }
    
    return write_csv(ORDERS_CSV, $orders);
}

// Get statistics
function get_stats() {
    $products = read_csv(PRODUCTS_CSV);
    $orders = read_csv(ORDERS_CSV);
    $users = read_csv(USERS_CSV);
    
    $total_revenue = 0;
    foreach ($orders as $order) {
        $total_revenue += floatval($order['total_amount']);
    }
    
    return [
        'total_products' => count($products),
        'total_orders' => count($orders),
        'total_users' => count($users),
        'total_revenue' => $total_revenue
    ];
}

// Display success message
function show_success($message) {
    return '<div class="alert alert-success">' . $message . '</div>';
}

// Display error message
function show_error($message) {
    return '<div class="alert alert-error">' . $message . '</div>';
}
