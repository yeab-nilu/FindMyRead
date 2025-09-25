<?php
require_once 'config/database.php';

$pageTitle = 'Contact';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $to = 'yabsiradelelegne@gmail.com';
        $subject = 'New contact message from FindMyRead';
        $body = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}\n";

        // Build headers
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'From: FindMyRead <no-reply@FindMyRead.local>';
        $headers[] = 'Reply-To: ' . $email;
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        $sent = @mail($to, $subject, $body, implode("\r\n", $headers));
        if ($sent) {
            $success = 'Thanks for reaching out! Your message has been sent.';
            $_POST = [];
        } else {
            $error = 'Sorry, we could not send your message right now. Please try again later.';
        }
    }
}

include 'includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="grid grid-2">
            <div class="card">
                <div class="card-header">
                    <h3>Contact Us</h3>
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
                            <label class="form-label" for="name">Name</label>
                            <input class="form-input" type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-input" type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="message">Message</label>
                            <textarea class="form-textarea" id="message" name="message" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i> Send</button>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3>Our Details</h3>
                </div>
                <div class="card-body">
                    <p><strong>Email:</strong> support@FindMyRead.local</p>
                    <p><strong>Hours:</strong> Mon–Fri, 9:00–17:00</p>
                    <p>We typically respond within 1–2 business days.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>


