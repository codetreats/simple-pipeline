<?php
/**
 * Returns all jobs
 */

$targetDirectory = getcwd() . '/status'; 

function getFoldersInDirectory($directory) {
    $folders = array();
    $items = scandir($directory);
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..' && is_dir($directory . '/' . $item)) {
            $folders[] = $item;
        }
    }
    return $folders;
}

$folders = getFoldersInDirectory($targetDirectory);

header('Content-Type: application/json');
echo json_encode($folders);
?>