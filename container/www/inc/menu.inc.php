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
