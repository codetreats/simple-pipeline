<?php
/**
 * Returns all executions of a given job
 */
$LIMIT_EXECUTIONS=15;

function isValidDirectoryName($name) {
    return preg_match('/^[a-zA-Z0-9_-]+$/', $name);
}

if (isset($_GET['job']) && (isValidDirectoryName($_GET['job']) || $_GET['job'] == "")) {
    $targetFolder = getcwd() . '/status/' . $_GET['job']; 

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
} else {
    header("HTTP/1.1 400 Bad Request");
    echo "Invalid folder name.";
}
?>
