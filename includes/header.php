<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Calculate root path for includes
$is_admin_dir = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$root_path = $is_admin_dir ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- Material Design 3 (MD3) Styles -->
    <link rel="stylesheet" href="<?php echo $root_path; ?>css/md3-design.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="<?php echo $root_path; ?>css/style.css">
    <link rel="stylesheet" href="<?php echo $root_path; ?>css/responsive.css">
    <!-- Enterprise Dark Theme (FINAL OVERRIDE) -->
    <link rel="stylesheet" href="<?php echo $root_path; ?>css/enterprise-light-theme.css">
    <!-- Premium SaaS Theme Structure -->
    <link rel="stylesheet" href="<?php echo $root_path; ?>css/saas-theme.css">
    <!-- Scroll Animations -->
    <link rel="stylesheet" href="<?php echo $root_path; ?>css/scroll-animations.css">
    <!-- 3D Card Styles -->
    <link rel="stylesheet" href="<?php echo $root_path; ?>css/card-3d.css">
    <link rel="icon" type="image/svg+xml" href="<?php echo $root_path; ?>assets/images/logo.svg" />
</head>
<body>

    <header class="main-header sticky-top py-2">
        <div class="container-fluid px-3 px-lg-4 px-xl-5">
            <nav class="navbar navbar-expand-lg p-0">
                <!-- Hamburger Menu Toggler (Mobile) -->
                <button class="navbar-toggler border-0 me-2 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon" style="filter: invert(1);"></span> <!-- Invert for dark icon if header is white -->
                </button>

                <!-- Desktop Brand / Home Link -->
                <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo $root_path; ?>">
                    <div class="logo-wrapper">
                        <img class="logo-img" src="<?php echo $root_path; ?>assets/images/logo.png" alt="<?php echo SITE_NAME; ?>" onerror="this.src='https://via.placeholder.com/45?text=U'">
                    </div>
                    <span class="d-none d-sm-inline brand-text"><?php echo SITE_NAME; ?></span>
                </a>

                <?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
                
                <!-- Desktop Menu -->
                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav gap-1">
                        <li class="nav-item">
                            <a href="<?php echo $root_path ?: './'; ?>" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $root_path; ?>products.php" class="nav-link <?php echo($current_page == 'products.php' || $current_page == 'product-detail.php') && !isset($_GET['fresh']) ? 'active' : ''; ?>">
                                Shop
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo $root_path; ?>local-mart.php" class="nav-link <?php echo $current_page == 'local-mart.php' ? 'active' : ''; ?>" style="<?php echo $current_page == 'local-mart.php' ? '' : ''; ?>">
                                <i class="bi bi-geo-alt-fill me-1" style="color:#6C63FF;"></i> Local Mart
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $root_path; ?>about.php" class="nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">
                                About
                            </a>
                        </li>

                        <?php if (is_admin()): ?>
                            <li class="nav-item">
                                <a href="<?php echo $is_admin_dir ? './' : 'admin/'; ?>" class="nav-link <?php echo strpos($current_page, 'admin') !== false ? 'active' : ''; ?>">
                                    Admin
                                </a>
                            </li>
                        <?php
endif; ?>
                    </ul>
                </div>
                
                <!-- Search Bar (Desktop) -->
                <div class="d-none d-lg-flex flex-grow-1 mx-4 justify-content-end" style="max-width: 320px;">
                    <form action="<?php echo $root_path; ?>products.php" method="GET" class="input-group glass-search-bar w-100" role="search">
                        <input type="search" name="search" placeholder="Search..." class="form-control" aria-label="Search products" autocomplete="off" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="btn" aria-label="Submit search" title="Search">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Right Actions / Desktop & Mobile -->
                <div class="d-flex align-items-center gap-2 ms-auto ms-lg-0">
                    <!-- Mobile Search Toggle -->
                    <button class="btn btn-link p-2 border-0 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSearch" aria-expanded="false">
                        <i class="bi bi-search fs-5 text-secondary"></i>
                    </button>

                    <!-- Desktop Wishlist -->
                    <a href="<?php echo $root_path; ?>wishlist.php" class="d-none d-lg-flex btn btn-link p-2" title="Wishlist">
                        <i class="bi bi-heart-fill fs-5"></i>
                    </a>

                    <!-- Desktop Cart -->
                    <a href="<?php echo $root_path; ?>cart.php" class="d-none d-lg-flex btn btn-link p-2" title="Cart">
                        <i class="bi bi-bag-fill fs-5"></i>
                    </a>

                    <!-- Profile Logic -->
                    <?php if (is_logged_in()): ?>
                        <div class="dropdown">
                            <button class="btn btn-link p-0 border-0" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-3" style="color: var(--saas-primary);"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end saas-glass-card border-0 p-2 text-center" style="min-width: 220px; border-radius: 16px;">
                                <li class="p-3 border-bottom mb-2 bg-light rounded-top" style="border-radius: 12px 12px 0 0;">
                                    <div class="fw-bold" style="color: var(--saas-text-heading); font-size: 1.1rem;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                                    <small class="text-secondary d-block text-truncate"><?php echo htmlspecialchars($_SESSION['user_email']); ?></small>
                                </li>
                                <li><a class="dropdown-item rounded-3 py-2 fw-medium text-start" href="<?php echo $root_path; ?>profile.php"><i class="bi bi-person me-2 text-primary"></i> Profile Dashboard</a></li>
                                <li><a class="dropdown-item rounded-3 py-2 fw-medium text-start" href="<?php echo $root_path; ?>profile.php?tab=orders"><i class="bi bi-box-seam me-2 text-success"></i> My Orders</a></li>
                                <li><a class="dropdown-item rounded-3 py-2 fw-medium text-start" href="<?php echo $root_path; ?>wishlist.php"><i class="bi bi-heart me-2 text-danger"></i> Wishlist</a></li>
                                <?php if (is_admin()): ?>
                                    <li><a class="dropdown-item rounded-3 py-2 fw-medium text-start" href="<?php echo $is_admin_dir ? './' : 'admin/'; ?>"><i class="bi bi-speedometer2 me-2 text-info"></i> Admin Control</a></li>
                                <?php
    endif; ?>
                                <li><hr class="dropdown-divider my-2"></li>
                                <li><a class="dropdown-item rounded-3 py-2 fw-medium text-start text-danger" href="<?php echo $root_path; ?>logout.php"><i class="bi bi-box-arrow-right me-2"></i> Secure Logout</a></li>
                            </ul>
                        </div>
                    <?php
else: ?>
                        <a href="<?php echo $root_path; ?>login.php" class="btn btn-primary rounded-pill px-4 fw-bold ms-2 shadow-sm" style="background-color: #7C3AED; border: none;">
                            Login
                        </a>
                    <?php
endif; ?>
                </div>
                <!-- Mobile Search Collapse -->
                <div class="collapse d-lg-none w-100 mt-2 pb-2" id="mobileSearch">
                    <form action="<?php echo $root_path; ?>products.php" method="GET" class="input-group glass-search-bar" role="search">
                        <input type="search" name="search" placeholder="Search products..." class="form-control" aria-label="Search products" autocomplete="off" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="btn" aria-label="Submit search" title="Search">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>

            </nav>
    </header>

    <!-- Mobile Bottom Nav Logic (Moved Outside Header) -->
    <div class="d-lg-none w-100">
        <ul class="nav nav-pills justify-content-around fixed-bottom shadow-lg py-2 border-top mobile-bottom-navbar" style="z-index: 1050; background-color: var(--light-bg-elevated); border-top: 1px solid var(--light-border-primary) !important;">
            <li class="nav-item">
                <a href="<?php echo $root_path ?: './'; ?>" class="nav-link text-center border-0 p-2 mobile-nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                    <i class="bi bi-house-fill d-block mb-1" style="font-size: 1.5rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 500;">Home</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo $root_path; ?>products.php" class="nav-link text-center border-0 p-2 mobile-nav-link <?php echo($current_page == 'products.php' || $current_page == 'product-detail.php') ? 'active' : ''; ?>">
                    <i class="bi bi-grid-fill d-block mb-1" style="font-size: 1.5rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 500;">Shop</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo $root_path; ?>local-mart.php" class="nav-link text-center border-0 p-2 mobile-nav-link <?php echo $current_page == 'local-mart.php' ? 'active' : ''; ?>">
                    <i class="bi bi-geo-alt-fill d-block mb-1" style="font-size: 1.5rem; color: #6C63FF;"></i>
                    <span style="font-size: 0.7rem; font-weight: 500;">Local Mart</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo $root_path; ?>about.php" class="nav-link text-center border-0 p-2 mobile-nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">
                    <i class="bi bi-info-circle-fill d-block mb-1" style="font-size: 1.5rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 500;">About</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo $root_path; ?>cart.php" class="nav-link text-center border-0 p-2 mobile-nav-link <?php echo $current_page == 'cart.php' ? 'active' : ''; ?>">
                    <i class="bi bi-bag-fill d-block mb-1" style="font-size: 1.5rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 500;">Cart</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?php echo $root_path; ?>wishlist.php" class="nav-link text-center border-0 p-2 mobile-nav-link <?php echo $current_page == 'wishlist.php' ? 'active' : ''; ?>">
                    <i class="bi bi-heart-fill d-block mb-1" style="font-size: 1.5rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 500;">Wishlist</span>
                </a>
            </li>
            <?php if (is_admin()): ?>
            <li class="nav-item">
                <a href="<?php echo $is_admin_dir ? './' : 'admin/'; ?>" class="nav-link text-center border-0 p-2 mobile-nav-link <?php echo strpos($current_page, 'admin') !== false ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2 d-block mb-1" style="font-size: 1.5rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 500;">Admin</span>
                </a>
            </li>
            <?php
endif; ?>
            <li class="nav-item">
                <a href="<?php echo $root_path; ?>profile.php" class="nav-link text-center border-0 p-2 mobile-nav-link <?php echo($current_page == 'profile.php' || $current_page == 'login.php' || $current_page == 'register.php') ? 'active' : ''; ?>">
                    <i class="bi bi-person-circle d-block mb-1" style="font-size: 1.5rem;"></i>
                    <span style="font-size: 0.7rem; font-weight: 500;">Account</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast align-items-center text-body border-0 rounded-4 shadow" role="alert" aria-live="assertive" aria-atomic="true" style="background: var(--light-bg-elevated); border: 1px solid var(--light-border-primary) !important;">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">
                    Hello, world! This is a toast message.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Toast Script -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Force dark theme
        const html = document.documentElement;
        html.setAttribute('data-bs-theme', 'light');
        localStorage.setItem('theme', 'light');
    });

    // Global Toast Function
    function showToast(message, type = 'primary') {
        const toastEl = document.getElementById('liveToast');
        const toastBody = document.getElementById('toastMessage');
        const toast = new bootstrap.Toast(toastEl);
        
        let bgColor = 'var(--accent-purple)';
        if (type === 'success') bgColor = '#059669';
        if (type === 'danger' || type === 'error') bgColor = '#DC2626';
        if (type === 'info') bgColor = 'var(--dark-surface-4)';
        
        toastEl.style.background = bgColor;
        toastBody.innerText = message;
        toast.show();
    }
    </script>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.querySelector('.zoom-container');
        const img = document.getElementById('mainProductImage');

        container.addEventListener('mousemove', (e) => {
            const x = e.clientX - e.target.offsetLeft;
            const y = e.clientY - e.target.offsetTop;
            
            img.style.transformOrigin = `${e.offsetX}px ${e.offsetY}px`;
            img.style.transform = "scale(2)";
        });

        container.addEventListener('mouseleave', () => {
            img.style.transformOrigin = "center";
            img.style.transform = "scale(1)";
        });
    });
    </script>
    
    <main class="main-content flow-in">
