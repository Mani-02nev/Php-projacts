<?php
// Site Configuration
define('SITE_NAME', 'Univault');
define('SITE_URL', 'http://localhost/6xpress');
define('CURRENCY', '₹');

// CSV File Paths
define('DATA_DIR', __DIR__ . '/../data/');
define('PRODUCTS_CSV', DATA_DIR . 'products.csv');
define('USERS_CSV', DATA_DIR . 'users.csv');
define('ORDERS_CSV', DATA_DIR . 'orders.csv');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSV Helper Functions
function read_csv($file) {
    if (!file_exists($file)) {
        return [];
    }
    
    $data = [];
    $handle = fopen($file, 'r');
    
    if ($handle) {
        $headers = fgetcsv($handle, 0, ',', '"', '\\');
        
        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if (count($row) == count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }
        
        fclose($handle);
    }
    
    return $data;
}

function write_csv($file, $data, $headers = null) {
    $handle = fopen($file, 'w');
    
    if ($handle) {
        // Write headers
        if ($headers) {
            fputcsv($handle, $headers, ',', '"', '\\');
        } elseif (!empty($data)) {
            fputcsv($handle, array_keys($data[0]), ',', '"', '\\');
        }
        
        // Write data
        foreach ($data as $row) {
            fputcsv($handle, $row, ',', '"', '\\');
        }
        
        fclose($handle);
        return true;
    }
    
    return false;
}

function append_csv($file, $row) {
    $handle = fopen($file, 'a');
    
    if ($handle) {
        fputcsv($handle, $row, ',', '"', '\\');
        fclose($handle);
        return true;
    }
    
    return false;
}

function get_next_id($data) {
    if (empty($data)) {
        return 1;
    }
    
    $max_id = 0;
    foreach ($data as $row) {
        if (isset($row['id']) && $row['id'] > $max_id) {
            $max_id = $row['id'];
        }
    }
    
    return $max_id + 1;
}
?>
