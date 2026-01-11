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

<div class="container py-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden animate__animated animate__zoomIn">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-5">
                        <div class="bg-primary text-white d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow" style="width: 70px; height: 70px;">
                            <i class="bi bi-person-plus-fill fs-2"></i>
                        </div>
                        <h2 class="fw-bold text-dark">Join Univaut</h2>
                        <p class="text-secondary small">Experience the best in digital shopping</p>
                    </div>
                    
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
                            <label class="form-label small fw-bold text-secondary text-uppercase ms-1">Full Name</label>
                            <div class="input-group rounded-pill border overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" name="name" class="form-control border-0 py-3 pe-4 shadow-none" placeholder="Enter your full name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary text-uppercase ms-1">Email Address</label>
                            <div class="input-group rounded-pill border overflow-hidden shadow-sm">
                                <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-0 py-3 pe-4 shadow-none" placeholder="your@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-4 g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary text-uppercase ms-1">Password</label>
                                <div class="input-group rounded-pill border overflow-hidden shadow-sm">
                                    <input type="password" name="password" class="form-control border-0 py-3 px-4 shadow-none" placeholder="Min 6 chars" required minlength="6">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary text-uppercase ms-1">Confirm</label>
                                <div class="input-group rounded-pill border overflow-hidden shadow-sm">
                                    <input type="password" name="confirm_password" class="form-control border-0 py-3 px-4 shadow-none" placeholder="Retype" required minlength="6">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid mb-4 pt-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow transition-hover">
                                Create Account
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="text-secondary small mb-0">Already a member?</p>
                        <a href="login.php" class="text-primary fw-bold text-decoration-none">Sign In to Your Account <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
