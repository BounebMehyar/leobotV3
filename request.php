<?php
session_start();

// Function to load JSON data
function loadJson($filename) {
    if (!file_exists($filename)) {
        return [];
    }
    $jsonData = file_get_contents($filename);
    return json_decode($jsonData, true);
}

// Function to save JSON data
function saveJson($filename, $data) {
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Function to append data to CSV
function appendToCsv($filename, $data) {
    $file = fopen($filename, 'a');
    fputcsv($file, $data);
    fclose($file);
}

// Verify if the user has passed the verification step
if (!isset($_SESSION['verification_code']) || !isset($_SESSION['verified_email'])) {
    header('Location: verification.php');
    exit;
}

// Retrieve the verified email and department from the codes.json
$verifiedEmail = $_SESSION['verified_email'];
$verificationCode = $_SESSION['verification_code'];
$departmentName = null;

// Load the codes data and find the verified email and department
$codesData = loadJson('codes.json');
$entryFound = false;

foreach ($codesData as $entry) {
    if ($entry['code'] == $verificationCode && $entry['email'] == $verifiedEmail) {
        if (isset($entry['department'])) {
            $departmentName = $entry['department'];
        } elseif (isset($entry['departement'])) {
            $departmentName = $entry['departement'];
        }
        $entryFound = true;
        break;
    }
}

if (!$entryFound || !$departmentName) {
    echo "Verification failed or department not found. Debug Info: Email: $verifiedEmail, Department: $departmentName";
    exit;
}

// Load questions and filter them by department
$questions = loadJson('newquestion.json');
$departmentQuestions = array_filter($questions, function($question) use ($departmentName) {
    return isset($question['departement']) && $question['departement'] === $departmentName;
});

// Append questions and departments to CSV
$csvFile = 'questions_departements.csv';

// Check if the CSV file is empty to add the header
if (!file_exists($csvFile) || filesize($csvFile) == 0) {
    appendToCsv($csvFile, ['Question', 'Department']);
}

foreach ($departmentQuestions as $question) {
    appendToCsv($csvFile, [$question['question'], $departmentName]);
}

// Function to handle the answer submission
function handleAnswerSubmission($questions, $departmentName) {
    $questionText = $_POST['question_text'];
    $answer = $_POST['answer'];

    // Update the answer in the data array and remove the answered question
    foreach ($questions as $key => $question) {
        if ($question['question'] === $questionText) {
            $answeredQuestion = [
                'question' => $question['question'],
                'reponse' => $answer
            ];

            // Save to department-specific JSON file
            $departmentFile = 'departments/' . $departmentName . '.json';
            $departmentData = loadJson($departmentFile);
            $departmentData[] = $answeredQuestion;
            saveJson($departmentFile, $departmentData);

            // Remove the answered question from the questions array
            unset($questions[$key]);
            break;
        }
    }

    // Save the updated questions back to newquestion.json
    saveJson('newquestion.json', array_values($questions));

    // Reload the page to refresh the questions list
    header("Refresh:0");
    exit;
}

// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'], $_POST['question_text'])) {
    handleAnswerSubmission($questions, $departmentName);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Interface du Responsable de Département</title>
    <link rel="stylesheet" type="text/css" href="request.css">
    <style>
        body {
            background-image: url('image/background2.jpg');
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
        }
    </style>
</head>
<body>
    <h1>Répondre aux Questions</h1>
    <div class="questions-container">
        <?php foreach ($departmentQuestions as $question) : ?>
            <div class="question-box">
                <p class="question-text"><?php echo htmlspecialchars($question['question']); ?></p>
                <p class="employee-details">
                    Demandé par: <?php echo htmlspecialchars($question['username']); ?>
                </p>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <textarea name="answer" placeholder="Écrivez votre réponse ici..."></textarea>
                    <input type="hidden" name="question_text" value="<?php echo htmlspecialchars($question['question']); ?>">
                    <input type="submit" value="Envoyer la Réponse">
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
