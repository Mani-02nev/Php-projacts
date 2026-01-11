<?php
require_once 'includes/config.php';

$categories = [
    'Electronics' => ['Smartphone', 'Laptop', 'Headphones', 'Smartwatch', 'Tablet', 'Camera', 'Speaker', 'Monitor', 'Gaming Console', 'Keyboard'],
    'Fashion' => ['T-Shirt', 'Jeans', 'Sneakers', 'Jacket', 'Watch', 'Handbag', 'Dress', 'Sunglasses', 'Belt', 'Hat'],
    'Home & Living' => ['Bedsheet', 'Curtain', 'Dining Table', 'Sofa', 'Lamp', 'Vase', 'Cushion', 'Rugs', 'Wall Clock', 'Shelf'],
    'Books' => ['Mystery Novel', 'Science Fiction', 'Biography', 'Self-Help', 'Cookbook', 'History', 'Manga', 'Poetry', 'Business', 'Travel Guide'],
    'Beauty' => ['Lipstick', 'Face Cream', 'Shampoo', 'Perfume', 'Mascara', 'Sunscreen', 'Hair Oil', 'Face Wash', 'Nail Polish', 'Serum']
];

$products = [];
$id = 1;

foreach ($categories as $category => $items) {
    for ($i = 1; $i <= 50; $i++) {
        $base_item = $items[array_rand($items)];
        $name = $base_item . " " . ($i < 10 ? "0".$i : $i);
        $description = "Premium " . $category . " product. " . $name . " designed for quality and style. This item belongs to our exclusive " . $category . " collection.";
        $price = rand(299, 9999);
        $stock = rand(10, 100);
        // Using real sample images from Unsplash based on category to make it look premium
        $category_images = [
            'Electronics' => [
                '1498062363085-0431f753f0ed', '1525547710557-809291baa59d', '1461749280684-dccba630e2f6', 
                '1518770660439-4636190af475', '1496181133206-80ce9b88a853', '1550745165-9bc0b252726f',
                '1526738549149-8e07eca2c1b4', '1491933314544-58b2317f6452', '1588508065123-287b28e013da',
                '1544244015-0df4b3ffc6b0'
            ],
            'Fashion' => [
                '1490481651871-ab68ff25517d', '1549298916-b41d501d3772', '1539106604-147b05ca11d5', 
                '1488161628813-244a26a28644', '1515886657613-9f3515b0c78f', '1543163521-1bf539c55dd2',
                '1556906781-9a412961c28c', '1506152983158-b4a74a01c721', '1467043237213-65f2da53396f',
                '1479064560450-41718b41311b'
            ],
            'Home & Living' => [
                '1616489953149-8d769df8b7de', '1586023492125-27b2c045efd7', '1513694203232-719a280e022f', 
                '1484101403033-57121f21788c', '1556911220-e15b29be8c8f', '1505691938895-1758d7eaa511',
                '1522771739844-6a9f6d5f14af', '1533090161767-e6ffed986c88', '1493663284031-b747a1a440be',
                '1554995207-c18c20360b59'
            ],
            'Books' => [
                '1495446815901-a7297e633e8d', '1512820790803-83ca734da794', '1524995997946-a1c2e315a42f', 
                '1507842217343-583bb7270b66', '1491841223175-0b295f783f67', '1532012190544-29ec99159d81',
                '1516979187457-637abb4f9153', '1497633510901-a1e452153545', '1521587760476-6c12b77488f7',
                '1550399105-df24673c267c'
            ],
            'Beauty' => [
                '1522335789203-aabd1fc54bc9', '1612817288484-6f916006741a', '1596462502278-27bfdc4033c8', 
                '1571781926291-c477ebfd024b', '1556216785-33ec50b8686f', '1596462502278-27bfdc4033c8',
                '1515377604503-65b05740a16c', '1527799858524-8b18dc9283c2', '1601049541210-255883a9cfe1',
                '1526045612213-3fc349793aa5'
            ]
        ];
        
        $img_id = $category_images[$category][array_rand($category_images[$category])];
        $image = "https://images.unsplash.com/photo-" . $img_id . "?w=400&h=400&fit=crop&q=80";
        
        $products[] = [
            'id' => $id++,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock' => $stock,
            'image' => $image,
            'category' => $category
        ];
    }
}

$handle = fopen('data/products.csv', 'w');
fputcsv($handle, ['id', 'name', 'description', 'price', 'stock', 'image', 'category']);
foreach ($products as $product) {
    fputcsv($handle, $product);
}
fclose($handle);

echo "Successfully generated 250 products!";
?>
