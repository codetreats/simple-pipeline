<?php

function isValidDirectoryName($name) {
    return preg_match('/^[a-zA-Z0-9_-]+$/', $name);
}


// Param missing?
if (!isset($_GET['job']) ) {
    return;
}

if (!isset($_GET['enabled'])) {
    return;
}
$enabled = $_GET['enabled'];
if ($enabled != "0" && $enabled != "1") {
    return;
}

// Validate job param
if ($_GET['job'] != "" && !isValidDirectoryName($_GET['job'])) {
    return;
}

$filename = getcwd() . '/trigger/' . $_GET["job"] . '/enabled.flag'; 
$file = fopen($filename, 'w');
fwrite($file, $enabled . "\n");
fclose($file);
?>