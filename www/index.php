<?php

session_start();

include_once '../app/core.php';

if (file_exists($file = APP_VIEW . $view . '.tpl.php')) {
    include_once $file;
} else {
    die('No content');
}
