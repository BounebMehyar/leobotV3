<?php
$question = $_POST['question'];
$json = file_get_contents('C:\wamp64\www\getdata\data.json');
$data = json_decode($json, true);
$suggestions = [];
foreach ($data as $item) {
    similar_text($item['question'], $question, $similarity);
    if ($similarity > 50) {
        $suggestions[] = $item['question'];
    }
}
foreach ($suggestions as $suggestion) {
    echo '<div class="suggestedQuestion">' . $suggestion . '</div>';
}
?>
