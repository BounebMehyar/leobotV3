<?php
$userId = isset($_POST['userId']) ? $_POST['userId'] : null;
$directory = 'conversations';
$conversations = [];

if ($userId !== null && is_dir($directory)) {
    $files = scandir($directory);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
            $content = file_get_contents($directory . '/' . $file);
            $data = json_decode($content, true);
            if ($data['userId'] === $userId) {
                $conversations[] = [
                    'conversationId' => $data['conversationId'],
                    'title' => isset($data['interactions'][0]['question']) ? $data['interactions'][0]['question'] : 'No title'
                ];
            }
        }
    }
    echo json_encode($conversations);
} else {
    echo json_encode([]); // Return an empty array if no user ID or directory
}

