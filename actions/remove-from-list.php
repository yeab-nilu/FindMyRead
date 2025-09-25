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
    // Remove from the specified list
    $removeFromList = $pdo->prepare("
        DELETE FROM user_reading_lists 
        WHERE user_id = ? AND book_id = ? AND list_type = ?
    ");
    $removeFromList->execute([$userId, $bookId, $listType]);
    
    if ($removeFromList->rowCount() > 0) {
        // Log interaction
        $logInteraction = $pdo->prepare("
            INSERT INTO user_interactions (user_id, book_id, interaction_type, metadata) 
            VALUES (?, ?, 'remove_from_list', ?)
        ");
        $logInteraction->execute([$userId, $bookId, json_encode(['list_type' => $listType])]);
        
        $listTypeLabels = [
            'want_to_read' => 'Want to Read',
            'currently_reading' => 'Currently Reading',
            'read' => 'Read',
            'favorites' => 'Favorites'
        ];
        
        echo json_encode([
            'success' => true, 
            'message' => "Book removed from {$listTypeLabels[$listType]} list"
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Book not found in this list']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
