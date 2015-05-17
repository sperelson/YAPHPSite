<?php

define('APPSTART', microtime(true));

if (!defined("DIRECTORY_SEPARATOR")) {
    define("DIRECTORY_SEPARATOR", "/");
}

require __DIR__ . DIRECTORY_SEPARATOR . 'boot' . DIRECTORY_SEPARATOR . 'boot.php';
