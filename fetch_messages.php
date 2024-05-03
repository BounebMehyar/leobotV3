<?php
require 'db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['conversation_id'])) {
    $conversationId = $_POST['conversation_id'];

    $stmt = $pdo->prepare("SELECT content, timestamp, role FROM messages WHERE conversation = :conversation_id ORDER BY timestamp ASC");
    $stmt->execute([':conversation_id' => $conversationId]);

    $messages = $stmt->fetchAll();

    // Prepare HTML output to send back to the client
    $output = '';
    foreach ($messages as $message) {
        $roleClass = htmlspecialchars($message['role']); // "sent" or "received"
        $output .= '<div class="message-box ' . $roleClass . '">';
        $output .= '<p>' . htmlspecialchars($message['content']) . '</p>';
        $output .= '<span class="timestamp">' . $message['timestamp'] . '</span>';
        $output .= '</div>';
    }

    echo $output;
    exit; 
}
?>
