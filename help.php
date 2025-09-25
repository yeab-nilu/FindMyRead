<?php
require_once 'config/database.php';

$pageTitle = 'Help Center';
include 'includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Help Center</h3>
            </div>
            <div class="card-body">
                <h4>Getting Started</h4>
                <p>Create an account, search books by title or author, and add them to your lists. Rate books to improve your recommendations.</p>

                <h4>Common Questions</h4>
                <ul>
                    <li>How do I reset my password? — Use the “Forgot your password?” link on the login page.</li>
                    <li>Why don’t I see recommendations? — Rate a few books and add some to your lists.</li>
                    <li>How do I change my reading goals? — Update them from your Dashboard.</li>
                </ul>

                <p>Need more help? <a href="contact.php">Contact us</a> and we’ll assist you.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>


