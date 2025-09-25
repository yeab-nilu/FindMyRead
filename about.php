<?php
require_once 'config/database.php';

$pageTitle = 'About Us';
include 'includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>About FindMyRead</h3>
            </div>
            <div class="card-body">
                <p><strong>FindMyRead is a university project</strong> created by students passionate about both web development and reading.</p>
                
                <p>This platform combines our interest in books with our journey learning full-stack development - 
                   building everything from the frontend interface to the backend database systems.</p>
                
                <h4>What FindMyRead Offers:</h4>
                <ul>
                    <li>Smart book search and discovery</li>
                    <li>Curated genre collections</li>
                    <li>Personal reading lists</li>
                    <li>Progress tracking for your books</li>
                </ul>
                
                <p>As this is a learning project, we are constantly improving and adding new features. 
                   Your feedback is welcome via the <a href="contact.php">Contact page</a>!</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>