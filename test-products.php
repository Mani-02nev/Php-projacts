<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get all products
$products = get_all_products();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Product Test - 6Xpress</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .product { border: 1px solid #ddd; padding: 10px; margin: 10px 0; }
        .success { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <h1>6Xpress - Product Load Test</h1>
    <p class="success">✅ Successfully loaded <?php echo count($products); ?> products from CSV!</p>
    
    <h2>Sample Products:</h2>
    <?php foreach (array_slice($products, 0, 10) as $product): ?>
        <div class="product">
            <strong>ID <?php echo $product['id']; ?>:</strong> 
            <?php echo htmlspecialchars($product['name']); ?><br>
            <strong>Price:</strong> <?php echo format_price($product['price']); ?> | 
            <strong>Stock:</strong> <?php echo $product['stock']; ?> units
        </div>
    <?php endforeach; ?>
    
    <h2>All Products Summary:</h2>
    <ul>
        <?php foreach ($products as $product): ?>
            <li>
                <strong><?php echo $product['id']; ?>.</strong> 
                <?php echo htmlspecialchars(substr($product['name'], 0, 60)); ?>... - 
                <strong><?php echo format_price($product['price']); ?></strong>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <hr>
    <p><a href="index.php">← Back to Homepage</a> | <a href="products.php">View All Products</a></p>
</body>
</html>
