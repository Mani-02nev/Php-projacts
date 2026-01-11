<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Login';

if (is_logged_in()) {
    redirect('index.php');
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
            redirect('index.php');
        } else {
            $error = 'Invalid email or password';
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__fadeInUp">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-5">
                        <div class="bg-primary text-white d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow" style="width: 70px; height: 70px;">
                            <i class="bi bi-shield-lock-fill fs-2"></i>
                        </div>
                        <h2 class="fw-bold text-dark">Welcome Back</h2>
                        <p class="text-secondary small">Login to access your orders and wishlist</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-4 border-0 shadow-sm small mb-4 animate__animated animate__headShake">
                            <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase ms-1">Email Address</label>
                            <div class="input-group rounded-pill border overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-0 py-3 pe-4 shadow-none" placeholder="your@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase ms-1">Password</label>
                            <div class="input-group rounded-pill border overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-key text-muted"></i></span>
                                <input type="password" name="password" class="form-control border-0 py-3 pe-4 shadow-none" placeholder="••••••••" required>
                            </div>
                        </div>
                        
                        <div class="d-grid mb-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow transition-hover">
                                Sign In
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="text-secondary small mb-0">Don't have an account?</p>
                        <a href="register.php" class="text-primary fw-bold text-decoration-none">Create Account Now <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5 text-secondary small">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> Project. Secure & Encrypted.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
