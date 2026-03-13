<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Create Account';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields';
    }
    elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    }
    elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    }
    else {
        $existing_user = get_user_by_email($email);
        if ($existing_user) {
            $error = 'Email already registered';
        }
        else {
            if (add_user($name, $email, $password, 'customer')) {
                $success = 'Successfully registered! You can now login.';
            }
            else {
                $error = 'Registration failed. Please try again.';
            }
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
        
        <!-- Right Side: Register Form -->
        <div class="col-12 col-lg-6 d-flex align-items-center justify-content-center py-5">
            <div class="w-100" style="max-width: 480px; padding: 0 20px;">
                
                <div class="text-center text-lg-start mb-5 d-lg-none">
                    <h2 class="fw-bold text-heading">Join the Platform</h2>
                    <p class="text-secondary small">Start your enterprise journey with us</p>
                </div>
                
                <div class="saas-glass-card p-4 p-md-5 animate__animated animate__fadeIn">
                    <h3 class="fw-bold text-center mb-1 text-heading">Create Account</h3>
                    <p class="text-center text-secondary mb-4">Please fill in your details to get started.</p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-3 border-0 shadow-sm small mb-4 animate__animated animate__headShake">
                            <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php
endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success rounded-3 border-0 shadow-sm small mb-4 animate__animated animate__bounceIn">
                            <i class="bi bi-check-circle me-2"></i> <?php echo $success; ?>
                            <div class="mt-3"><a href="login.php" class="btn btn-sm btn-success rounded-3 px-4">Go to Login</a></div>
                        </div>
                    <?php
endif; ?>
                    
                    <form method="POST">
                        <div class="saas-form-group">
                            <label class="saas-label">Full Name</label>
                            <input type="text" name="name" class="form-control saas-input" 
                                   placeholder="Enter your full name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>

                        <div class="saas-form-group">
                            <label class="saas-label">Email Address</label>
                            <input type="email" name="email" class="form-control saas-input" 
                                   placeholder="your@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        
                        <div class="row g-3 saas-form-group">
                            <div class="col-md-6">
                                <label class="saas-label">Password</label>
                                <input type="password" name="password" class="form-control saas-input" 
                                       placeholder="Min 6 chars" required minlength="6">
                            </div>
                            <div class="col-md-6">
                                <label class="saas-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control saas-input" 
                                       placeholder="Retype password" required minlength="6">
                            </div>
                        </div>
                        
                        <div class="d-grid mb-4 mt-4">
                            <button type="submit" class="saas-btn-primary">
                                Create Account
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="small text-secondary mb-0">Already a member? 
                            <a href="login.php" class="fw-bold text-decoration-none" style="color: var(--saas-primary);">Sign In here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
