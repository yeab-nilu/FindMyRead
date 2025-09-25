<?php
require_once 'config/database.php';

$pdo = getDBConnection();

// Get all genres with book counts
$genres = $pdo->query("
    SELECT g.*, COUNT(b.id) as book_count 
    FROM genres g 
    LEFT JOIN books b ON g.id = b.genre_id 
    GROUP BY g.id, g.name, g.color, g.description, g.created_at, g.updated_at
    ORDER BY book_count DESC, g.name ASC
")->fetchAll();

$pageTitle = 'Genres';
include 'includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="mb-4">
            <h1>Browse by Genre</h1>
            <p>Discover books organized by category</p>
        </div>

        <div class="grid grid-3">
            <?php foreach ($genres as $genre): ?>
            <a href="books.php?genre=<?php echo $genre['id']; ?>" class="card text-center" style="text-decoration: none; color: inherit;">
                <div class="card-body">
                    <div style="width: 80px; height: 80px; background: <?php echo $genre['color']; ?>; border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($genre['name']); ?></h3>
                    <p class="text-secondary mb-2"><?php echo $genre['book_count']; ?> books</p>
                    <?php if ($genre['description']): ?>
                    <p style="font-size: 0.9rem; color: var(--text-light);"><?php echo htmlspecialchars(substr($genre['description'], 0, 100)); ?><?php echo strlen($genre['description']) > 100 ? '...' : ''; ?></p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
