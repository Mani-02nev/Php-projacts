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
    } else {
        $user = get_user_by_email($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            redirect('./');
        } else {
            $error = 'Invalid email or password';
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid px-0" style="min-height: 100vh; background-color: #0E1116; display: flex; align-items: center; justify-content: center;">
    <div class="row w-100 justify-content-center">
        <div class="col-md-5 col-lg-4 col-xxl-3">
            <div class="text-center mb-5">
                <i class="bi bi-shield-lock text-primary display-4 mb-3"></i>
                <h2 class="fw-bold text-white">System Access</h2>
                <p class="text-secondary small">Secure login for customers & administrators</p>
            </div>
            
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__fadeInUp bg-white">
                <div class="card-body p-4 p-md-5">
                    <h3 class="fw-bold mb-4 text-center" style="color: #111827;">Welcome Back</h3>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-4 border-0 shadow-sm small mb-4 animate__animated animate__headShake">
                            <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase ms-1" style="color: #374151;">Email Address</label>
                            <div class="input-group rounded-pill border overflow-hidden shadow-sm" style="background-color: #F9FAFB; border-color: #E5E7EB !important;">
                                <span class="input-group-text border-0 ps-4" style="background-color: transparent;"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-0 py-3 pe-4 shadow-none" 
                                       style="background-color: transparent; color: #111827;" 
                                       placeholder="your@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase ms-1" style="color: #374151;">Password</label>
                            <div class="input-group rounded-pill border overflow-hidden shadow-sm" style="background-color: #F9FAFB; border-color: #E5E7EB !important;">
                                <span class="input-group-text border-0 ps-4" style="background-color: transparent;"><i class="bi bi-key text-muted"></i></span>
                                <input type="password" name="password" class="form-control border-0 py-3 pe-4 shadow-none" 
                                       style="background-color: transparent; color: #111827;" 
                                       placeholder="••••••••" required>
                            </div>
                        </div>
                        
                        <div class="d-grid mb-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow transition-hover" 
                                    style="background-color: #7C3AED; border: none;">
                                Sign In
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="small mb-1" style="color: #6B7280;">New to the platform?</p>
                        <a href="register.php" class="fw-bold text-decoration-none" style="color: #7C3AED;">Create Account Now <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5 small">
                <p class="mb-0" style="color: #4B5563;">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Secure & Encrypted.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
