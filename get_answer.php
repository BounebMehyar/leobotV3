<?php
// Check if 'question' key exists in POST data
if (!isset($_POST['question'])) {
    die(json_encode(['error' => "Error: 'question' key not found in POST data."]));
}

$selectedQuestion = $_POST['question'];

// Prepare data for the POST request to the Flask API
$url = 'http://localhost:5000/predict';
$data = array('question' => $selectedQuestion);
$options = array(
    'http' => array(
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
    )
);

// Create context for the POST request
$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

// Check if the POST request was successful
if ($result === FALSE) {
    die(json_encode(['error' => 'Error: Unable to contact the Flask API.']));
}

// Decode the JSON response from the Flask API
$responseData = json_decode($result, true);

// Check if the JSON response is valid and contains the 'department' key
if ($responseData === NULL || !isset($responseData['department'])) {
    die(json_encode(['error' => 'Error: Invalid JSON response from the Flask API.']));
}

$department = $responseData['department'];

// Load the corresponding JSON file for the department
$jsonFilePath = "departments/$department.json";
if (!file_exists($jsonFilePath)) {
    die(json_encode(['error' => 'Error: Department JSON file not found.']));
}

$jsonData = file_get_contents($jsonFilePath);
$data = json_decode($jsonData, true);

// Initialize response variable
$response = null;

// Search for the selected question in the department's JSON data
foreach ($data as $item) {
    if ($item['question'] === $selectedQuestion) {
        $response = $item['reponse'];
        break;
    }
}

// Print the response and department in JSON format
echo json_encode([
    'response' => $response ?? "لم يتم العثور على إجابة. يرجى اختيار إحدى الاقتراحات أو العودة لاحقًا. شكرًا",
    'department' => $department
]);

