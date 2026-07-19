<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Login';

if (is_logged_in()) {
    redirect('./');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    }
    else {
        $user = get_user_by_email($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            redirect('./');
        }
        else {
            $error = 'Invalid email or password';
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid px-0" style="min-height: 100vh; display: flex; align-items: stretch; background: var(--saas-bg-gradient);">
    <div class="row w-100 m-0">
        
        <!-- Left Side: Abstract Graphic / Branding -->
        <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center align-items-center position-relative overflow-hidden" 
             style="background: linear-gradient(135deg, var(--saas-primary), #4338CA); color: white;">
            
            <!-- Abstract Shapes -->
            <div style="position: absolute; top: -10%; left: -10%; width: 50vh; height: 50vh; background: rgba(255,255,255,0.05); border-radius: 50%; blur: 40px;"></div>
            <div style="position: absolute; bottom: -5%; right: -5%; width: 60vh; height: 60vh; background: rgba(255,255,255,0.08); border-radius: 50%; backdrop-filter: blur(20px);"></div>
            <div style="position: absolute; top: 40%; left: 60%; width: 30vh; height: 30vh; background: rgba(255,255,255,0.03); border-radius: 20px; transform: rotate(45deg);"></div>

            <div class="z-1 text-center" style="max-width: 500px;">
                <div class="mb-5">
                    <i class="bi bi-box-seam" style="font-size: 4rem; color: rgba(255,255,255,0.9);"></i>
                </div>
                <h1 class="fw-bold mb-4" style="font-size: 3rem; letter-spacing: -1px; color: white;">Elevate your commerce.</h1>
                <p class="fs-5" style="color: rgba(255,255,255,0.8); line-height: 1.6;">
                    Access your Univault dashboard to manage products, view insights, and securely control your shopping experience.
                </p>
                
                <!-- Testimonial/Trust Badge -->
                <div class="mt-5 p-4 text-start saas-glass-card" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                    <div class="d-flex text-warning mb-2">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="mb-0 fw-medium">"The most secure & seamless shopping platform..."</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center py-5">
            <div class="w-100" style="max-width: 440px; padding: 0 20px;">
                
                <div class="text-center text-lg-start mb-5 d-lg-none">
                    <h2 class="fw-bold text-heading">Admin All Perfact</h2>
                    <p class="text-secondary small">manage ments cleen perfact update cleen</p>
                </div>
                
                <div class="saas-glass-card p-4 p-md-5 animate__animated animate__fadeIn">
                    <h3 class="fw-bold text-center mb-1 text-heading">Admin All Perfact</h3>
                    <p class="text-center text-secondary mb-4">manage ments cleen perfact update cleen</p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-3 border-0 shadow-sm small mb-4 animate__animated animate__headShake">
                            <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php
endif; ?>
                    
                    <form method="POST">
                        <div class="saas-form-group">
                            <label class="saas-label">Email</label>
                            <input type="email" name="email" class="form-control saas-input" 
                                   placeholder="Enter your email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        
                        <div class="saas-form-group">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="saas-label mb-0">Password</label>
                                <a href="#" class="small text-decoration-none fw-medium" style="color: var(--saas-primary);">Forgot password?</a>
                            </div>
                            <input type="password" name="password" class="form-control saas-input" 
                                   placeholder="••••••••" required>
                        </div>
                        
                        <div class="d-grid mb-4 mt-4">
                            <button type="submit" class="saas-btn-primary">
                                Sign in
                            </button>
                        </div>
                        
                        <div class="position-relative mb-4">
                            <hr style="border-color: var(--saas-border-light);">
                            <span class="position-absolute top-50 start-50 translate-middle px-3" style="background: var(--saas-surface-glass); color: var(--saas-text-muted); font-size: 0.85rem;">Or continue with</span>
                        </div>
                        
                        <div class="d-grid mb-4">
                            <button type="button" class="saas-btn-outline w-100">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google" width="18" height="18" class="me-2">
                                Login with Google
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="small text-secondary mb-0">Don't have an account? 
                            <a href="register.php" class="fw-bold text-decoration-none" style="color: var(--saas-primary);">Sign up</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
