<?php
/**
 * Local Mart API — Univault Platform
 * Reads shops.csv and local-mart-products.csv and returns JSON
 */

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

// ── CSV Paths ───────────────────────────────────────────────────
$shops_csv    = __DIR__ . '/../data/shops.csv';
$products_csv = __DIR__ . '/../data/local-mart-products.csv';

// ── Generic CSV reader ──────────────────────────────────────────
function read_local_csv(string $path): array {
    if (!file_exists($path)) {
        return [];
    }
    $rows    = [];
    $handle  = fopen($path, 'r');
    if (!$handle) return [];

    $headers = fgetcsv($handle);   // first row = headers
    if (!$headers) { fclose($handle); return []; }

    // Trim BOM / whitespace from header names
    $headers = array_map(fn($h) => trim($h, " \t\n\r\0\x0B\xEF\xBB\xBF"), $headers);

    while (($data = fgetcsv($handle)) !== false) {
        if (count($data) !== count($headers)) continue;
        $row = array_combine($headers, $data);
        // Cast numeric fields
        $rows[] = $row;
    }
    fclose($handle);
    return $rows;
}

// ── Load Data ───────────────────────────────────────────────────
$shops    = read_local_csv($shops_csv);
$products = read_local_csv($products_csv);

// ── Type-cast shops ─────────────────────────────────────────────
$shops = array_map(function($s) {
    return [
        'shop_id'       => (int)$s['shop_id'],
        'shop_name'     => $s['shop_name'],
        'address'       => $s['address'],
        'latitude'      => (float)$s['latitude'],
        'longitude'     => (float)$s['longitude'],
        'rating'        => (float)$s['rating'],
        'phone'         => $s['phone'],
        'category'      => $s['category']    ?? 'General',
        'opening_hours' => $s['opening_hours'] ?? '9 AM – 9 PM',
        'is_open'       => (bool)(int)($s['is_open'] ?? 1),
    ];
}, $shops);

// ── Type-cast products + inject product_count into shops ────────
$product_count_map = [];   // shop_id → count

$products = array_map(function($p) use (&$product_count_map) {
    $sid = (int)$p['shop_id'];
    $product_count_map[$sid] = ($product_count_map[$sid] ?? 0) + 1;
    return [
        'product_id'   => (int)$p['product_id'],
        'product_name' => $p['product_name'],
        'shop_id'      => $sid,
        'price'        => (float)$p['price'],
        'stock'        => (int)$p['stock'],
        'category'     => $p['category']  ?? '',
        'image_url'    => $p['image_url'] ?? '',
    ];
}, $products);

// Stamp product_count onto each shop
$shops = array_map(function($s) use ($product_count_map) {
    $s['product_count'] = $product_count_map[$s['shop_id']] ?? 0;
    return $s;
}, $shops);

// ── Optional: filter by search query ────────────────────────────
$search = trim($_GET['search'] ?? '');
$result_products = $products;
if ($search !== '') {
    $q = strtolower($search);
    $result_products = array_values(array_filter($products, function($p) use ($q) {
        return str_contains(strtolower($p['product_name']), $q)
            || str_contains(strtolower($p['category']), $q);
    }));
}

// ── Optional: filter shops by shop_id ───────────────────────────
$shop_id_filter = (int)($_GET['shop_id'] ?? 0);
$result_shops   = $shops;
if ($shop_id_filter > 0) {
    $result_shops = array_values(array_filter($shops, fn($s) => $s['shop_id'] === $shop_id_filter));
    $result_products = array_values(array_filter($products, fn($p) => $p['shop_id'] === $shop_id_filter));
}

// ── Response ─────────────────────────────────────────────────────
echo json_encode([
    'success'       => true,
    'shops'         => array_values($result_shops),
    'products'      => array_values($result_products),
    'total_shops'   => count($shops),
    'total_products'=> count($products),
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
