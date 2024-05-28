<?php
if (isset($_GET['department'])) {
    $department = htmlspecialchars($_GET['department']);
    $filePath = 'departments/' . $department . '.json';
    if (file_exists($filePath)) {
        $data = file_get_contents($filePath);
        if ($data !== false) {
            $questionsReponses = json_decode($data, true);
            if (!is_null($questionsReponses)) {
                foreach ($questionsReponses as $index => $qr) {
                    echo '<li>';
                    echo '<strong>' . htmlspecialchars($qr['question']) . '</strong> - ' . htmlspecialchars($qr['reponse']);
                    echo ' <a href="modifier.php?question=' . urlencode($qr['question']) . '&department=' . urlencode($department) . '">Modifier</a>';
                    echo '<form method="post" action="index.php" style="display:inline;">';
                    echo '<input type="hidden" name="delete" value="true">';
                    echo '<input type="hidden" name="index" value="' . $index . '">';
                    echo '<input type="submit" value="Supprimer" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette question ?\');">';
                    echo '</form>';
                    echo '</li>';
                }
            } else {
                echo '<li>No questions found for this department.</li>';
            }
        } else {
            echo '<li>Error loading JSON data.</li>';
        }
    } else {
        echo '<li>No questions found for this department.</li>';
    }
} else {
    echo '<li>No department selected.</li>';
}

