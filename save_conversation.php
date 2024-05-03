<?php
$userId = isset($_POST['userId']) ? $_POST['userId'] : 'unknown_user';
$conversationId = isset($_POST['currentConversationId']) ? $_POST['currentConversationId'] : null;

$question = $_POST['question'];
$response = $_POST['response'];

$directory = 'conversations';
if (!file_exists($directory)) {
    mkdir($directory, 0777, true);
}

// Each conversation will be saved in a separate file
if ($conversationId === null) {
    $conversationId = uniqid('conv_', true); // Generate a new ID if it's the first message of the conversation
    $conversationData = [
        'conversationId' => $conversationId,
        'userId' => $userId,
        'interactions' => [
            ['question' => $question, 'response' => $response]
        ],
        'timestamp' => time()
    ];
} else {
    $jsonFilePath = $directory . '/' . $conversationId . '.json';
    if (file_exists($jsonFilePath)) {
        $conversationData = json_decode(file_get_contents($jsonFilePath), true);
        $conversationData['interactions'][] = ['question' => $question, 'response' => $response];
    } else {
        // In case the file should exist but doesn't
        $conversationData = [
            'conversationId' => $conversationId,
            'userId' => $userId,
            'interactions' => [
                ['question' => $question, 'response' => $response]
            ],
            'timestamp' => time()
        ];
    }
}

// Write the data to the specific conversation file
$jsonFilePath = $directory . '/' . $conversationId . '.json';
file_put_contents($jsonFilePath, json_encode($conversationData, JSON_PRETTY_PRINT));

echo json_encode(['status' => 'success', 'message' => 'Conversation saved successfully', 'conversationId' => $conversationId]);

