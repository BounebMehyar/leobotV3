<?php
require 'db.php'; // Ensure you have included your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? 'New Chat'; // Set a default title if none is provided
    try {
        $stmt = $pdo->prepare("INSERT INTO conversation (title) VALUES (:title)");
        $stmt->execute([':title' => $title]);
        $conversationId = $pdo->lastInsertId(); // Get the ID of the newly created conversation

        echo json_encode(['success' => true, 'conversationId' => $conversationId]); // Return the conversation ID to the client
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
