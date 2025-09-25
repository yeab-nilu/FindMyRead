<?php
require_once '../config/database.php';
requireLogin();

header('Content-Type: application/json');

$bookId = (int)($_GET['book_id'] ?? 0);
$fromList = sanitizeInput($_GET['from_list'] ?? '');
$toList = sanitizeInput($_GET['to_list'] ?? '');

$validListTypes = ['want_to_read', 'currently_reading', 'read', 'favorites'];

if (!$bookId || !in_array($fromList, $validListTypes) || !in_array($toList, $validListTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

try {
    // Remove from current list
    $removeFromCurrent = $pdo->prepare("
        DELETE FROM user_reading_lists 
        WHERE user_id = ? AND book_id = ? AND list_type = ?
    ");
    $removeFromCurrent->execute([$userId, $bookId, $fromList]);
    
    // Add to new list
    $addToNew = $pdo->prepare("
        INSERT INTO user_reading_lists (user_id, book_id, list_type) 
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE list_type = VALUES(list_type)
    ");
    $addToNew->execute([$userId, $bookId, $toList]);
    
    // Log interaction
    $logInteraction = $pdo->prepare("
        INSERT INTO user_interactions (user_id, book_id, interaction_type, metadata) 
        VALUES (?, ?, 'move_to_list', ?)
    ");
    $logInteraction->execute([$userId, $bookId, json_encode(['from_list' => $fromList, 'to_list' => $toList])]);
    
    $listTypeLabels = [
        'want_to_read' => 'Want to Read',
        'currently_reading' => 'Currently Reading',
        'read' => 'Read',
        'favorites' => 'Favorites'
    ];
    
    echo json_encode([
        'success' => true, 
        'message' => "Book moved to {$listTypeLabels[$toList]} list"
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
