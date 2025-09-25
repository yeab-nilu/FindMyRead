<?php
require_once 'config/database.php';

// Destroy session
session_destroy();

// Clear session data
$_SESSION = [];

// Redirect to home page
redirect('index.php');
?>
