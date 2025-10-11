<?php
require_once 'config/database.php';
requireLogin();

$user = getCurrentUser();
$pdo = getDBConnection();

// Get user's reading statistics
$stats = $pdo->prepare("
    SELECT * FROM reading_analytics WHERE user_id = ?
");
$stats->execute([$user['id']]);
$userStats = $stats->fetch();

// Get user's reading lists counts
$listCounts = $pdo->prepare("
    SELECT list_type, COUNT(*) as count 
    FROM user_reading_lists 
    WHERE user_id = ? 
    GROUP BY list_type
");
$listCounts->execute([$user['id']]);
$listCountsData = $listCounts->fetchAll(PDO::FETCH_KEY_PAIR);

// Get recent recommendations
$recommendations = $pdo->prepare("
    SELECT br.*, b.title, b.author, b.average_rating, b.cover_image_url, g.name as genre_name
    FROM book_recommendations br
    JOIN books b ON br.recommended_book_id = b.id
    LEFT JOIN genres g ON b.genre_id = g.id
    WHERE br.user_id = ? AND br.is_viewed = 0
    ORDER BY br.confidence_score DESC
    LIMIT 6
");
$recommendations->execute([$user['id']]);
$userRecommendations = $recommendations->fetchAll();

// Get recently added books to reading lists
$recentBooks = $pdo->prepare("
    SELECT url.*, b.title, b.author, b.average_rating, b.cover_image_url, g.name as genre_name
    FROM user_reading_lists url
    JOIN books b ON url.book_id = b.id
    LEFT JOIN genres g ON b.genre_id = g.id
    WHERE url.user_id = ?
    ORDER BY url.added_at DESC
    LIMIT 6
");
$recentBooks->execute([$user['id']]);
$userRecentBooks = $recentBooks->fetchAll();

// Get reading progress for current month
$currentMonth = date('Y-m');
$monthlyProgress = $pdo->prepare("
    SELECT COUNT(*) as books_read_this_month
    FROM user_reading_lists 
    WHERE user_id = ? AND list_type = 'read' 
    AND DATE_FORMAT(added_at, '%Y-%m') = ?
");
$monthlyProgress->execute([$user['id'], $currentMonth]);
$monthlyData = $monthlyProgress->fetch();

$pageTitle = 'Dashboard';
include 'includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="mb-4">
            <h1>Welcome back, <?php echo htmlspecialchars($user['first_name'] ?: $user['username']); ?>!</h1>
            <p>Here's what's happening with your reading journey.</p>
        </div>

        <!-- Stats Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $userStats['books_read_count'] ?? 0; ?></div>
                <div class="stat-label">Books Read</div>
                <div class="stat-change positive">
                    +<?php echo $monthlyData['books_read_this_month'] ?? 0; ?> this month
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $listCountsData['want_to_read'] ?? 0; ?></div>
                <div class="stat-label">Want to Read</div>
                <div class="stat-change">
                    In your list
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $listCountsData['currently_reading'] ?? 0; ?></div>
                <div class="stat-label">Currently Reading</div>
                <div class="stat-change">
                    Active books
                </div>
            </div>
        </div>

        <!-- Reading Goal Progress -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Monthly Reading Goal</h3>
            </div>
            <div class="card-body">
                <div class="flex justify-between items-center mb-2">
                    <span>Progress: <?php echo $monthlyData['books_read_this_month'] ?? 0; ?> / <?php echo $userStats['monthly_reading_goal'] ?? 12; ?> books</span>
                    <span><?php echo round((($monthlyData['books_read_this_month'] ?? 0) / ($userStats['monthly_reading_goal'] ?? 12)) * 100); ?>%</span>
                </div>
                <div style="width: 100%; height: 8px; background: var(--bg-tertiary); border-radius: 4px; overflow: hidden;">
                    <div style="width: <?php echo round((($monthlyData['books_read_this_month'] ?? 0) / ($userStats['monthly_reading_goal'] ?? 12)) * 100); ?>%; height: 100%; background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)); transition: width 0.3s ease;"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-2">
            <!-- Recent Recommendations -->
            <div class="card">
                <div class="card-header">
                    <h3>Recommended for You</h3>
                    <a href="recommendations.php" class="btn btn-sm btn-outline">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($userRecommendations)): ?>
                        <p class="text-center text-secondary">No recommendations yet. Start rating books to get personalized suggestions!</p>
                    <?php else: ?>
                        <div class="grid grid-1">
                            <?php foreach ($userRecommendations as $rec): ?>
                            <div class="flex items-center gap-3 p-2" style="border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                                <div style="width: 50px; height: 70px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 style="font-size: 0.9rem; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($rec['title']); ?></h4>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.25rem;">by <?php echo htmlspecialchars($rec['author']); ?></p>
                                    <div class="flex items-center gap-2">
                                        <div class="stars">
                                            <?php echo generateStars($rec['average_rating']); ?>
                                        </div>
                                        <span style="font-size: 0.8rem; color: var(--text-secondary);"><?php echo formatRating($rec['average_rating']); ?></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.25rem;">
                                        <?php echo round($rec['confidence_score'] * 100); ?>% match
                                    </div>
                                    <a href="book-details.php?id=<?php echo $rec['recommended_book_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h3>Recent Activity</h3>
                    <a href="reading-lists.php" class="btn btn-sm btn-outline">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($userRecentBooks)): ?>
                        <p class="text-center text-secondary">No recent activity. Start adding books to your lists!</p>
                    <?php else: ?>
                        <div class="grid grid-1">
                            <?php foreach ($userRecentBooks as $book): ?>
                            <div class="flex items-center gap-3 p-2" style="border: 1px solid var(--border-light); border-radius: var(--radius-md);">
                                <div style="width: 50px; height: 70px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 style="font-size: 0.9rem; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($book['title']); ?></h4>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.25rem;">by <?php echo htmlspecialchars($book['author']); ?></p>
                                    <div class="flex items-center gap-2">
                                        <span style="font-size: 0.8rem; padding: 0.25rem 0.5rem; background: var(--bg-secondary); border-radius: var(--radius-sm); color: var(--text-secondary);">
                                            <?php echo ucfirst(str_replace('_', ' ', $book['list_type'])); ?>
                                        </span>
                                        <span style="font-size: 0.8rem; color: var(--text-light);">
                                            <?php echo formatDate($book['added_at']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <a href="book-details.php?id=<?php echo $book['book_id']; ?>" class="btn btn-sm btn-outline">View</a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-4">
                    <a href="books.php" class="btn btn-secondary">
                        <i class="fas fa-search"></i> Browse Books
                    </a>
                    <a href="recommendations.php" class="btn btn-secondary">
                        <i class="fas fa-magic"></i> Get Recommendations
                    </a>
                    <a href="reading-lists.php" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Manage Lists
                    </a>
                    <a href="analytics.php" class="btn btn-secondary">
                        <i class="fas fa-chart-bar"></i> View Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
