<?php

function isValidDirectoryName($name) {
    return preg_match('/^[a-zA-Z0-9_-]+$/', $name);
}

function isValidFilename($name) {
    return in_array($name, ["trigger", "cancel", "enabled"]);
}

if (isset($_GET['job'])) {
    $job = $_GET['job'];
} else {
    $job = "";
}

if (isset($_GET['filename'])) {
    $filename = $_GET['filename'];
} else {
    $filename = "trigger";
}

// Validate job param
if ($job != "" && !isValidDirectoryName($job)) {
    return;
}

// Validate job param
if (!isValidFilename($filename)) {
    return;
}

$filename = getcwd() . '/trigger/' . $job . '/' . $filename . ".flag"; 
$file = fopen($filename, 'w');
if (isset($_GET['params'])) {
    $params = $_GET['params'];
    $params_regex = '/^[A-Za-z0-9-_ ]*$/';
    if (preg_match($params_regex, $params)) {
        fwrite($file, "PARAMS=\"" . $params . "\"\n");
    } else {
        echo "Forbidden params:'$params'";
    }
}
if (isset($_GET['override_monitor_src'])) {
    $src = $_GET['override_monitor_src'];
    $src_regex = '/^[A-Za-z0-9][-_A-Za-z0-9]*[A-Za-z0-9](\.[A-Za-z0-9][-_A-Za-z0-9]*[A-Za-z0-9]+)*$/';
    if (preg_match($src_regex, $src)) {
        fwrite($file, "OVERRIDE_MONITOR_SRC=" . $src . "\n");
    } else {
        echo "Forbidden src";
    }
}
fclose($file);
?>