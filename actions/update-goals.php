<?php
require_once '../config/database.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $monthlyGoal = (int)($_POST['monthly_goal'] ?? 12);
    $yearlyGoal = (int)($_POST['yearly_goal'] ?? 50);
    
    if ($monthlyGoal < 1 || $monthlyGoal > 100 || $yearlyGoal < 1 || $yearlyGoal > 500) {
        $_SESSION['error'] = 'Invalid goal values. Monthly goal: 1-100, Yearly goal: 1-500';
        redirect('analytics.php');
    }
    
    $pdo = getDBConnection();
    $userId = $_SESSION['user_id'];
    
    try {
        $updateGoals = $pdo->prepare("
            UPDATE reading_analytics 
            SET monthly_reading_goal = ?, yearly_reading_goal = ?
            WHERE user_id = ?
        ");
        $updateGoals->execute([$monthlyGoal, $yearlyGoal, $userId]);
        
        $_SESSION['success'] = 'Reading goals updated successfully!';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Failed to update goals: ' . $e->getMessage();
    }
}

redirect('analytics.php');
?>
