<?php
require_once 'config/database.php';

$bookId = (int)($_GET['id'] ?? 0);

if (!$bookId) {
    redirect('books.php');
}

$pdo = getDBConnection();

// Get book details
$book = $pdo->prepare("
    SELECT b.*, g.name as genre_name, g.color as genre_color, g.description as genre_description
    FROM books b
    LEFT JOIN genres g ON b.genre_id = g.id
    WHERE b.id = ?
");
$book->execute([$bookId]);
$bookData = $book->fetch();

if (!$bookData) {
    redirect('books.php');
}

// Get book reviews
$reviews = $pdo->prepare("
    SELECT r.*, u.username, u.first_name, u.last_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.book_id = ?
    ORDER BY r.created_at DESC
    LIMIT 10
");
$reviews->execute([$bookId]);
$bookReviews = $reviews->fetchAll();

// Get similar books
$similarBooks = $pdo->prepare("
    SELECT b.*, g.name as genre_name, g.color as genre_color
    FROM books b
    LEFT JOIN genres g ON b.genre_id = g.id
    WHERE b.genre_id = ? AND b.id != ?
    ORDER BY b.average_rating DESC
    LIMIT 4
");
$similarBooks->execute([$bookData['genre_id'], $bookId]);
$similarBooksData = $similarBooks->fetchAll();

// Check if user has this book in their lists
$userLists = [];
if (isLoggedIn()) {
    $userBookLists = $pdo->prepare("
        SELECT list_type FROM user_reading_lists 
        WHERE user_id = ? AND book_id = ?
    ");
    $userBookLists->execute([$_SESSION['user_id'], $bookId]);
    $userLists = $userBookLists->fetchAll(PDO::FETCH_COLUMN);
}

$pageTitle = $bookData['title'];
include 'includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="grid grid-1 md:grid-3 gap-6">
            <!-- Book Cover and Basic Info -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="book-cover" style="width: 200px; height: 300px; margin: 0 auto 1.5rem;">
                        <?php if ($bookData['cover_image_url']): ?>
                            <img src="<?php echo htmlspecialchars($bookData['cover_image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($bookData['title']); ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-book" style="font-size: 4rem;"></i>
                        <?php endif; ?>
                    </div>
                    
                    <h1><?php echo htmlspecialchars($bookData['title']); ?></h1>
                    <p class="text-secondary mb-3">by <?php echo htmlspecialchars($bookData['author']); ?></p>
                    
                    <?php if ($bookData['genre_name']): ?>
                    <div class="mb-3">
                        <span style="background: <?php echo $bookData['genre_color']; ?>; color: white; padding: 0.5rem 1rem; border-radius: var(--radius-md); font-weight: 500;">
                            <?php echo htmlspecialchars($bookData['genre_name']); ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="book-rating mb-3">
                        <div class="stars" style="font-size: 1.2rem;">
                            <?php echo generateStars($bookData['average_rating']); ?>
                        </div>
                        <div class="rating-text" style="font-size: 1rem; margin-top: 0.5rem;">
                            <?php echo formatRating($bookData['average_rating']); ?> out of 5 stars
                        </div>
                        <div class="text-secondary" style="font-size: 0.9rem;">
                            Based on <?php echo $bookData['total_reviews']; ?> reviews
                        </div>
                    </div>
                    <?php if (isLoggedIn()): ?>
                    <div class="mb-3">
                        <div class="flex gap-2" data-book-id="<?php echo $bookId; ?>">
                            <button class="btn btn-secondary btn-sm" onclick="rateBook(<?php echo $bookId; ?>, 1)">★ 1</button>
                            <button class="btn btn-secondary btn-sm" onclick="rateBook(<?php echo $bookId; ?>, 2)">★ 2</button>
                            <button class="btn btn-secondary btn-sm" onclick="rateBook(<?php echo $bookId; ?>, 3)">★ 3</button>
                            <button class="btn btn-secondary btn-sm" onclick="rateBook(<?php echo $bookId; ?>, 4)">★ 4</button>
                            <button class="btn btn-secondary btn-sm" onclick="rateBook(<?php echo $bookId; ?>, 5)">★ 5</button>
                        </div>
                        <small class="text-secondary">Click to rate this book</small>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isLoggedIn()): ?>
                    <div class="book-actions">
                        <?php if (!in_array('want_to_read', $userLists)): ?>
                        <button class="btn btn-primary" onclick="addToList(<?php echo $bookId; ?>, 'want_to_read')">
                            <i class="fas fa-plus"></i> Add to Want to Read
                        </button>
                        <?php else: ?>
                        <span class="btn btn-success">
                            <i class="fas fa-check"></i> In Want to Read List
                        </span>
                        <?php endif; ?>
                        
                        <?php if (!in_array('currently_reading', $userLists)): ?>
                        <button class="btn btn-outline" onclick="addToList(<?php echo $bookId; ?>, 'currently_reading')">
                            <i class="fas fa-book-open"></i> Start Reading
                        </button>
                        <?php else: ?>
                        <span class="btn btn-warning">
                            <i class="fas fa-book-open"></i> Currently Reading
                        </span>
                        <?php endif; ?>

                        <?php if (!in_array('favorites', $userLists)): ?>
                        <button class="btn btn-outline" onclick="addToList(<?php echo $bookId; ?>, 'favorites')">
                            <i class="fas fa-heart"></i> Favorite
                        </button>
                        <?php else: ?>
                        <span class="btn btn-danger">
                            <i class="fas fa-heart"></i> In Favorites
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-secondary">
                        <a href="login.php">Sign in</a> to add this book to your reading lists
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Book Details -->
            <div class="grid grid-1 md:grid-2 gap-4">
                <div class="card">
                    <div class="card-header">
                        <h3>Book Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Author:</strong> <?php echo htmlspecialchars($bookData['author']); ?>
                        </div>
                        
                        <?php if ($bookData['publisher']): ?>
                        <div class="mb-3">
                            <strong>Publisher:</strong> <?php echo htmlspecialchars($bookData['publisher']); ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($bookData['publication_year']): ?>
                        <div class="mb-3">
                            <strong>Publication Year:</strong> <?php echo $bookData['publication_year']; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($bookData['page_count']): ?>
                        <div class="mb-3">
                            <strong>Pages:</strong> <?php echo $bookData['page_count']; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($bookData['language']): ?>
                        <div class="mb-3">
                            <strong>Language:</strong> <?php echo htmlspecialchars($bookData['language']); ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($bookData['isbn']): ?>
                        <div class="mb-3">
                            <strong>ISBN:</strong> <?php echo htmlspecialchars($bookData['isbn']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Description</h3>
                    </div>
                    <div class="card-body">
                        <p><?php echo htmlspecialchars($bookData['description']); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reviews Section -->
        <div class="card mt-6">
            <div class="card-header">
                <h3>Reviews</h3>
            </div>
            <div class="card-body">
                <?php if (empty($bookReviews)): ?>
                    <p class="text-center text-secondary">No reviews yet. Be the first to review this book!</p>
                <?php else: ?>
                    <div class="grid grid-1">
                        <?php foreach ($bookReviews as $review): ?>
                        <div style="border: 1px solid var(--border-light); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1rem;">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4><?php echo htmlspecialchars($review['first_name'] ?: $review['username']); ?></h4>
                                    <div class="stars">
                                        <?php echo generateStars($review['rating']); ?>
                                    </div>
                                </div>
                                <div class="text-secondary text-sm">
                                    <?php echo formatDate($review['created_at']); ?>
                                </div>
                            </div>
                            
                            <?php if ($review['review_text']): ?>
                            <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($review['is_verified_purchase']): ?>
                            <span style="background: var(--success-color); color: white; padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.8rem;">
                                ✓ Verified Purchase
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Similar Books -->
        <?php if (!empty($similarBooksData)): ?>
        <div class="card mt-6">
            <div class="card-header">
                <h3>Similar Books</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-4">
                    <?php foreach ($similarBooksData as $book): ?>
                    <div class="book-card">
                        <div class="book-cover">
                            <?php if ($book['cover_image_url']): ?>
                                <img src="<?php echo htmlspecialchars($book['cover_image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <?php else: ?>
                                <i class="fas fa-book"></i>
                            <?php endif; ?>
                        </div>
                        <div class="book-info">
                            <h4 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h4>
                            <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                            
                            <div class="book-rating">
                                <div class="stars">
                                    <?php echo generateStars($book['average_rating']); ?>
                                </div>
                                <span class="rating-text"><?php echo formatRating($book['average_rating']); ?></span>
                            </div>
                            
                            <div class="book-actions">
                                <a href="book-details.php?id=<?php echo $book['id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
