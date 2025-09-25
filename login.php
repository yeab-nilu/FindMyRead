<?php
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            // Update last login
            $stmt = $pdo->prepare("UPDATE users SET updated_at = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            redirect('dashboard.php');
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

$pageTitle = 'Login';
include 'includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="flex justify-center">
            <div class="card" style="max-width: 400px; width: 100%;">
                <div class="card-header text-center">
                    <h2>Welcome Back</h2>
                    <p>Sign in to your FindMyRead account</p>
                </div>
                
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" data-skip-validation>
                        <div class="form-group">
                            <label for="username" class="form-label">Username or Email</label>
                            <input type="text" id="username" name="username" class="form-input" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary w-full">
                                <i class="fas fa-sign-in-alt"></i> Sign In
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="register.php">Sign up here</a></p>
                        <p><a href="forgot-password.php">Forgot your password?</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
