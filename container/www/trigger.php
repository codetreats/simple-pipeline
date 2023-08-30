<?php

function isValidDirectoryName($name) {
    return preg_match('/^[a-zA-Z0-9_-]+$/', $name);
}

if (isset($_GET['job']) && (isValidDirectoryName($_GET['job']) || $_GET['job'] == "")) {
    $filename = getcwd() . '/trigger/' . $_GET["job"] . '/trigger.flag'; 
    $file = fopen($filename, 'w');
    if (isset($_GET['params'])) {
        fwrite( $file, $_GET['params'] . "\n");
    }
    fclose($file);
}
?>