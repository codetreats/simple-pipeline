<?php

// Param missing?
if (!isset($_GET['enabled'])) {
    return;
}

$enabled = $_GET['enabled'];
if ($enabled != "0" && $enabled != "1") {
    return;
}

$filename = getcwd() . '/trigger/enabled.flag'; 
$file = fopen($filename, 'w');
fwrite($file, $enabled . "\n");
fclose($file);
?>