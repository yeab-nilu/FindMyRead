<?php
require_once '../config/database.php';
requireLogin();

header('Content-Type: application/json');

// Support JSON body and form-encoded
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$bookId = (int)($data['book_id'] ?? ($_POST['book_id'] ?? 0));
$rating = (int)($data['rating'] ?? ($_POST['rating'] ?? 0));

if ($bookId <= 0 || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

try {
    // Ensure book exists
    $bookCheck = $pdo->prepare('SELECT id FROM books WHERE id = ?');
    $bookCheck->execute([$bookId]);
    if (!$bookCheck->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Book not found']);
        exit;
    }

    // Upsert review
    $pdo->beginTransaction();

    $existing = $pdo->prepare('SELECT id FROM reviews WHERE user_id = ? AND book_id = ?');
    $existing->execute([$userId, $bookId]);
    if ($row = $existing->fetch()) {
        $update = $pdo->prepare('UPDATE reviews SET rating = ?, updated_at = NOW() WHERE id = ?');
        $update->execute([$rating, $row['id']]);
    } else {
        $insert = $pdo->prepare('INSERT INTO reviews (user_id, book_id, rating) VALUES (?, ?, ?)');
        $insert->execute([$userId, $bookId, $rating]);
    }

    // Recompute aggregate rating
    $agg = $pdo->prepare('SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM reviews WHERE book_id = ?');
    $agg->execute([$bookId]);
    $stats = $agg->fetch();

    $updateBook = $pdo->prepare('UPDATE books SET average_rating = ?, total_reviews = ? WHERE id = ?');
    $updateBook->execute([number_format((float)$stats['avg_rating'], 2, '.', ''), (int)$stats['total'], $bookId]);

    // Log interaction
    $log = $pdo->prepare("INSERT INTO user_interactions (user_id, book_id, interaction_type, metadata) VALUES (?, ?, 'rate', ?)");
    $log->execute([$userId, $bookId, json_encode(['rating' => $rating])]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Rating saved']);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>


