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
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $existing_user = get_user_by_email($email);
        if ($existing_user) {
            $error = 'Email already registered';
        } else {
            if (add_user($name, $email, $password, 'customer')) {
                $success = 'Successfully registered! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid px-0" style="min-height: 100vh; background-color: #0E1116; display: flex; align-items: center; justify-content: center;">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5 col-xxl-4">
            <div class="text-center mb-5">
                <i class="bi bi-person-plus text-primary display-4 mb-3"></i>
                <h2 class="fw-bold text-white">Join the Platform</h2>
                <p class="text-secondary small">Start your enterprise journey with us</p>
            </div>
            
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__zoomIn bg-white">
                <div class="card-body p-4 p-md-5">
                    <h3 class="fw-bold mb-4 text-center" style="color: #111827;">Create Account</h3>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-4 border-0 shadow-sm small mb-4 animate__animated animate__headShake">
                            <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success rounded-4 border-0 shadow-sm small mb-4 animate__animated animate__bounceIn">
                            <i class="bi bi-check-circle me-2"></i> <?php echo $success; ?>
                            <div class="mt-3"><a href="login.php" class="btn btn-success btn-sm rounded-pill px-4">Go to Login</a></div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase ms-1" style="color: #374151;">Full Name</label>
                            <div class="input-group rounded-pill border overflow-hidden shadow-sm" style="background-color: #F9FAFB; border-color: #E5E7EB !important;">
                                <span class="input-group-text border-0 ps-4" style="background-color: transparent;"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" name="name" class="form-control border-0 py-3 pe-4 shadow-none" 
                                       style="background-color: transparent; color: #111827;" 
                                       placeholder="Enter your full name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase ms-1" style="color: #374151;">Email Address</label>
                            <div class="input-group rounded-pill border overflow-hidden shadow-sm" style="background-color: #F9FAFB; border-color: #E5E7EB !important;">
                                <span class="input-group-text border-0 ps-4" style="background-color: transparent;"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-0 py-3 pe-4 shadow-none" 
                                       style="background-color: transparent; color: #111827;" 
                                       placeholder="your@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-4 g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase ms-1" style="color: #374151;">Password</label>
                                <div class="input-group rounded-pill border overflow-hidden shadow-sm" style="background-color: #F9FAFB; border-color: #E5E7EB !important;">
                                    <input type="password" name="password" class="form-control border-0 py-3 px-4 shadow-none" 
                                           style="background-color: transparent; color: #111827;" 
                                           placeholder="Min 6 chars" required minlength="6">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase ms-1" style="color: #374151;">Confirm</label>
                                <div class="input-group rounded-pill border overflow-hidden shadow-sm" style="background-color: #F9FAFB; border-color: #E5E7EB !important;">
                                    <input type="password" name="confirm_password" class="form-control border-0 py-3 px-4 shadow-none" 
                                           style="background-color: transparent; color: #111827;" 
                                           placeholder="Retype" required minlength="6">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid mb-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow transition-hover" 
                                    style="background-color: #7C3AED; border: none;">
                                Create Account
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="small mb-1" style="color: #6B7280;">Already a member?</p>
                        <a href="login.php" class="fw-bold text-decoration-none" style="color: #7C3AED;">Sign In to Your Account <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
