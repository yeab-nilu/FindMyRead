<?php
require_once 'config/database.php';

$query = sanitizeInput($_GET['q'] ?? '');
$pageTitle = 'Search Results';

if (empty($query)) {
    redirect('books.php');
}

$pdo = getDBConnection();

// Search books
$searchQuery = $pdo->prepare("
    SELECT b.*, g.name as genre_name, g.color as genre_color 
    FROM books b 
    LEFT JOIN genres g ON b.genre_id = g.id 
    WHERE b.title LIKE ? OR b.author LIKE ? OR b.description LIKE ?
    ORDER BY b.average_rating DESC, b.total_reviews DESC
    LIMIT 50
");

$searchTerm = "%$query%";
$searchQuery->execute([$searchTerm, $searchTerm, $searchTerm]);
$searchResults = $searchQuery->fetchAll();

include 'includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="mb-4">
            <h1>Search Results</h1>
            <p>Searching for: "<strong><?php echo htmlspecialchars($query); ?></strong>"</p>
        </div>

        <?php if (empty($searchResults)): ?>
            <div class="text-center py-8">
                <i class="fas fa-search" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                <h3>No books found</h3>
                <p class="text-secondary mb-4">Try different keywords or browse all books.</p>
                <a href="books.php" class="btn btn-primary">Browse All Books</a>
            </div>
        <?php else: ?>
            <div class="mb-4">
                <p class="text-secondary">Found <?php echo count($searchResults); ?> book(s)</p>
            </div>

            <div class="grid grid-3">
                <?php foreach ($searchResults as $book): ?>
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
                        <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                        
                        <?php if ($book['genre_name']): ?>
                        <div class="mb-2">
                            <span style="background: <?php echo $book['genre_color']; ?>; color: white; padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.8rem;">
                                <?php echo htmlspecialchars($book['genre_name']); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="book-rating">
                            <div class="stars">
                                <?php echo generateStars($book['average_rating']); ?>
                            </div>
                            <span class="rating-text"><?php echo formatRating($book['average_rating']); ?> (<?php echo $book['total_reviews']; ?>)</span>
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
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
