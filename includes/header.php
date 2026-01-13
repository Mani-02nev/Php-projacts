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
    <!-- Custom Style (Keeping for premium touches only) -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="icon" type="image/svg+xml" href="<?php echo $root_path; ?>assets/images/logo.svg" />
    <style>
        .logo-img {
            height: 50px;
            width: auto;
            object-fit: contain;
            border: 3px solid #000; /* Subtle border for logo */
            padding: 2px;
            background: #fff;
            transition: transform 0.3s ease;
        }
        .logo-img:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <!-- Motion Canvas Layer -->
    <div class="motion-canvas">
        <div class="breath-orb orb-1"></div>
        <div class="breath-orb orb-2"></div>
        <div class="silk-line" style="top: 10%;"></div>
        <div class="silk-line" style="top: 50%;"></div>
        <div class="silk-line" style="top: 80%;"></div>
    </div>

    <header class="main-header sticky-top py-2">
        <div class="container">
            <nav class="navbar navbar-expand-lg p-0">
                <!-- Hamburger Menu Toggler (Mobile) -->
                <button class="navbar-toggler border-0 me-2 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon" style="filter: invert(1);"></span> <!-- Invert for dark icon if header is white -->
                </button>

                <!-- Desktop Brand / Home Link -->
                <a class="navbar-brand d-flex align-items-center fw-extrabold fs-3 me-auto me-lg-2" href="<?php echo $root_path; ?>index.php" style="color: #000 !important; font-weight: 900;">
                    <div class="logo-wrapper me-2 overflow-hidden border border-3 border-dark bg-white" style="height: 45px; width: 45px;">
                        <img class="w-100 h-100 object-fit-contain" src="<?php echo $root_path; ?>assets/images/logo.png" alt="L" onerror="this.src='https://via.placeholder.com/45?text=U'">
                    </div>
                    <span class="d-none d-sm-inline" style="letter-spacing: -1.5px;"><?php echo SITE_NAME; ?></span>
                </a>

                <?php 
                $current_page = basename($_SERVER['PHP_SELF']); 
                ?>
                
                <!-- Desktop Menu -->
                
                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav gap-3">
                        <li class="nav-item">
                            <a href="<?php echo $root_path; ?>index.php" class="nav-link px-3 fw-medium <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $root_path; ?>products.php" class="nav-link px-3 fw-medium <?php echo ($current_page == 'products.php' || $current_page == 'product-detail.php') ? 'active' : ''; ?>">
                                Shop
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $root_path; ?>about.php" class="nav-link px-3 fw-medium <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">
                                About
                            </a>
                        </li>
                        <?php if (is_admin()): ?>
                            <li class="nav-item">
                                <a href="<?php echo $is_admin_dir ? 'index.php' : 'admin/index.php'; ?>" class="nav-link px-3 fw-medium <?php echo strpos($current_page, 'admin') !== false ? 'active' : ''; ?>">
                                    Admin
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <!-- Search Bar (Desktop) with Glass Effect -->
                <div class="d-none d-lg-flex flex-grow-1 mx-4" style="max-width: 400px;">
                    <form action="<?php echo $root_path; ?>products.php" method="GET" class="input-group glass-search-bar">
                        <input type="text" name="search" placeholder="Search products..." class="form-control rounded-pill-start border-0" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="btn btn-primary rounded-pill-end border-0 px-3">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Right Actions / Desktop & Mobile -->
                <div class="d-flex align-items-center gap-2 gap-md-3 ms-auto ms-lg-0">
                    <!-- Mobile Search Toggle -->
                    <button class="btn btn-link text-indigo p-2 border-0 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileSearch" aria-expanded="false">
                        <i class="bi bi-search fs-5"></i>
                    </button>

                    <!-- Desktop Wishlist -->
                    <a href="<?php echo $root_path; ?>wishlist.php" class="d-none d-lg-block text-gold p-2" title="Wishlist">
                        <i class="bi bi-heart-fill fs-4"></i>
                    </a>

                    <!-- Desktop Cart -->
                    <a href="<?php echo $root_path; ?>cart.php" class="d-none d-lg-block text-indigo p-2" title="Cart">
                        <i class="bi bi-bag-fill fs-4"></i>
                    </a>

                    <!-- Profile Tooltip/Dropdown (Visible on All Screens) -->
                    <div class="dropdown">
                        <button class="btn btn-link p-0 border-0" type="button" id="profileDropdown" data-bs-toggle="dropdown" data-bs-display="static" data-bs-offset="0,10" aria-expanded="false">
                            <i class="bi bi-person-circle fs-3 text-indigo"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 text-center animate__animated animate__fadeIn animate__faster" style="min-width: 240px; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px); border-radius: 1.25rem; z-index: 9999; border: 1px solid rgba(49, 46, 129, 0.1) !important;">
                            <?php if (is_logged_in()): ?>
                                <li class="p-3 border-bottom mb-2 bg-light rounded-top-4">
                                    <div class="fw-bold fs-6 text-primary"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                                    <small class="text-muted d-block text-truncate"><?php echo htmlspecialchars($_SESSION['user_email']); ?></small>
                                </li>
                                <li><a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="<?php echo $root_path; ?>profile.php"><i class="bi bi-person"></i> Profile</a></li>
                                <li><a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="<?php echo $root_path; ?>wishlist.php"><i class="bi bi-heart"></i> Wishlist</a></li>
                                <li><a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="<?php echo $root_path; ?>cart.php"><i class="bi bi-bag"></i> Cart</a></li>
                                <?php if (is_admin()): ?>
                                    <li><a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="<?php echo $is_admin_dir ? 'index.php' : 'admin/index.php'; ?>"><i class="bi bi-speedometer2"></i> Admin Panel</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger rounded-3 py-2 d-flex align-items-center gap-2" href="<?php echo $root_path; ?>logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="<?php echo $root_path; ?>login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a></li>
                                <li><a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2" href="<?php echo $root_path; ?>register.php"><i class="bi bi-person-plus"></i> Register</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <!-- Mobile Search Collapse with Glass Effect -->
                <div class="collapse d-lg-none w-100 mt-2 pb-2" id="mobileSearch">
                    <form action="<?php echo $root_path; ?>products.php" method="GET" class="input-group glass-search-bar">
                        <input type="text" name="search" placeholder="Search products..." class="form-control rounded-pill-start border-0" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit" class="btn btn-primary rounded-pill-end border-0 px-3" style="background: var(--heritage-indigo);">
                            <i class="bi bi-search text-white"></i>
                        </button>
                    </form>
                </div>

                <!-- Mobile Bottom Nav Logic -->
                <div class="d-lg-none w-100">
                    <ul class="nav nav-pills justify-content-around fixed-bottom shadow-lg py-2 border-top mobile-bottom-navbar" style="z-index: 1050;">
                        <li class="nav-item">
                            <a href="<?php echo $root_path; ?>index.php" class="nav-link text-center border-0 p-2 mobile-nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                                <i class="bi bi-house-fill d-block mb-1" style="font-size: 1.5rem;"></i>
                                <span style="font-size: 0.7rem; font-weight: 500;">Home</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $root_path; ?>products.php" class="nav-link text-center border-0 p-2 mobile-nav-link <?php echo ($current_page == 'products.php' || $current_page == 'product-detail.php') ? 'active' : ''; ?>">
                                <i class="bi bi-grid-fill d-block mb-1" style="font-size: 1.5rem;"></i>
                                <span style="font-size: 0.7rem; font-weight: 500;">Shop</span>
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
                        <li class="nav-item">
                            <a href="<?php echo $root_path; ?>profile.php" class="nav-link text-center border-0 p-2 mobile-nav-link <?php echo ($current_page == 'profile.php' || $current_page == 'login.php' || $current_page == 'register.php') ? 'active' : ''; ?>">
                                <i class="bi bi-person-circle d-block mb-1" style="font-size: 1.5rem;"></i>
                                <span style="font-size: 0.7rem; font-weight: 500;">Account</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
    </header>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast align-items-center text-white bg-primary border-0 rounded-4 shadow" role="alert" aria-live="assertive" aria-atomic="true">
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
        // Force light theme only
        const html = document.documentElement;
        html.setAttribute('data-bs-theme', 'light');
        localStorage.setItem('theme', 'light');
    });

    // Global Toast Function
    function showToast(message, type = 'primary') {
        const toastEl = document.getElementById('liveToast');
        const toastBody = document.getElementById('toastMessage');
        const toast = new bootstrap.Toast(toastEl);
        
        toastEl.className = `toast align-items-center text-white bg-${type} border-0 rounded-4 shadow`;
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
