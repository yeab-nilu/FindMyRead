<?php
require_once 'config/database.php';

// Get featured books
$pdo = getDBConnection();
$featuredBooks = $pdo->query("
    SELECT b.*, g.name as genre_name, g.color as genre_color 
    FROM books b 
    LEFT JOIN genres g ON b.genre_id = g.id 
    ORDER BY b.average_rating DESC, b.total_reviews DESC 
    LIMIT 6
")->fetchAll();

// Get popular genres
$popularGenres = $pdo->query("
    SELECT g.*, COUNT(b.id) as book_count 
    FROM genres g 
    LEFT JOIN books b ON g.id = b.genre_id 
    GROUP BY g.id 
    ORDER BY book_count DESC 
    LIMIT 8
")->fetchAll();

// Get recent reviews
$recentReviews = $pdo->query("
    SELECT r.*, b.title as book_title, b.author as book_author, u.username 
    FROM reviews r 
    JOIN books b ON r.book_id = b.id 
    JOIN users u ON r.user_id = u.id 
    ORDER BY r.created_at DESC 
    LIMIT 3
")->fetchAll();

$pageTitle = 'Home';
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Discover Your Next Favorite Book</h1>
            <p>Get personalized book recommendations based on your reading preferences and discover amazing stories from our curated collection.</p>
            
            <div class="search-container">
                <form action="search.php" method="GET" class="search-form">
                    <input type="text" name="q" class="search-bar" placeholder="Search for books, authors, or genres..." required>
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Featured Books Section -->
<section class="section">
    <div class="container">
        <div class="text-center mb-4">
            <h2>Featured Books</h2>
            <p>Handpicked recommendations from our collection</p>
        </div>
        
        <div class="grid grid-3">
            <?php foreach ($featuredBooks as $book): ?>
            <div class="book-card">
                <div class="book-cover">
                    <?php if ($book['cover_image_url']): ?>
                        <img src="<?php echo htmlspecialchars($book['cover_image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                    <?php else: ?>
                        <i class="fas fa-book"></i>
                    <?php endif; ?>
                </div>
                <div class="book-info">
                    <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                    
                    <div class="book-rating">
                        <div class="stars">
                            <?php echo generateStars($book['average_rating']); ?>
                        </div>
                        <span class="rating-text"><?php echo formatRating($book['average_rating']); ?> (<?php echo $book['total_reviews']; ?> reviews)</span>
                    </div>
                    
                    <div class="book-actions">
                        <a href="book-details.php?id=<?php echo $book['id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        <?php if (isLoggedIn()): ?>
                        <button class="btn btn-outline btn-sm" onclick="addToList(<?php echo $book['id']; ?>, 'want_to_read')">
                            <i class="fas fa-plus"></i> Add to List
                        </button>
                        <button class="btn btn-outline btn-sm" onclick="addToList(<?php echo $book['id']; ?>, 'favorites')">
                            <i class="fas fa-heart"></i> Favorite
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="books.php" class="btn btn-primary">
                <i class="fas fa-book"></i> Browse All Books
            </a>
        </div>
    </div>
</section>

<!-- Popular Genres Section -->
<section class="section" style="background: var(--bg-primary);">
    <div class="container">
        <div class="text-center mb-4">
            <h2>Popular Genres</h2>
            <p>Explore books by category</p>
        </div>
        
        <div class="grid grid-4">
            <?php foreach ($popularGenres as $genre): ?>
            <a href="books.php?genre=<?php echo $genre['id']; ?>" class="card text-center" style="text-decoration: none; color: inherit;">
                <div class="card-body">
                    <div style="width: 60px; height: 60px; background: <?php echo $genre['color']; ?>; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                        <i class="fas fa-book"></i>
                    </div>
                    <h4><?php echo htmlspecialchars($genre['name']); ?></h4>
                    <p><?php echo $genre['book_count']; ?> books</p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Recent Reviews Section -->
<?php if (!empty($recentReviews)): ?>
<section class="section">
    <div class="container">
        <div class="text-center mb-4">
            <h2>Recent Reviews</h2>
            <p>What our readers are saying</p>
        </div>
        
        <div class="grid grid-3">
            <?php foreach ($recentReviews as $review): ?>
            <div class="card">
                <div class="card-body">
                    <div class="book-rating mb-2">
                        <div class="stars">
                            <?php echo generateStars($review['rating']); ?>
                        </div>
                        <span class="rating-text"><?php echo $review['rating']; ?>/5</span>
                    </div>
                    
                    <h4><?php echo htmlspecialchars($review['book_title']); ?></h4>
                    <p class="text-secondary">by <?php echo htmlspecialchars($review['book_author']); ?></p>
                    
                    <p class="mb-2"><?php echo htmlspecialchars(substr($review['review_text'], 0, 150)); ?><?php echo strlen($review['review_text']) > 150 ? '...' : ''; ?></p>
                    
                    <div class="flex justify-between items-center">
                        <small class="text-light">by <?php echo htmlspecialchars($review['username']); ?></small>
                        <small class="text-light"><?php echo formatDate($review['created_at']); ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Call to Action Section -->
<section class="section" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white;">
    <div class="container text-center">
        <h2>Ready to Find Your Next Great Read?</h2>
        <p class="mb-4">Join thousands of readers who have discovered their favorite books through our recommendation system.</p>
        
        <?php if (isLoggedIn()): ?>
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-tachometer-alt"></i> Go to Dashboard
            </a>
        <?php else: ?>
            <a href="register.php" class="btn btn-secondary">
                <i class="fas fa-user-plus"></i> Get Started Free
            </a>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
