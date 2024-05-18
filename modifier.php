<?php
// Vérifier si le paramètre de la question est présent dans l'URL
if (isset($_GET['question'])) {
    // Échapper les données pour prévenir les failles XSS
    $questionToEdit = htmlspecialchars($_GET['question']);

    // Charger les données JSON existantes
    $data = file_get_contents('data.json');
    $questionsReponses = json_decode($data, true);

    // Rechercher la question spécifique dans le tableau
    $key = array_search($questionToEdit, array_column($questionsReponses, 'question'));

    // Vérifier si la question existe dans les données
    if ($key !== false) {
        // La question a été trouvée, récupérer la question et la réponse
        $question = $questionsReponses[$key]['question'];
        $reponse = $questionsReponses[$key]['reponse'];
    } else {
        // Rediriger si la question n'est pas trouvée
        header("Location: index.php");
        exit;
    }
} else {
    // Rediriger si le paramètre de la question est manquant
    header("Location: index.php");
    exit;
}

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer la nouvelle question et réponse et les échapper pour prévenir les failles XSS
    $nouvelleQuestion = htmlspecialchars($_POST['nouvelle_question']);
    $nouvelleReponse = htmlspecialchars($_POST['nouvelle_reponse']);

    // Mettre à jour la question et la réponse dans les données
    $questionsReponses[$key]['question'] = $nouvelleQuestion;
    $questionsReponses[$key]['reponse'] = $nouvelleReponse;

    // Enregistrer les données mises à jour dans le fichier JSON
    file_put_contents('data.json', json_encode($questionsReponses, JSON_PRETTY_PRINT));

    // Rediriger vers la page principale après la modification
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la question et la réponse</title>
    <link rel="stylesheet" type="text/css" href="style.css"> 
</head>
<body>
    <h2>Modifier la question et la réponse :</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?question=' . urlencode($questionToEdit); ?>">
        <label for="nouvelle_question">Nouvelle question :</label><br>
        <input type="text" id="nouvelle_question" name="nouvelle_question" value="<?php echo htmlspecialchars($question); ?>"><br>
        <label for="nouvelle_reponse">Nouvelle réponse :</label><br>
        <input type="text" id="nouvelle_reponse" name="nouvelle_reponse" value="<?php echo htmlspecialchars($reponse); ?>"><br><br>
        <input type="submit" value="Enregistrer">
    </form>
</body>
</html>
