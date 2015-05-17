<?php

/*
 * A poor autoloader
 */

$folders = array("libs", "models", "controllers");
$ds = DIRECTORY_SEPARATOR;

foreach ($folders as $folder) {
    foreach (glob(__DIR__ . $ds . '..' . $ds . $folder . $ds . "*.php") as $filename) {
        require $filename;
    }
}
require __DIR__ . $ds . 'config.php';

// Finally the starting point
require __DIR__ . $ds . '..' . $ds . 'routes.php';
