<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Login';

// Redirect if already logged in
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

<div class="container">
    <div class="form-container">
        <h1 style="text-align: center; margin-bottom: 2rem;">Login to 6Xpress</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control" 
                       required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-control" 
                       required>
            </div>
            
            <button type="submit" class="btn btn-black" style="width: 100%; margin-top: 1rem;">
                Login
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 2rem;">
            Don't have an account? <a href="register.php" style="color: var(--black); font-weight: 600;">Register here</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
