<?php

function isValidDirectoryName($name) {
    return preg_match('/^[a-zA-Z0-9_-]+$/', $name);
}

function isValidFilename($name) {
    return in_array($name, ["trigger", "cancel", "enabled"]);
}

// Param missing?
if (!isset($_GET['job']) || !isset($_GET['filename'])) {
    return;
}

// Validate job param
if ($_GET['job'] != "" && !isValidDirectoryName($_GET['job'])) {
    return;
}

// Validate job param
if (!isValidFilename($_GET['filename'])) {
    return;
}

$filename = getcwd() . '/trigger/' . $_GET["job"] . '/' . $_GET["filename"] . ".flag"; 
$file = fopen($filename, 'w');
if (isset($_GET['params'])) {
    fwrite( $file, $_GET['params'] . "\n");
}
fclose($file);
?>