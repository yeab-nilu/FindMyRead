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
