<?php
// Charger les données JSON existantes
$data = file_get_contents('data.json');

// Check if the data was loaded successfully
if ($data === false) {
    // Handle error, e.g., log it or display a message
    echo "Error loading JSON data.";
    $questionsReponses = [];  // Initialize as empty array to avoid further errors
} else {
    $questionsReponses = json_decode($data, true);

    // Check if decoding was successful
    if (is_null($questionsReponses)) {
        echo "Error decoding JSON data.";
        $questionsReponses = [];  // Initialize as empty array to avoid further errors
    }
}

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete'])) {
        $indexToDelete = $_POST['index'];
        if (isset($questionsReponses[$indexToDelete])) {
            unset($questionsReponses[$indexToDelete]);
            $questionsReponses = array_values($questionsReponses);
            file_put_contents('data.json', json_encode($questionsReponses, JSON_PRETTY_PRINT));
        }
    } else {
        $question = $_POST['question'];
        $reponse = $_POST['reponse'];
        $departement = $_POST['departement'];

        $i = count($questionsReponses);
        $questionsReponses[] = array('Id' => $i, 'question' => $question, 'reponse' => $reponse, 'departement' => $departement);

        file_put_contents('data.json', json_encode($questionsReponses, JSON_PRETTY_PRINT));
        $command = escapeshellcmd('python translation.py');
        $output = shell_exec($command);
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de questions et réponses</title>
    <link rel="stylesheet" type="text/css" href="styleadmin.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>
<div class="container">
        <div class="d-flex justify-content-end pt-2">
            <div class="btn-group btn-group-sm">
                <button onclick="changeLanguage('en')" class="btn btn-outline-primary">English</button>
                <button onclick="changeLanguage('fr')" class="btn btn-outline-primary">Français</button>
                <button onclick="changeLanguage('ar')" class="btn btn-outline-primary">العربية</button>
            </div>
        </div>
    <h2>Ajouter une question et une réponse :</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="departement">Département :</label><br>
            <select id="departement" name="departement">
                <option value="DC">communication</option>
                <option value="RH">Ressources Humaines(RH)</option>
                <option value="DG">Direction et Administration generale</option>
                <option value="DProd">Production</option>
                <option value="DF">finance</option>

            </select>
        <label for="question">Question :</label><br>
        <input type="text" id="question" name="question"><br>
        <label for="reponse">Réponse :</label><br>
        <input type="text" id="reponse" name="reponse"><br>
        
        <input type="submit" value="Envoyer">
    </form>
    <div id="questionsArea"></div>

    <h2>Liste des questions et réponses :</h2>
    <ul>
        <?php foreach ($questionsReponses as $index => $qr) : ?>
            <li>
                <strong><?php echo $qr['question'],"</br>"; ?></strong> - <?php echo $qr['reponse']; ?>
                <a href="modifier.php?question=<?php echo urlencode($qr['question']); ?>">Modifier</a>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:Block;">
                    <input type="hidden" name="delete" value="true">
                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                    <input type="submit" value="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?');">
                </form>
            </li>
        <?php endforeach; ?>
    </ul>


<script>
     $(document).ready(function() {
        loadQuestions(); // Load questions on page load

        $('#departement').change(function() {
            loadQuestions(); // Load questions when the department changes
        });
    });
    function loadQuestions() {
        const department = $('#departement').val();
        $.ajax({
            url: 'fetch_questionsAdmin.php',
            type: 'GET',
            data: { department: department },
            success: function(data) {
                $('#questionsArea').html(data);
            }
        });
    }
</script>
</body>
</html>