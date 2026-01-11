    </main>
    
    <footer class="main-footer py-5 border-top border-dark">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <h3 class="fw-bold text-primary mb-3"><?php echo SITE_NAME; ?></h3>
                    <p class="text-secondary small">Your premium online shopping destination for modern lifestyle and digital products.</p>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3 text-white">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-secondary text-decoration-none transition-link">Home</a></li>
                        <li class="mb-2"><a href="products.php" class="text-secondary text-decoration-none transition-link">Products</a></li>
                        <li class="mb-2"><a href="about.php" class="text-secondary text-decoration-none transition-link">About Us</a></li>
                        <li class="mb-2"><a href="cart.php" class="text-secondary text-decoration-none transition-link">Cart</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3 text-white">Support</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-secondary text-decoration-none transition-link">Contact Us</a></li>
                        <li class="mb-2"><a href="#" class="text-secondary text-decoration-none transition-link">Shipping Info</a></li>
                        <li class="mb-2"><a href="#" class="text-secondary text-decoration-none transition-link">Returns</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="fw-bold mb-3 text-white">Account</h5>
                    <ul class="list-unstyled">
                        <?php if (is_logged_in()): ?>
                            <li class="mb-2"><a href="profile.php" class="text-secondary text-decoration-none transition-link">My Profile</a></li>
                            <li class="mb-2"><a href="logout.php" class="text-secondary text-decoration-none transition-link">Logout</a></li>
                        <?php else: ?>
                            <li class="mb-2"><a href="login.php" class="text-secondary text-decoration-none transition-link">Login</a></li>
                            <li class="mb-2"><a href="register.php" class="text-secondary text-decoration-none transition-link">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <div class="pt-4 mt-5 border-top border-secondary text-center">
                <p class="text-secondary small mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
