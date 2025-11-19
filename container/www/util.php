<?php

include 'model/Job.php';
date_default_timezone_set("Europe/Berlin");

function get_job_overview(string $status_dir) {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    return '<table id="job_overview">' . get_jobs_as_html($status_dir, $limit) . '</table>';
}

function get_jobs_as_html(string $status_dir, int $limit) {
    $html = "";
    foreach (get_jobs($status_dir, $limit) as $job) {
        $html .= $job->toHtml();
    }
    return $html;
}

function get_jobs(string $status_dir, int $limit) {    
    $jobs = [];
    $files = [];
    foreach(scandir($status_dir) as $file) {
        if ($file !== '.' && $file !== '..' && str_starts_with($file, "20") && str_ends_with($file, '.txt')) {
            $files[] = $file;
        }
    }

    $files = array_slice(array_reverse($files), 0, $limit);
    foreach ($files as $file) {
        $jobs[] = Job::from($file, file_get_contents($status_dir . '/' . $file));
    }
    return $jobs;
}

function get_menu($dir) {
    $menu_file = $dir . '/menu.txt';
    if (file_exists($menu_file)) {
        $lines = file_get_contents($menu_file);
        $menu = "";
        foreach (explode("\n", $lines) as $line) {
            if (trim($line) != "") {
                $caption = explode("=>", $line)[0];
                $link = explode("=>", $line)[1];
                $menu .= "<li><a class='menu__item' href='" . trim($link) . "'>" . trim($caption) . "</a></li>";
            }
        }
        return $menu;
    } else {
        return "";
    }
}

function get_title($status_dir) {
    $title_file = $status_dir . '/title.txt';
    if (file_exists($title_file)) {
        $text = file_get_contents($title_file);
        $lines = array_filter(explode("\n", $text), function($line) {
            return trim($line) !== '';
        });
        if (!empty($lines)) {
            return trim($lines[0]);
        }
    }
    return "unnamed job";
}

function get_enabled($trigger_dir) {
    $enabled_file = $trigger_dir . '/enabled.flag';
    if (file_exists($enabled_file)) {
        $text = trim(file_get_contents($enabled_file));
        if ($text == "0") {
            return "";
        }
    }
    return " checked='checked'";
}

?>