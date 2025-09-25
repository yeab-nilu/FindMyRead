<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="description" content="FindMyRead - Discover your next favorite book with our intelligent recommendation system. Browse thousands of books, read reviews, and get personalized suggestions.">
    <meta name="keywords" content="books, recommendations, reading, reviews, book discovery, literature">
    <meta name="author" content="FindMyRead Team">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?>">
    <meta property="og:description" content="Discover your next favorite book with our intelligent recommendation system.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo APP_URL . ($_SERVER['REQUEST_URI'] ?? ''); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">
                <i class="fas fa-book-open"></i>
                <span>FindMyRead</span>
            </a>
            
            <div class="nav-menu" id="navMenu">
                <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="books.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'books.php' ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i> Books
                </a>
                <a href="genres.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'genres.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tags"></i> Genres
                </a>
                <a href="recommendations.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'recommendations.php' ? 'active' : ''; ?>">
                    <i class="fas fa-magic"></i> Recommendations
                </a>
                
                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="reading-lists.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reading-lists.php' ? 'active' : ''; ?>">
                        <i class="fas fa-list"></i> My Lists
                    </a>
                    <div class="nav-dropdown">
                        <a href="#" class="nav-link dropdown-toggle">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="profile.php">
                                <i class="fas fa-user-circle"></i> Profile
                            </a>
                            <a href="analytics.php">
                                <i class="fas fa-chart-bar"></i> Analytics
                            </a>
                            <a href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="register.php" class="nav-link nav-cta <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user-plus"></i> Sign Up
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="nav-toggle" id="navToggle">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </nav>

    <main class="main-content">
