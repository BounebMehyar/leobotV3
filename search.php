<?php
require 'vendor/autoload.php';

$file_path = "questions_departements.csv";
$handle = fopen($file_path, "r"); 

$q = $_GET["search_query"]; // Get the search query from the URL parameter
$hint = "";

// Check if file opened successfully
if ($handle !== FALSE) {
    // Read each line of the CSV file
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Assuming 'question' is in the first column of your CSV
        $question = $data[0];

        // Search for the query in the 'question' field
        if (strpos(strtolower($question), strtolower($q)) !== false) {
            $hint .= "<div><a href='#'>" . htmlspecialchars($question) . "</a></div>";
        }
    }
    fclose($handle);
}

$response = ($hint === "") ? "No suggestion" : $hint;

echo $response;
