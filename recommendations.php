<?php
require_once 'config/database.php';
requireLogin();

$user = getCurrentUser();
$pdo = getDBConnection();

// Get user's reading preferences
$preferences = json_decode($user['reading_preferences'] ?? '{}', true);
$favoriteGenres = $preferences['favorite_genres'] ?? [];

// Generate recommendations if requested
if (isset($_GET['generate']) && $_GET['generate'] == '1') {
    generateRecommendations($user['id'], $pdo);
    redirect('recommendations.php');
}

// Get existing recommendations
$recommendations = $pdo->prepare("
    SELECT br.*, b.title, b.author, b.description, b.average_rating, b.total_reviews, 
           b.cover_image_url, b.publication_year, g.name as genre_name, g.color as genre_color
    FROM book_recommendations br
    JOIN books b ON br.recommended_book_id = b.id
    LEFT JOIN genres g ON b.genre_id = g.id
    WHERE br.user_id = ?
    ORDER BY br.confidence_score DESC, br.created_at DESC
");

$recommendations->execute([$user['id']]);
$userRecommendations = $recommendations->fetchAll();

// Get user's reading history for context
$readingHistory = $pdo->prepare("
    SELECT b.*, url.list_type, g.name as genre_name
    FROM user_reading_lists url
    JOIN books b ON url.book_id = b.id
    LEFT JOIN genres g ON b.genre_id = g.id
    WHERE url.user_id = ?
    ORDER BY url.added_at DESC
    LIMIT 10
");
$readingHistory->execute([$user['id']]);
$userHistory = $readingHistory->fetchAll();

$pageTitle = 'Recommendations';
include 'includes/header.php';

// Function to generate recommendations
function generateRecommendations($userId, $pdo) {
    // Clear existing recommendations
    $pdo->prepare("DELETE FROM book_recommendations WHERE user_id = ?")->execute([$userId]);
    
    // Get user's reading preferences and history
    $userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $userStmt->execute([$userId]);
    $user = $userStmt->fetch();
    $preferences = json_decode($user['reading_preferences'] ?? '{}', true);
    $favoriteGenres = $preferences['favorite_genres'] ?? [];
    
    // Get user's read books and ratings
    $readBooks = $pdo->prepare("
        SELECT b.*, g.name as genre_name, r.rating as user_rating
        FROM user_reading_lists url
        JOIN books b ON url.book_id = b.id
        LEFT JOIN genres g ON b.genre_id = g.id
        LEFT JOIN reviews r ON r.user_id = url.user_id AND r.book_id = url.book_id
        WHERE url.user_id = ? AND url.list_type = 'read'
    ");
    $readBooks->execute([$userId]);
    $userReadBooks = $readBooks->fetchAll();
    
    $recommendations = [];
    
    // Derive weighted genre preferences from reads and ratings
    $weightedGenres = [];
    foreach ($userReadBooks as $rb) {
        if (!empty($rb['genre_name'])) {
            $weight = isset($rb['user_rating']) && $rb['user_rating'] > 0 ? (int)$rb['user_rating'] : 3; // default mid-weight if unrated
            $weightedGenres[$rb['genre_name']] = ($weightedGenres[$rb['genre_name']] ?? 0) + $weight;
        }
    }
    arsort($weightedGenres);
    $topWeightedGenres = array_slice(array_keys($weightedGenres), 0, 5);

    // Merge with explicit favorite genres
    $effectiveGenres = array_values(array_unique(array_merge($topWeightedGenres, $favoriteGenres)));

    // 1. Content-based recommendations based on effective genres and user ratings
    if (!empty($effectiveGenres)) {
        $genrePlaceholders = str_repeat('?,', count($effectiveGenres) - 1) . '?';
        $contentBased = $pdo->prepare("
            SELECT b.*, g.name as genre_name, g.color as genre_color
            FROM books b
            LEFT JOIN genres g ON b.genre_id = g.id
            WHERE g.name IN ($genrePlaceholders)
            AND b.id NOT IN (
                SELECT book_id FROM user_reading_lists 
                WHERE user_id = ? AND list_type IN ('read', 'currently_reading')
            )
            ORDER BY b.average_rating DESC, b.total_reviews DESC
            LIMIT 10
        ");
        $contentBased->execute(array_merge($effectiveGenres, [$userId]));
        $contentBooks = $contentBased->fetchAll();
        
        foreach ($contentBooks as $book) {
            $recommendations[] = [
                'book' => $book,
                'reason' => "Based on your interest in {$book['genre_name']} and your ratings",
                'algorithm' => 'content_based',
                'confidence' => 0.8
            ];
        }
    }
    
    // 2. Collaborative filtering - emphasize overlap with highly rated reads
    if (!empty($userReadBooks)) {
        $highlyRatedIds = array_column(array_filter($userReadBooks, function($b) { return (int)($b['user_rating'] ?? 0) >= 4; }), 'id');
        $readBookIds = !empty($highlyRatedIds) ? $highlyRatedIds : array_column($userReadBooks, 'id');
        $placeholders = str_repeat('?,', count($readBookIds) - 1) . '?';
        
        $collaborative = $pdo->prepare("
            SELECT b.*, g.name as genre_name, g.color as genre_color,
                   COUNT(DISTINCT url2.user_id) as similar_users
            FROM books b
            LEFT JOIN genres g ON b.genre_id = g.id
            JOIN user_reading_lists url2 ON b.id = url2.book_id
            WHERE url2.user_id != ? 
            AND url2.list_type = 'read'
            AND url2.book_id IN (
                SELECT book_id FROM user_reading_lists 
                WHERE user_id IN (
                    SELECT DISTINCT url3.user_id 
                    FROM user_reading_lists url3 
                    WHERE url3.book_id IN ($placeholders) 
                    AND url3.user_id != ?
                    AND url3.list_type = 'read'
                )
            )
            AND b.id NOT IN ($placeholders)
            GROUP BY b.id
            HAVING similar_users >= 2
            ORDER BY similar_users DESC, b.average_rating DESC, b.total_reviews DESC
            LIMIT 8
        ");
        $collaborative->execute(array_merge([$userId], $readBookIds, [$userId], $readBookIds));
        $collabBooks = $collaborative->fetchAll();
        
        foreach ($collabBooks as $book) {
            $recommendations[] = [
                'book' => $book,
                'reason' => "Liked by {$book['similar_users']} readers who enjoyed books you rated highly",
                'algorithm' => 'collaborative',
                'confidence' => min(0.9, 0.6 + ((int)$book['similar_users'] * 0.05))
            ];
        }
    }
    
    // 3. Popular books in user's preferred genres
    if (!empty($favoriteGenres)) {
        $genrePlaceholders = str_repeat('?,', count($favoriteGenres) - 1) . '?';
        $popular = $pdo->prepare("
            SELECT b.*, g.name as genre_name, g.color as genre_color
            FROM books b
            LEFT JOIN genres g ON b.genre_id = g.id
            WHERE g.name IN ($genrePlaceholders)
            AND b.total_reviews >= 10
            AND b.average_rating >= 4.0
            AND b.id NOT IN (
                SELECT book_id FROM user_reading_lists 
                WHERE user_id = ? AND list_type IN ('read', 'currently_reading')
            )
            ORDER BY b.total_reviews DESC, b.average_rating DESC
            LIMIT 6
        ");
        $popular->execute(array_merge($favoriteGenres, [$userId]));
        $popularBooks = $popular->fetchAll();
        
        foreach ($popularBooks as $book) {
            $recommendations[] = [
                'book' => $book,
                'reason' => "Popular {$book['genre_name']} book with great reviews",
                'algorithm' => 'popular',
                'confidence' => 0.7
            ];
        }
    }
    
    // 4. Trending books (recently added with good ratings)
    $trending = $pdo->prepare("
        SELECT b.*, g.name as genre_name, g.color as genre_color
        FROM books b
        LEFT JOIN genres g ON b.genre_id = g.id
        WHERE b.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        AND b.average_rating >= 4.0
        AND b.total_reviews >= 5
        AND b.id NOT IN (
            SELECT book_id FROM user_reading_lists 
            WHERE user_id = ? AND list_type IN ('read', 'currently_reading')
        )
        ORDER BY b.average_rating DESC, b.total_reviews DESC
        LIMIT 4
    ");
    $trending->execute([$userId]);
    $trendingBooks = $trending->fetchAll();
    
    foreach ($trendingBooks as $book) {
        $recommendations[] = [
            'book' => $book,
            'reason' => "Trending book with excellent reviews",
            'algorithm' => 'trending',
            'confidence' => 0.75
        ];
    }
    
    // Remove duplicates and sort by confidence
    $uniqueRecommendations = [];
    $seenBooks = [];
    
    foreach ($recommendations as $rec) {
        $bookId = $rec['book']['id'];
        if (!in_array($bookId, $seenBooks)) {
            $uniqueRecommendations[] = $rec;
            $seenBooks[] = $bookId;
        }
    }
    
    // Sort by confidence score
    usort($uniqueRecommendations, function($a, $b) {
        return $b['confidence'] <=> $a['confidence'];
    });
    
    // Insert recommendations into database
    $insertStmt = $pdo->prepare("
        INSERT INTO book_recommendations (user_id, recommended_book_id, recommendation_reason, algorithm_used, confidence_score)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    foreach (array_slice($uniqueRecommendations, 0, 20) as $rec) {
        $insertStmt->execute([
            $userId,
            $rec['book']['id'],
            $rec['reason'],
            $rec['algorithm'],
            $rec['confidence']
        ]);
    }
}
?>

<div class="section">
    <div class="container">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1>Your Recommendations</h1>
                <p>Personalized book suggestions based on your reading preferences</p>
            </div>
            <a href="?generate=1" class="btn btn-primary">
                <i class="fas fa-sync"></i> Generate New Recommendations
            </a>
        </div>

        <!-- Reading Context -->
        <?php if (!empty($userHistory)): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h3>Based on Your Reading History</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-5">
                    <?php foreach (array_slice($userHistory, 0, 5) as $book): ?>
                    <div class="text-center">
                        <div style="width: 60px; height: 80px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: var(--radius-sm); margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-book"></i>
                        </div>
                        <h4 style="font-size: 0.8rem; margin-bottom: 0.25rem;"><?php echo htmlspecialchars(substr($book['title'], 0, 20)) . (strlen($book['title']) > 20 ? '...' : ''); ?></h4>
                        <p style="font-size: 0.7rem; color: var(--text-secondary);"><?php echo ucfirst(str_replace('_', ' ', $book['list_type'])); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recommendations -->
        <?php if (empty($userRecommendations)): ?>
            <div class="text-center py-8">
                <i class="fas fa-magic" style="font-size: 3rem; color: var(--text-light); margin-bottom: 1rem;"></i>
                <h3>No recommendations yet</h3>
                <p class="text-secondary mb-4">Start by rating some books or adding them to your reading lists to get personalized recommendations.</p>
                <a href="?generate=1" class="btn btn-primary">
                    <i class="fas fa-sync"></i> Generate Recommendations
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-3">
                <?php foreach ($userRecommendations as $rec): ?>
                <div class="book-card">
                    <div class="book-cover">
                        <?php if ($rec['cover_image_url']): ?>
                            <img src="<?php echo htmlspecialchars($rec['cover_image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($rec['title']); ?>">
                        <?php else: ?>
                            <i class="fas fa-book"></i>
                        <?php endif; ?>
                    </div>
                    <div class="book-info">
                        <h3 class="book-title"><?php echo htmlspecialchars($rec['title']); ?></h3>
                        <p class="book-author">by <?php echo htmlspecialchars($rec['author']); ?></p>
                        
                        <?php if ($rec['genre_name']): ?>
                        <div class="mb-2">
                            <span style="background: <?php echo $rec['genre_color']; ?>; color: white; padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.8rem;">
                                <?php echo htmlspecialchars($rec['genre_name']); ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="book-rating">
                            <div class="stars">
                                <?php echo generateStars($rec['average_rating']); ?>
                            </div>
                            <span class="rating-text"><?php echo formatRating($rec['average_rating']); ?> (<?php echo $rec['total_reviews']; ?>)</span>
                        </div>
                        
                        <div class="mb-2">
                            <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.5rem;">
                                <strong>Why recommended:</strong><br>
                                <?php echo htmlspecialchars($rec['recommendation_reason']); ?>
                            </div>
                            <div style="font-size: 0.8rem; color: var(--primary-color); font-weight: 500;">
                                <?php echo round($rec['confidence_score'] * 100); ?>% match • <?php echo ucfirst(str_replace('_', ' ', $rec['algorithm_used'])); ?>
                            </div>
                        </div>
                        
                        <div class="book-actions">
                            <a href="book-details.php?id=<?php echo $rec['recommended_book_id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <button class="btn btn-outline btn-sm" onclick="addToList(<?php echo $rec['recommended_book_id']; ?>, 'want_to_read')">
                                <i class="fas fa-plus"></i> Add to List
                            </button>
                            <button class="btn btn-outline btn-sm" onclick="addToList(<?php echo $rec['recommended_book_id']; ?>, 'favorites')">
                                <i class="fas fa-heart"></i> Favorite
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
