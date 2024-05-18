<?php
if (isset($_GET['department']) ) {
    $department = $_GET['department'];
   echo $department;
    $data = file_get_contents("departments/{$department}.json");
    $questionsReponses = json_decode($data, true);
    $output = "";

    foreach ($questionsReponses as $qr) {
        $output .= "<div><strong>{$qr['question']}</strong> - {$qr['reponse']}</div>";
    }

    echo $output;
}
