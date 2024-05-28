<?php
$data = file_get_contents('newquestion.json');
$jsonArr = json_decode($data, true) ?: [];

$newQuestion = [
    'userId' => $_POST['userId'],
    'username' => $_POST['username'],
    'question' => $_POST['question'],
    'conversationId' => $_POST['currentConversationId'],
    'departement' => $_POST['departement']
];

// Append new question
$jsonArr[] = $newQuestion;

// Save back to the file
file_put_contents('newquestion.json', json_encode($jsonArr, JSON_PRETTY_PRINT));

echo json_encode(['success' => true]);
