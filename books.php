<?php
require_once 'config/database.php';

$pdo = getDBConnection();

// Get search and filter parameters
$search = sanitizeInput($_GET['q'] ?? '');
$genre = (int)($_GET['genre'] ?? 0);
$sort = sanitizeInput($_GET['sort'] ?? 'rating');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

// Build query
$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(b.title LIKE ? OR b.author LIKE ? OR b.description LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($genre > 0) {
    $whereConditions[] = "b.genre_id = ?";
    $params[] = $genre;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Sort options
$sortOptions = [
    'rating' => 'b.average_rating DESC, b.total_reviews DESC',
    'reviews' => 'b.total_reviews DESC, b.average_rating DESC',
    'title' => 'b.title ASC',
    'author' => 'b.author ASC',
    'year' => 'b.publication_year DESC',
    'recent' => 'b.created_at DESC'
];

$orderBy = $sortOptions[$sort] ?? $sortOptions['rating'];

// Get total count for pagination
$countQuery = "SELECT COUNT(*) FROM books b $whereClause";
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalBooks = $countStmt->fetchColumn();
$totalPages = ceil($totalBooks / $limit);

// Get books
$booksQuery = "
    SELECT b.*, g.name as genre_name, g.color as genre_color 
    FROM books b 
    LEFT JOIN genres g ON b.genre_id = g.id 
    $whereClause
    ORDER BY $orderBy
    LIMIT $limit OFFSET $offset
";

$booksStmt = $pdo->prepare($booksQuery);
$booksStmt->execute($params);
$books = $booksStmt->fetchAll();

// Get genres for filter
$genres = $pdo->query("SELECT * FROM genres ORDER BY name")->fetchAll();

$pageTitle = 'Books';
include 'includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="mb-4">
            <h1>Browse Books</h1>
            <p>Discover amazing books from our collection</p>
        </div>

        <!-- Search and Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="" class="grid grid-1 md:grid-3 gap-3">
                    <div class="form-group">
                        <label for="q" class="form-label">Search</label>
                        <input type="text" id="q" name="q" class="form-input" 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Search books, authors...">
                    </div>
                    
                    <div class="form-group">
                        <label for="genre" class="form-label">Genre</label>
                        <select id="genre" name="genre" class="form-select">
                            <option value="">All Genres</option>
                            <?php foreach ($genres as $g): ?>
                                <option value="<?php echo $g['id']; ?>" <?php echo $genre == $g['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($g['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sort" class="form-label">Sort By</label>
                        <select id="sort" name="sort" class="form-select">
                            <option value="rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                            <option value="reviews" <?php echo $sort == 'reviews' ? 'selected' : ''; ?>>Most Reviewed</option>
                            <option value="title" <?php echo $sort == 'title' ? 'selected' : ''; ?>>Title A-Z</option>
                            <option value="author" <?php echo $sort == 'author' ? 'selected' : ''; ?>>Author A-Z</option>
                            <option value="year" <?php echo $sort == 'year' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="recent" <?php echo $sort == 'recent' ? 'selected' : ''; ?>>Recently Added</option>
                        </select>
                    </div>
                    
                    <div class="form-group flex items-end">
                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Info -->
        <div class="flex justify-between items-center mb-4">
            <p class="text-secondary">
                Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $limit, $totalBooks); ?> of <?php echo $totalBooks; ?> books
                <?php if (!empty($search)): ?>
                    for "<?php echo htmlspecialchars($search); ?>"
                <?php endif; ?>
            </p>
            
            <?php if (isLoggedIn()): ?>
            <a href="recommendations.php" class="btn btn-outline">
                <i class="fas fa-magic"></i> Get Recommendations
            </a>
            <?php endif; ?>
        </div>

        <!-- Books Grid -->
        <?php if (empty($books)): ?>
            <div class="text-center py-8">
                <i class="fas fa-search" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                <h3>No books found</h3>
                <p class="text-secondary">Try adjusting your search criteria or browse all books.</p>
                <a href="books.php" class="btn btn-primary mt-3">Browse All Books</a>
            </div>
        <?php else: ?>
            <div class="grid grid-3">
                <?php foreach ($books as $book): ?>
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

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="flex justify-center mt-6">
                <div class="flex gap-2">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                           class="btn btn-outline btn-sm">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                           class="btn <?php echo $i == $page ? 'btn-primary' : 'btn-outline'; ?> btn-sm">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                           class="btn btn-outline btn-sm">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
