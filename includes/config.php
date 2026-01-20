<?php
// Site Configuration
define('SITE_NAME', 'Univault');
define('SITE_URL', 'http://localhost/6xpress');
define('CURRENCY', '₹');

// CSV File Paths
// Check if we are in a writable environment (like local dev) or read-only (like Vercel)
$original_data_dir = __DIR__ . '/../data/';
$writable_dir = $original_data_dir;

// If we are on Vercel or the directory is not writable, use /tmp
if (getenv('VERCEL') || (!is_writable($original_data_dir) && php_sapi_name() !== 'cli')) {
    $writable_dir = '/tmp/';
}

define('DATA_DIR', $writable_dir);
define('PRODUCTS_CSV', DATA_DIR . 'products.csv');
define('USERS_CSV', DATA_DIR . 'users.csv');
define('ORDERS_CSV', DATA_DIR . 'orders.csv');

// Initialize /tmp data text files if needed
if ($writable_dir === '/tmp/') {
    $files = ['products.csv', 'users.csv', 'orders.csv'];
    foreach ($files as $file) {
        $source = $original_data_dir . $file;
        $dest = DATA_DIR . $file;
        
        // Only copy if destination doesn't exist (preserve session data if container reuse)
        if (!file_exists($dest)) {
            if (file_exists($source)) {
                copy($source, $dest);
            } else {
                // Initialize empty file if source doesn't exist
                touch($dest);
            }
        }
    }
}

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
