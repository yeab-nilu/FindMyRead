<?php
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $firstName = sanitizeInput($_POST['first_name'] ?? '');
    $lastName = sanitizeInput($_POST['last_name'] ?? '');
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $pdo = getDBConnection();
        
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            $error = 'Username or email already exists.';
        } else {
            // Create user
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $readingPreferences = json_encode([
                'favorite_genres' => [],
                'reading_goal' => 12,
                'preferred_language' => 'English'
            ]);
            
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password_hash, first_name, last_name, reading_preferences) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$username, $email, $passwordHash, $firstName, $lastName, $readingPreferences])) {
                $userId = $pdo->lastInsertId();
                
                // Create reading analytics record
                $stmt = $pdo->prepare("
                    INSERT INTO reading_analytics (user_id, books_read_count, favorite_genres, monthly_reading_goal, yearly_reading_goal) 
                    VALUES (?, 0, '[]', 12, 50)
                ");
                $stmt->execute([$userId]);
                
                // Auto-login
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                
                $success = 'Account created successfully! Welcome to BookWise!';
                redirect('dashboard.php');
            } else {
                $error = 'Failed to create account. Please try again.';
            }
        }
    }
}

$pageTitle = 'Sign Up';
include 'includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="flex justify-center">
            <div class="card" style="max-width: 500px; width: 100%;">
                <div class="card-header text-center">
                    <h2>Join BookWise</h2>
                    <p>Create your account and start discovering amazing books</p>
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
                        <div class="grid grid-2">
                            <div class="form-group">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" id="first_name" name="first_name" class="form-input" 
                                       value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" id="last_name" name="last_name" class="form-input" 
                                       value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" id="username" name="username" class="form-input" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" id="email" name="email" class="form-input" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" id="password" name="password" class="form-input" required>
                            <div class="form-help">Must be at least 6 characters long</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm Password *</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary w-full">
                                <i class="fas fa-user-plus"></i> Create Account
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Already have an account? <a href="login.php">Sign in here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
