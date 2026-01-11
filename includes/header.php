<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <a href="index.php">
                        <h1>6<span>X</span>press</h1>
                    </a>
                </div>
                
                <ul class="nav-menu">
                    <li><a href="index.php" class="nav-link"><i class="bi bi-house-door"></i> Home</a></li>
                    <li><a href="products.php" class="nav-link"><i class="bi bi-grid"></i> Products</a></li>
                    <?php if (is_admin()): ?>
                        <li><a href="admin/index.php" class="nav-link"><i class="bi bi-speedometer2"></i> Admin</a></li>
                    <?php endif; ?>
                </ul>
                
                <!-- Search Bar -->
                <div class="nav-search">
                    <form action="products.php" method="GET" style="display: flex; align-items: center;">
                        <input type="text" 
                               name="search" 
                               placeholder="Search products..." 
                               class="search-input"
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="search-btn">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
                
                <div class="nav-actions">
                    <!-- Cart Icon -->
                    <a href="cart.php" class="cart-icon">
                        <i class="bi bi-cart3" style="font-size: 1.25rem;"></i>
                        <span class="cart-count"><?php echo get_cart_count(); ?></span>
                    </a>
                    
                    <!-- Profile Dropdown -->
                    <div class="profile-dropdown">
                        <button class="profile-btn" onclick="toggleProfileMenu()">
                            <?php if (is_logged_in()): ?>
                                <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                            <?php else: ?>
                                <i class="bi bi-person" style="font-size: 1.25rem;"></i>
                            <?php endif; ?>
                        </button>
                        
                        <div class="profile-menu" id="profileMenu">
                            <?php if (is_logged_in()): ?>
                                <div class="profile-menu-header">
                                    <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                                    <div>
                                        <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                                        <small><?php echo htmlspecialchars($_SESSION['user_email']); ?></small>
                                    </div>
                                </div>
                                <div class="profile-menu-divider"></div>
                                <a href="profile.php" class="profile-menu-item">
                                    <i class="bi bi-person"></i>
                                    <span>My Profile</span>
                                </a>
                                <a href="cart.php" class="profile-menu-item">
                                    <i class="bi bi-cart"></i>
                                    <span>My Cart</span>
                                </a>
                                <?php if (is_admin()): ?>
                                    <a href="admin/index.php" class="profile-menu-item">
                                        <i class="bi bi-speedometer2"></i>
                                        <span>Admin Panel</span>
                                    </a>
                                <?php endif; ?>
                                <div class="profile-menu-divider"></div>
                                <a href="logout.php" class="profile-menu-item profile-menu-logout">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Logout</span>
                                </a>
                            <?php else: ?>
                                <a href="login.php" class="profile-menu-item">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                    <span>Login</span>
                                </a>
                                <a href="register.php" class="profile-menu-item">
                                    <i class="bi bi-person-plus"></i>
                                    <span>Register</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    
    <script>
    function toggleProfileMenu() {
        const menu = document.getElementById('profileMenu');
        menu.classList.toggle('show');
    }
    
    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.profile-dropdown')) {
            const menu = document.getElementById('profileMenu');
            menu.classList.remove('show');
        }
    });
    </script>
    
    <main class="main-content">
