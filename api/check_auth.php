<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo json_encode([
                'authenticated' => true,
                'user' => $user
            ]);
        } else {
            session_destroy();
            echo json_encode(['authenticated' => false]);
        }
    } catch (PDOException $e) {
        echo json_encode(['authenticated' => false]);
    }
} else {
    echo json_encode(['authenticated' => false]);
}
?>
