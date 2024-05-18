<?php
session_start();

// Check if the user has passed the verification step
if (!isset($_SESSION['verification_code'])) {
    header('Location: verification.php');
    exit;
}

// Function to load questions
function loadQuestions() {
    $jsonData = file_get_contents('newquestion.json');
    return json_decode($jsonData, true);
}

// Function to save answers back to JSON
function saveAnswers($data) {
    file_put_contents('newquestion.json', json_encode($data, JSON_PRETTY_PRINT));
}

// Check if form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'], $_POST['question_id'])) {
    $questions = loadQuestions();
    $questionId = $_POST['question_id'];
    $answer = $_POST['answer'];

    // Update the answer in the data array
    foreach ($questions as $key => &$question) {
        if ($question['conversationId'] === $questionId) {
            $question['answer'] = $answer; // Store the answer
            break;
        }
    }

    // Save the updated data
    saveAnswers($questions);

    echo "<p>Réponse enregistrée!</p>";
}

$questions = loadQuestions();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Interface du Responsable de Département</title>
    <link rel="stylesheet" type="text/css" href="request.css">
</head>
<body>
    <h1>Répondre aux Questions</h1>
    <div class="questions-container">
        <?php foreach ($questions as $question) : ?>
            <div class="question-box">
                <p class="question-text"><?php echo htmlspecialchars($question['question']); ?></p>
                <p class="employee-details">
                    Demandé par: <?php echo htmlspecialchars($question['username']); ?>
                </p>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <textarea name="answer" placeholder="Écrivez votre réponse ici..."></textarea>
                    <input type="hidden" name="question_id" value="<?php echo $question['conversationId']; ?>">
                    <input type="submit" value="Envoyer la Réponse">
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
