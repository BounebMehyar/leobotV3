<?php
$conversationId = isset($_POST['currentConversationId']) ? $_POST['currentConversationId'] : '';
$directory = 'conversations';
$filePath = $directory . '/' . $conversationId . '.json'; // Correct usage of the forward slash

// Check if the file exists
if (file_exists($filePath)) {
    $jsonData = file_get_contents($filePath);
    $conversationData = json_decode($jsonData, true);

    // Start with the initial greeting
    $responseHtml = '<div class="message received p-2 mb-2 bg-light rounded">Hello! How can I assist you?</div>';

    if (isset($conversationData['interactions'])) {
        foreach ($conversationData['interactions'] as $interaction) {
            // For each interaction, format as HTML
            $questionHtml = '<div class="message sent p-2 mb-2 bg-primary text-white rounded">' . htmlspecialchars($interaction['question']) . '</div>';
            $responseHtml .= $questionHtml;  // Append question immediately
            $responseHtml .= '<div class="message received p-2 mb-2 bg-light rounded">' . htmlspecialchars($interaction['response']) . '</div>';  // Append response immediately after question
        }
    } else {
        $responseHtml .= '<div class="message received p-2 mb-2 bg-light rounded">No interactions found in this conversation.</div>';
    }
} else {
    $responseHtml = '<div class="message received p-2 mb-2 bg-light rounded">Conversation file not found.</div>';
}

// Output the response HTML
echo $responseHtml;

