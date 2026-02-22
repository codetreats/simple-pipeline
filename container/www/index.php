<?php
    include 'util.php';
    $DIR = '/var/www/html/pipeline';
    $status_dir = $DIR . "/status";
    $trigger_dir = $DIR . "/trigger";
    $menu = get_menu($DIR);
    $title = get_title($status_dir);
    $jobs = get_job_overview($status_dir);
    $enabled = get_enabled($trigger_dir);
?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' href='style.css'>
    <link rel='stylesheet' href='menu.css'>
    <link rel='stylesheet' href='custom.css'>
    <script src="index.js" defer></script>

    <title><?php echo $title ?></title>
</head>

<body>
    <div class='hamburger-menu'>
        <input id='menu__toggle' type='checkbox' />
        <label class='menu__btn' for='menu__toggle'>
          <span></span>
        </label>    
        <div class='menu__overlay' onclick="document.getElementById('menu__toggle').checked = false;"></div>
        <ul id='menu__box' class='menu__box'>
            <?php echo $menu ?>
        </ul>
        <h1 id='main_title'><?php echo $title ?></h1>
        <div style='flex: 1;'></div>
        <div class='switch-container' title='Disable/Enable Jobs'>
            <label class='switch'>
                <input id='enabled_switch' type='checkbox' <?php echo $enabled ?> >
                <span class='slider round'></span>
            </label>
        </div>
        <button title='Cancel Job' class='square_button' id='cancel_button'></button>
        <button title='Reload' class='square_button' id='reload_button' onclick="window.location.href='index.php'"></button>
        <button title='Run Job' class='square_button' id='trigger_button'></button>
    </div>
    <div id='params_container'>
        <label for='params'>Params: </label>
        <input type='text' id='params' name='params'>
    </div>
    <div id='main_container'><?php echo $jobs ?></div>
</body>

</html>