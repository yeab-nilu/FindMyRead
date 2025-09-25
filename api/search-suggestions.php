<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$q = sanitizeInput($_GET['q'] ?? '');
if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$pdo = getDBConnection();
$term = "%$q%";

$stmt = $pdo->prepare('
    SELECT id, title, author
    FROM books
    WHERE title LIKE ? OR author LIKE ?
    ORDER BY average_rating DESC, total_reviews DESC
    LIMIT 10
');
$stmt->execute([$term, $term]);
$results = $stmt->fetchAll();

echo json_encode($results);
?>


