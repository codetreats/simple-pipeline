<?php

if (isset($_GET['filename'])) {
    $filename = $_GET['filename'];
} else {
    $filename = "trigger";
}

// Validate filename param
if (!in_array($filename, ["trigger", "cancel", "enabled"])) {
    return;
}

$filename = getcwd() . '/trigger/' . $filename . ".flag"; 
$file = fopen($filename, 'w');
if (isset($_GET['params'])) {
    $params = $_GET['params'];
    $params_regex = '/^[A-Za-z0-9-_ ]*$/';
    if (preg_match($params_regex, $params)) {
        fwrite($file, "PARAMS=\"" . $params . "\"\n");
    } else {
        echo "Forbidden params: '$params'";
    }
}
if (isset($_GET['override_monitor_src'])) {
    $src = $_GET['override_monitor_src'];
    $src_regex = '/^[A-Za-z0-9][-_A-Za-z0-9]*[A-Za-z0-9](\.[A-Za-z0-9][-_A-Za-z0-9]*[A-Za-z0-9]+)*$/';
    if (preg_match($src_regex, $src)) {
        fwrite($file, "OVERRIDE_MONITOR_SRC=" . $src . "\n");
    } else {
        echo "Forbidden src: '$src'";
    }
}
fclose($file);
header('Location: /pipeline/index.php');
?>