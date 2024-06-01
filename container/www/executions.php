<?php
/**
 * Returns all executions
 */
$LIMIT_EXECUTIONS=15;

$targetFolder = getcwd() . '/status/'; 

function getFilesInFolder($folder) {
    $files = array();
    if (is_dir($folder)) {
        $items = scandir($folder);
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..' && is_file($folder . '/' . $item)) {
                if ($item != "title.txt") {
                    $files[] = $item;
                }                    
            }
        }
    }
    return $files;
}

$files = getFilesInFolder($targetFolder);
krsort($files);

header('Content-Type: application/json');
echo json_encode(array_slice($files, 0, $LIMIT_EXECUTIONS));

?>
