<?php
require_once 'config/database.php';
requireLogin();

$user = getCurrentUser();
$pageTitle = 'Profile';
include 'includes/header.php';
?>

<div class="section">
    <div class="container">
        <div class="grid grid-2">
            <div class="card">
                <div class="card-header">
                    <h3>Your Profile</h3>
                </div>
                <div class="card-body">
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <?php if (!empty($user['first_name']) || !empty($user['last_name'])): ?>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))); ?></p>
                    <?php endif; ?>
                    <p><strong>Joined:</strong> <?php echo formatDate($user['created_at']); ?></p>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3>Reading Preferences</h3>
                </div>
                <div class="card-body">
                    <?php $prefs = json_decode($user['reading_preferences'] ?? '{}', true); ?>
                    <p><strong>Favorite Genres:</strong> <?php echo !empty($prefs['favorite_genres']) ? htmlspecialchars(implode(', ', $prefs['favorite_genres'])) : '—'; ?></p>
                    <p><strong>Preferred Language:</strong> <?php echo htmlspecialchars($prefs['preferred_language'] ?? '—'); ?></p>
                    <p><strong>Reading Goal:</strong> <?php echo htmlspecialchars((string)($prefs['reading_goal'] ?? '—')); ?> books/year</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>



