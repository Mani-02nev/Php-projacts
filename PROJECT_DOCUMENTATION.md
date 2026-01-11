# Univaut - Professional E-commerce Platform
### College Project Documentation & Deliverables

---

## 1. Project Folder Structure
A clean, modular structure following best practices for PHP development:
```text
project-root/
├── admin/              # Admin dashboard and management pages
│   ├── index.php       # Main admin console
│   ├── orders.php      # Order management
│   └── products.php    # Product inventory management
├── assets/             # Static assets (images, logos)
├── css/                # Custom CSS (style.css, mobile-nav.css)
├── data/               # Persistent Data Storage (CSV files)
│   ├── products.csv    # Product database
│   ├── users.csv       # User accounts
│   └── orders.csv      # Order history
├── includes/           # Reusable server-side logic
│   ├── config.php      # Site constants and CSV helpers
│   ├── functions.php   # Core business logic (Cart, Wishlist, Auth)
│   ├── header.php      # Navigation and Meta tags
│   └── footer.php      # Site footer and Scripts
├── index.php           # Landing page with Featured products
├── products.php        # All products listing with Filters
├── product-detail.php  # Detailed product view with Zoom
├── cart.php            # Shopping cart management
├── wishlist.php        # Saved products page
├── checkout.php        # Order processing flow
├── login.php           # User authentication
└── register.php        # New user enrollment
```

---

## 2. Database Schema (CSV Model)
We use a lightweight, efficient CSV-based database system.

### Products (`products.csv`)
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | Integer | Unique Identifier |
| `name` | String | Product Title |
| `description` | Text | Detailed info |
| `price` | Float | Product cost |
| `stock` | Integer | Inventory count |
| `image` | String | Image URL (Unsplash/Sample) |
| `category` | String | Category (Electronics, Fashion, etc.) |

### Orders (`orders.csv`)
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | Integer | Order Number |
| `user_id` | Integer | Linked User ID |
| `customer_name`| String | Delivery Name |
| `total_amount` | Float | Invoice Total |
| `status` | String | Pending / Shipped / Cancelled |
| `items` | JSON String | Array of product IDs and quantities |

---

## 3. Page-wise Feature Breakdown
- **Homepage**: Mega Hero carousel, Feature highlights, and Animated New Arrivals with Skeleton Loaders.
- **Product Listing**: Sidebar filtering (By Category, Price, Stock), Search bar, and Sort selector.
- **Product Details**: High-resolution image zoom, Stock indicator, and "Recently Viewed" tracking.
- **Wishlist**: Save-for-later functionality with heart icons on all product cards.
- **Cart/Checkout**: Dynamic quantity adjustment, CSRF-safe forms, and multi-step order placement.
- **Admin Panel**: Real-time sales stats, Revenue tracking, and full CRUD operations on inventory.

---

## 4. UI Color Palette & Design
A premium "Glassmorphism" aesthetic with a versatile Dark/Light mode.

- **Primary**: `#3b82f6` (Vibrant Blue)
- **Background (Light)**: `#f8fafc` (Slate 50)
- **Background (Dark)**: `#0f172a` (Slate 900)
- **Accent**: `#1e293b` (Rich Navy)
- **Typography**: Inter (Google Fonts)
- **Animations**: Animate.min.css for smooth page entries.

---

## 5. Reusable Components
- **Navbar**: Smart navigation that adapts active states and shows live counts for Cart/Wishlist.
- **Product Card**: A modular component with hover transformations and favorite toggles.
- **Toast System**: Global JS function `showToast(msg, type)` for instant user feedback.
- **Skeleton Loader**: CSS-based placeholders for improving perceived performance.

---

## 6. Logic Sample (Cart/Wishlist)
```php
// Core function to handle complex state without SQL
function add_to_cart($product_id, $quantity = 1) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}
```

---

## 7. Viva / Project Explanation
**Core Concept**: This project demonstrates the ability to build a robust, scalable web application using pure PHP and CSV storage. It focuses on state management (Sessions), Data Handling (File I/O), and modern UI/UX principles (Bootstrap 5, Responsive Design, and Theme Management). 
**Key Implementation**: The application features a 250+ product database with 5 unique categories, ensuring a rich demo experience for evaluators. It handles user authentication and order processing seamlessly without the overhead of heavy frameworks.
