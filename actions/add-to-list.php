<?php
require_once '../config/database.php';
requireLogin();

header('Content-Type: application/json');

$bookId = (int)($_GET['book_id'] ?? 0);
$listType = sanitizeInput($_GET['list_type'] ?? '');

$validListTypes = ['want_to_read', 'currently_reading', 'read', 'favorites'];

if (!$bookId || !in_array($listType, $validListTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

try {
    // Check if book exists
    $bookCheck = $pdo->prepare("SELECT id FROM books WHERE id = ?");
    $bookCheck->execute([$bookId]);
    if (!$bookCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Book not found']);
        exit;
    }
    
    // Check if already in this list
    $existingCheck = $pdo->prepare("
        SELECT id FROM user_reading_lists 
        WHERE user_id = ? AND book_id = ? AND list_type = ?
    ");
    $existingCheck->execute([$userId, $bookId, $listType]);
    
    if ($existingCheck->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Book already in this list']);
        exit;
    }
    
    // Remove from other lists first
    $removeFromOthers = $pdo->prepare("
        DELETE FROM user_reading_lists 
        WHERE user_id = ? AND book_id = ? AND list_type != ?
    ");
    $removeFromOthers->execute([$userId, $bookId, $listType]);
    
    // Add to the specified list
    $addToList = $pdo->prepare("
        INSERT INTO user_reading_lists (user_id, book_id, list_type) 
        VALUES (?, ?, ?)
    ");
    $addToList->execute([$userId, $bookId, $listType]);
    
    // Log interaction
    $logInteraction = $pdo->prepare("
        INSERT INTO user_interactions (user_id, book_id, interaction_type, metadata) 
        VALUES (?, ?, 'add_to_list', ?)
    ");
    $logInteraction->execute([$userId, $bookId, json_encode(['list_type' => $listType])]);
    
    // Update reading analytics
    updateReadingAnalytics($pdo, $userId, $listType);

    $listTypeLabels = [
        'want_to_read' => 'Want to Read',
        'currently_reading' => 'Currently Reading',
        'read' => 'Read',
        'favorites' => 'Favorites'
    ];
    
    echo json_encode([
        'success' => true, 
        'message' => "Book added to {$listTypeLabels[$listType]} list"
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

<?php
// Helper to update user's reading analytics when list changes
function updateReadingAnalytics(PDO $pdo, int $userId, string $listType): void {
    // Ensure analytics row exists
    $ensure = $pdo->prepare("INSERT INTO reading_analytics (user_id) VALUES (?) ON DUPLICATE KEY UPDATE user_id = user_id");
    $ensure->execute([$userId]);

    // Recompute aggregates
    $countRead = $pdo->prepare("SELECT COUNT(*) AS c FROM user_reading_lists WHERE user_id = ? AND list_type = 'read'");
    $countRead->execute([$userId]);
    $booksReadCount = (int)$countRead->fetch()['c'];

    $currentMonth = date('Y-m');
    $monthStmt = $pdo->prepare("SELECT COUNT(*) AS c FROM user_reading_lists WHERE user_id = ? AND list_type = 'read' AND DATE_FORMAT(added_at, '%Y-%m') = ?");
    $monthStmt->execute([$userId, $currentMonth]);
    $currentMonthRead = (int)$monthStmt->fetch()['c'];

    $currentYear = date('Y');
    $yearStmt = $pdo->prepare("SELECT COUNT(*) AS c FROM user_reading_lists WHERE user_id = ? AND list_type = 'read' AND YEAR(added_at) = ?");
    $yearStmt->execute([$userId, $currentYear]);
    $currentYearRead = (int)$yearStmt->fetch()['c'];

    // Handle streaks only when marking as read
    $streak = 0;
    $lastDate = null;
    $getExisting = $pdo->prepare("SELECT reading_streak, last_reading_date FROM reading_analytics WHERE user_id = ?");
    $getExisting->execute([$userId]);
    if ($row = $getExisting->fetch()) {
        $streak = (int)$row['reading_streak'];
        $lastDate = $row['last_reading_date'];
    }

    if ($listType === 'read') {
        $today = new DateTimeImmutable('today');
        $yesterday = $today->sub(new DateInterval('P1D'));
        if ($lastDate) {
            try {
                $last = new DateTimeImmutable($lastDate);
                if ($last->format('Y-m-d') === $today->format('Y-m-d')) {
                    // already counted today, keep streak
                } elseif ($last->format('Y-m-d') === $yesterday->format('Y-m-d')) {
                    $streak += 1;
                } else {
                    $streak = 1;
                }
            } catch (Exception $e) {
                $streak = max(1, $streak);
            }
        } else {
            $streak = max(1, $streak);
        }
        $lastDateToSet = $today->format('Y-m-d');
    } else {
        $lastDateToSet = $lastDate; // unchanged
    }

    $update = $pdo->prepare("UPDATE reading_analytics SET books_read_count = ?, current_month_read = ?, current_year_read = ?, reading_streak = ?, last_reading_date = ? WHERE user_id = ?");
    $update->execute([$booksReadCount, $currentMonthRead, $currentYearRead, $streak, $lastDateToSet, $userId]);
}
?>
