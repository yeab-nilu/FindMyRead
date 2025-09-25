<?php
require_once 'config/database.php';
requireLogin();

$user = getCurrentUser();
$pdo = getDBConnection();

$listType = sanitizeInput($_GET['list'] ?? 'want_to_read');
$validListTypes = ['want_to_read', 'currently_reading', 'read', 'favorites'];

if (!in_array($listType, $validListTypes)) {
    $listType = 'want_to_read';
}

// Get books in the selected list
$books = $pdo->prepare("
    SELECT url.*, b.title, b.author, b.average_rating, b.total_reviews, 
           b.cover_image_url, b.publication_year, g.name as genre_name, g.color as genre_color
    FROM user_reading_lists url
    JOIN books b ON url.book_id = b.id
    LEFT JOIN genres g ON b.genre_id = g.id
    WHERE url.user_id = ? AND url.list_type = ?
    ORDER BY url.added_at DESC
");
$books->execute([$user['id'], $listType]);
$userBooks = $books->fetchAll();

// Get counts for all lists
$listCounts = $pdo->prepare("
    SELECT list_type, COUNT(*) as count 
    FROM user_reading_lists 
    WHERE user_id = ? 
    GROUP BY list_type
");
$listCounts->execute([$user['id']]);
$listCountsData = $listCounts->fetchAll(PDO::FETCH_KEY_PAIR);

$listLabels = [
    'want_to_read' => 'Want to Read',
    'currently_reading' => 'Currently Reading',
    'read' => 'Read',
    'favorites' => 'Favorites'
];

$pageTitle = 'My Reading Lists';
include 'includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="mb-4">
            <h1>My Reading Lists</h1>
            <p>Manage your personal book collections</p>
        </div>

        <!-- List Navigation -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="flex gap-2 flex-wrap">
                    <?php foreach ($listLabels as $type => $label): ?>
                    <a href="?list=<?php echo $type; ?>" 
                       class="btn <?php echo $listType == $type ? 'btn-primary' : 'btn-outline'; ?>">
                        <?php echo $label; ?>
                        <span class="badge" style="background: rgba(255,255,255,0.2); padding: 0.25rem 0.5rem; border-radius: 10px; margin-left: 0.5rem;">
                            <?php echo $listCountsData[$type] ?? 0; ?>
                        </span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Current List -->
        <div class="card">
            <div class="card-header">
                <h3><?php echo $listLabels[$listType]; ?> (<?php echo count($userBooks); ?> books)</h3>
            </div>
            <div class="card-body">
                <?php if (empty($userBooks)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-list" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                        <h3>No books in this list</h3>
                        <p class="text-secondary mb-4">Start building your reading list by browsing books and adding them to your lists.</p>
                        <a href="books.php" class="btn btn-primary">
                            <i class="fas fa-search"></i> Browse Books
                        </a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-3">
                        <?php foreach ($userBooks as $book): ?>
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
                                
                                <div class="text-secondary text-sm mb-2">
                                    Added <?php echo formatDate($book['added_at']); ?>
                                </div>
                                
                                <div class="book-actions">
                                    <a href="book-details.php?id=<?php echo $book['book_id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                    
                                    <?php if ($listType != 'read'): ?>
                                    <a href="actions/move-to-list.php?book_id=<?php echo $book['book_id']; ?>&from_list=<?php echo $listType; ?>&to_list=read" 
                                       class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Mark as Read
                                    </a>
                                    <?php endif; ?>
                                    
                                    <a href="actions/remove-from-list.php?book_id=<?php echo $book['book_id']; ?>&list_type=<?php echo $listType; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Remove this book from your list?')">
                                        <i class="fas fa-trash"></i> Remove
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Reading Statistics -->
        <div class="card mt-4">
            <div class="card-header">
                <h3>Reading Statistics</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-4">
                    <div class="text-center">
                        <div class="stat-number"><?php echo $listCountsData['read'] ?? 0; ?></div>
                        <div class="stat-label">Books Read</div>
                    </div>
                    <div class="text-center">
                        <div class="stat-number"><?php echo $listCountsData['currently_reading'] ?? 0; ?></div>
                        <div class="stat-label">Currently Reading</div>
                    </div>
                    <div class="text-center">
                        <div class="stat-number"><?php echo $listCountsData['want_to_read'] ?? 0; ?></div>
                        <div class="stat-label">Want to Read</div>
                    </div>
                    <div class="text-center">
                        <div class="stat-number"><?php echo $listCountsData['favorites'] ?? 0; ?></div>
                        <div class="stat-label">Favorites</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
