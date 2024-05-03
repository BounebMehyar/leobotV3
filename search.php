<?php

$jsonData = file_get_contents("translated_data.json");

$data = json_decode($jsonData, true);

$q = $_GET["search_query"];

$hint = "";

foreach ($data as $item) {
    foreach ($item as $language => $content) {
         if (strpos(strtolower($content['question']), strtolower($q)) !== false) {
          $hint .= "<div><a href='#'>" . $content['question'] . "</a></div>"; 
        }
    }
}

$response = ($hint == "") ? "No suggestion" : $hint;


echo $response;
