<?php

define('APP_PATH', dirname(__FILE__));
define('APP_VIEW', APP_PATH . '/views/');
define('APP_SALT', 'bYD]QXWI0hxMSX}42lCT,Dltzz54?FR~i2JQ"lxh1O');

include_once APP_PATH . '/functions.php';

spl_autoload_register(function ($class_name) {
    if ($class_name === 'DB') {
        $folder = 'classes';
    } elseif (strpos($class_name, 'Controller') !== false) {
        $folder = 'Controllers';
    } else {
        $folder = 'Models';
    }

    include_once APP_PATH . "/{$folder}/{$class_name}.php";
});

include_once APP_PATH . '/route.php';

