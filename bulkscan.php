<?php
$f = fopen("./data/scan.txt", "a");
fwrite($f, date("Y-m-d H:i:s") . "\n");
fclose($f);

$url = "http://localhost:5053/index.php/bulkscan";
$html_string = file_get_contents($url);

$f = fopen("./data/scan.txt", "a");
fwrite($f, date("Y-m-d H:i:s") . "\n\n");
fclose($f);