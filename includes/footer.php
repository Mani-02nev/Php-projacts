    </main>
    
    <footer class="main-footer py-5 border-top border-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <h3 class="fw-bold text-primary mb-3"><?php echo SITE_NAME; ?></h3>
                    <p class="text-secondary small">Your premium online shopping destination for modern lifestyle and digital products.</p>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3 text-body">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo $root_path ?: './'; ?>" class="text-secondary text-decoration-none transition-link">Home</a></li>
                        <li class="mb-2"><a href="<?php echo $root_path; ?>products.php" class="text-secondary text-decoration-none transition-link">Products</a></li>
                        <li class="mb-2"><a href="<?php echo $root_path; ?>fresh-mart.php" class="text-secondary text-decoration-none transition-link">Fresh Mart</a></li>
                        <li class="mb-2"><a href="<?php echo $root_path; ?>local-mart.php" class="text-secondary text-decoration-none transition-link">Local Mart</a></li>
                        <li class="mb-2"><a href="<?php echo $root_path; ?>about.php" class="text-secondary text-decoration-none transition-link">About Us</a></li>
                        <li class="mb-2"><a href="<?php echo $root_path; ?>cart.php" class="text-secondary text-decoration-none transition-link">Cart</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3 text-body">Support</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-secondary text-decoration-none transition-link">Contact Us</a></li>
                        <li class="mb-2"><a href="#" class="text-secondary text-decoration-none transition-link">Shipping Info</a></li>
                        <li class="mb-2"><a href="#" class="text-secondary text-decoration-none transition-link">Returns</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3 text-body">Account</h5>
                    <ul class="list-unstyled">
                        <?php if (is_logged_in()): ?>
                            <li class="mb-2"><a href="<?php echo $root_path; ?>profile.php" class="text-secondary text-decoration-none transition-link">My Profile</a></li>
                            <li class="mb-2"><a href="<?php echo $root_path; ?>logout.php" class="text-secondary text-decoration-none transition-link">Logout</a></li>
                        <?php else: ?>
                            <li class="mb-2"><a href="<?php echo $root_path; ?>login.php" class="text-secondary text-decoration-none transition-link">Login</a></li>
                            <li class="mb-2"><a href="<?php echo $root_path; ?>register.php" class="text-secondary text-decoration-none transition-link">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <div class="pt-4 mt-5 border-top border-light-subtle text-center">
                <p class="text-secondary small mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Three.js (if not already included) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <!-- Add to Cart Animation -->
    <script src="<?php echo $root_path; ?>js/add-to-cart-3d.js"></script>
    <!-- Wishlist Animation -->
    <script src="<?php echo $root_path; ?>js/wishlist-3d.js"></script>
    <!-- AJAX Interactions (No Reload) -->
    <script src="<?php echo $root_path; ?>js/ajax-interactions.js"></script>
    <!-- Scroll Animations -->
    <script src="<?php echo $root_path; ?>js/scroll-animations.js"></script>
    <!-- 3D Card Tilt -->
    <script src="<?php echo $root_path; ?>js/card-3d-tilt.js"></script>
    <script src="<?php echo $root_path; ?>js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Setup Wishlist 3D Effect for all wishlist toggles
            // Matches links with "wishlist_toggle" in href OR class "wishlist-btn"
            if(window.setupWishlistAnimation) {
                window.setupWishlistAnimation('a[href*="wishlist_toggle"], .wishlist-btn');
            }
        });
    </script>
</body>
</html>
