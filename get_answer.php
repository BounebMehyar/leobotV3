<?php
$selectedQuestion = $_POST['question'];

$jsonData = file_get_contents("translated_data.json");

$data = json_decode($jsonData, true);

foreach ($data as $item) {
    foreach ($item as $language => $contant) {
        if ($contant['question'] === $selectedQuestion) {
           $response = $contant['answer'];
           break;
        }

    }
}

echo $response ?? "English: No answer found. Please choose one of the suggestions or come back later. Thank you <br> French: Aucune réponse trouvée. Veuillez choisir l'une des suggestions ou revenir plus tard. Merci <br> Arabic: لم يتم العثور على إجابة. يرجى اختيار إحدى الاقتراحات أو العودة لاحقًا. شكرًا";

