<?php

// Генерация пароля
function mosMakePassword($length = 6)
{
    $salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789._#$@%&*+|=^!~";
    $len = strlen($salt) - 1;
    $makepass = '';

    for ($i = 0; $i < $length; $i++) {
        $makepass .= $salt[mt_rand(0, $len)];
    }

    return $makepass;
}

// Проверка на корректность ввода данных
function checkValid($value, $type = 'text', $priz = true)
{
    if ($priz) {
        $value = strip_tags($value);
    }

    $value = trim($value);

    if ('login' == $type) {
        $value = preg_replace('/([^a-z0-9_])/i', '', $value);
    } else if ('pass' == $type) {
        $value = preg_replace('/([^a-z0-9\.@!+\#_])/i', '', $value);
    } else if ('email' == $type) {
        $value = preg_replace('/([^a-z0-9\.@\-_])/i', '', $value);
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
    } else if ('text' == $type) {
        $value = preg_replace('/([^a-zа-яё0-9 ,\.№@!+\-\#_\(\)]\'")/i', '', $value);
    } else if ('f' == $type) {
        $value = preg_replace('/([^a-z_])/i', '', $value);
    } else if ('url' == $type) {
        $value = preg_replace('/([^a-z0-9_-])/i', '', $value);
    } else if ($type == 'si') {
        $value = preg_replace('/([^0-9a-z_-])/i', '', $value);
    }

    return $value;
}

function httpExceptionJson($message = 'Error')
{
    header('Content-Type: application/json', true, 400);

    die(
        json_encode([
            'error' => true,
            'message' => $message,
        ])
    );
}

function httpErrorsJson($errors)
{
    header('Content-Type: application/json', true, 400);

    die(
        json_encode([
            'error' => true,
            'errors' => $errors,
        ])
    );
}

function httpResponseJson($response)
{
    die(json_encode($response));
}

function validate($data)
{
    foreach ($data as $k => $v) {
        if (!$v) {
            $errors[$k] = 'Required field "'.ucfirst($k).'"';
        }
    }

    return $errors ?? false;
}

function setToken($v)
{
    return hash('sha256', $v . APP_SALT);
}

function isLogin()
{
    return (new UserController())->isLogin();
}

function hasPostMethod()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return httpExceptionJson('The method is not supported.');
    }
}

function hashPwd($password)
{
    return md5($password . APP_SALT);
}

function validSecurePwd($password)
{
    $rules = [
        'a-z',
        'A-Z',
        '0-9',
        '@$%^&=',
    ];

    $cnt = count($rules);

    foreach ($rules as $rule) {
        if (preg_match('#['.$rule.']#', $password)) {
            $cnt--;
        }
    }

    return (bool)$cnt;
}

function validSimpePwd($password)
{
    $badPwd = [
        '123456',
        '123456789',
        'qwerty',
    ];

    return in_array($password, $badPwd);
}