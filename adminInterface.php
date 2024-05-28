<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de questions et réponses</title>
    <link rel="stylesheet" type="text/css" href="styleadmin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container">
    <h2>Ajouter une question et une réponse :</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="departement">Département :</label><br>
        <select id="departement" name="departement">
            <option value="DC">Direction communication</option>
            <option value="RH">Ressources Humaines</option>
            <option value="DG">La direction générale</option>
            <option value="DProd">Direction production</option>
            <option value="DQ">Direction qualité </option>
            <option value="DT">Direction technique</option>
            <option value="DF">Direction financière</option>
            <option value="DProj">Direction projet</option>
            <option value="She">She </option>
        </select>
        <label for="question">Question :</label><br>
        <input type="text" id="question" name="question"><br>
        <label for="reponse">Réponse :</label><br>
        <input type="text" id="reponse" name="reponse"><br>
        <input type="submit" value="Envoyer">
    </form>

    <h2>Liste des questions et réponses :</h2>
    <ul id="questionsArea">
        <?php foreach ($questionsReponses as $index => $qr) : ?>
            <li>
                <strong><?php echo $qr['question'], "</br>"; ?></strong> - <?php echo $qr['reponse']; ?>
                <a href="modifier.php?question=<?php echo urlencode($qr['question']); ?>&department=<?php echo urlencode($initialDepartment); ?>">Modifier</a>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
                    <input type="hidden" name="delete" value="true">
                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                    <input type="submit" value="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?');">
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
    $(document).ready(function() {
        $('#departement').change(function() {
            loadQuestions();
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
