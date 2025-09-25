<?php
require_once 'config/database.php';
requireLogin();

$user = getCurrentUser();
$pdo = getDBConnection();

// Get user's reading analytics
$analytics = $pdo->prepare("SELECT * FROM reading_analytics WHERE user_id = ?");
$analytics->execute([$user['id']]);
$userAnalytics = $analytics->fetch();

// Get reading history by month
$monthlyData = $pdo->prepare("
    SELECT 
        DATE_FORMAT(added_at, '%Y-%m') as month,
        COUNT(*) as books_read
    FROM user_reading_lists 
    WHERE user_id = ? AND list_type = 'read'
    AND added_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(added_at, '%Y-%m')
    ORDER BY month ASC
");
$monthlyData->execute([$user['id']]);
$monthlyStats = $monthlyData->fetchAll();

// Get genre preferences
$genreStats = $pdo->prepare("
    SELECT g.name, g.color, COUNT(*) as book_count
    FROM user_reading_lists url
    JOIN books b ON url.book_id = b.id
    JOIN genres g ON b.genre_id = g.id
    WHERE url.user_id = ? AND url.list_type = 'read'
    GROUP BY g.id, g.name, g.color
    ORDER BY book_count DESC
    LIMIT 10
");
$genreStats->execute([$user['id']]);
$genreData = $genreStats->fetchAll();

// Get reading streak
$streakData = $pdo->prepare("
    SELECT 
        COUNT(DISTINCT DATE(added_at)) as current_streak,
        MAX(added_at) as last_reading_date
    FROM user_reading_lists 
    WHERE user_id = ? AND list_type = 'read'
    AND added_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");
$streakData->execute([$user['id']]);
$streakInfo = $streakData->fetch();

// Get recent activity
$recentActivity = $pdo->prepare("
    SELECT url.*, b.title, b.author, g.name as genre_name
    FROM user_reading_lists url
    JOIN books b ON url.book_id = b.id
    LEFT JOIN genres g ON b.genre_id = g.id
    WHERE url.user_id = ?
    ORDER BY url.added_at DESC
    LIMIT 10
");
$recentActivity->execute([$user['id']]);
$activityData = $recentActivity->fetchAll();

$pageTitle = 'Reading Analytics';
include 'includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="mb-4">
            <h1>Reading Analytics</h1>
            <p>Track your reading progress and discover your reading patterns</p>
        </div>

        <!-- Overview Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $userAnalytics['books_read_count'] ?? 0; ?></div>
                <div class="stat-label">Total Books Read</div>
                <div class="stat-change positive">
                    +<?php echo $userAnalytics['current_month_read'] ?? 0; ?> this month
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $userAnalytics['reading_streak'] ?? 0; ?></div>
                <div class="stat-label">Day Streak</div>
                <div class="stat-change positive">
                    Keep it up!
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $userAnalytics['monthly_reading_goal'] ?? 12; ?></div>
                <div class="stat-label">Monthly Goal</div>
                <div class="stat-change">
                    <?php echo round((($userAnalytics['current_month_read'] ?? 0) / ($userAnalytics['monthly_reading_goal'] ?? 12)) * 100); ?>% complete
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $userAnalytics['yearly_reading_goal'] ?? 50; ?></div>
                <div class="stat-label">Yearly Goal</div>
                <div class="stat-change">
                    <?php echo round((($userAnalytics['current_year_read'] ?? 0) / ($userAnalytics['yearly_reading_goal'] ?? 50)) * 100); ?>% complete
                </div>
            </div>
        </div>

        <div class="grid grid-2">
            <!-- Monthly Reading Progress -->
            <div class="card">
                <div class="card-header">
                    <h3>Monthly Reading Progress</h3>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span>This Month: <?php echo $userAnalytics['current_month_read'] ?? 0; ?> / <?php echo $userAnalytics['monthly_reading_goal'] ?? 12; ?> books</span>
                            <span><?php echo round((($userAnalytics['current_month_read'] ?? 0) / ($userAnalytics['monthly_reading_goal'] ?? 12)) * 100); ?>%</span>
                        </div>
                        <div style="width: 100%; height: 8px; background: var(--bg-tertiary); border-radius: 4px; overflow: hidden;">
                            <div style="width: <?php echo round((($userAnalytics['current_month_read'] ?? 0) / ($userAnalytics['monthly_reading_goal'] ?? 12)) * 100); ?>%; height: 100%; background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)); transition: width 0.3s ease;"></div>
                        </div>
                    </div>
                    
                    <?php if (!empty($monthlyStats)): ?>
                    <h4>Last 12 Months</h4>
                    <div class="space-y-2">
                        <?php foreach ($monthlyStats as $month): ?>
                        <div class="flex justify-between items-center">
                            <span><?php echo date('M Y', strtotime($month['month'] . '-01')); ?></span>
                            <div class="flex items-center gap-2">
                                <div style="width: 100px; height: 4px; background: var(--bg-tertiary); border-radius: 2px;">
                                    <div style="width: <?php echo min(100, ($month['books_read'] / 10) * 100); ?>%; height: 100%; background: var(--primary-color); border-radius: 2px;"></div>
                                </div>
                                <span style="font-size: 0.8rem; color: var(--text-secondary);"><?php echo $month['books_read']; ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Genre Preferences -->
            <div class="card">
                <div class="card-header">
                    <h3>Favorite Genres</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($genreData)): ?>
                        <p class="text-center text-secondary">No reading data yet. Start reading to see your genre preferences!</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach ($genreData as $genre): ?>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <div style="width: 12px; height: 12px; background: <?php echo $genre['color']; ?>; border-radius: 50%;"></div>
                                    <span><?php echo htmlspecialchars($genre['name']); ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div style="width: 60px; height: 4px; background: var(--bg-tertiary); border-radius: 2px;">
                                        <div style="width: <?php echo min(100, ($genre['book_count'] / max(array_column($genreData, 'book_count'))) * 100); ?>%; height: 100%; background: <?php echo $genre['color']; ?>; border-radius: 2px;"></div>
                                    </div>
                                    <span style="font-size: 0.8rem; color: var(--text-secondary);"><?php echo $genre['book_count']; ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card mt-4">
            <div class="card-header">
                <h3>Recent Activity</h3>
            </div>
            <div class="card-body">
                <?php if (empty($activityData)): ?>
                    <p class="text-center text-secondary">No recent activity. Start adding books to your lists!</p>
                <?php else: ?>
                    <div class="grid grid-1">
                        <?php foreach ($activityData as $activity): ?>
                        <div class="flex items-center gap-3 p-3" style="border: 1px solid var(--border-light); border-radius: var(--radius-md); margin-bottom: 0.5rem;">
                            <div style="width: 40px; height: 50px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="flex-1">
                                <h4 style="font-size: 0.9rem; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($activity['title']); ?></h4>
                                <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.25rem;">by <?php echo htmlspecialchars($activity['author']); ?></p>
                                <div class="flex items-center gap-2">
                                    <span style="font-size: 0.8rem; padding: 0.25rem 0.5rem; background: var(--bg-secondary); border-radius: var(--radius-sm); color: var(--text-secondary);">
                                        <?php echo ucfirst(str_replace('_', ' ', $activity['list_type'])); ?>
                                    </span>
                                    <span style="font-size: 0.8rem; color: var(--text-light);">
                                        <?php echo formatDate($activity['added_at']); ?>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <a href="book-details.php?id=<?php echo $activity['book_id']; ?>" class="btn btn-sm btn-outline">View</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Reading Goals Management -->
        <div class="card mt-4">
            <div class="card-header">
                <h3>Reading Goals</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="actions/update-goals.php">
                    <div class="grid grid-2 gap-4">
                        <div class="form-group">
                            <label for="monthly_goal" class="form-label">Monthly Reading Goal</label>
                            <input type="number" id="monthly_goal" name="monthly_goal" class="form-input" 
                                   value="<?php echo $userAnalytics['monthly_reading_goal'] ?? 12; ?>" min="1" max="100">
                        </div>
                        
                        <div class="form-group">
                            <label for="yearly_goal" class="form-label">Yearly Reading Goal</label>
                            <input type="number" id="yearly_goal" name="yearly_goal" class="form-input" 
                                   value="<?php echo $userAnalytics['yearly_reading_goal'] ?? 50; ?>" min="1" max="500">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Goals
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
