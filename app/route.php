<?php

$route = preg_replace('/[^a-z0-9\/_-]/i', '', $_SERVER['REQUEST_URI'] ?? '');

$route = trim($route, '/');
$path = explode('/', $route);

if (count($path) > 1 && $path[0] === 'api') {
    if ($path[1] === 'login') {
        (new UserController())->login();
    } elseif ($path[1] === 'registr') {
        (new UserController())->registr();
    } elseif ($path[1] === 'personal') {
        (new UserController())->personalUpdate();
    } elseif ($path[1] === 'change-pwd') {
        (new UserController())->changePwd();
    } elseif ($path[1] === 'logout') {
        (new UserController())->logout();
    } else {
        //
    }

    exit();
} elseif ($path[0] === 'personal') {
    if (!isLogin()) {
        header('Location: /');
        exit();
    }

    $view = 'personal';
} elseif (!$path[0]) {
    if (isLogin()) {
        header('Location: /personal');
        exit();
    }

    $view = 'main';
} else {
    http_response_code(404);
    $view = 'not_found';
}
