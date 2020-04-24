<?php

class UserController
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function login()
    {
        hasPostMethod();

        $data['username'] = checkValid($_POST['username'] ?? '', 'login');
        $data['password'] = checkValid($_POST['password'] ?? '', 'pass');

        if ($errors = validate($data)) {
            return httpErrorsJson($errors);
        } else {
            $data['verify_token'] = setToken($_SERVER['HTTP_USER_AGENT'] ?? 'simple-php / localhost');

            if ($user = $this->user->find($data)) {
                $data = [
                    'token' => setToken(mosMakePassword(rand(12, 15))),
                ];

                if ($this->user->update($data, ['id' => $user['id']])) {
                    setcookie("token_uin", $data['token'], time() + (60 * 60 * 24), '/');
                    $_COOKIE['token_uin'] = $data['token'];

                    $_SESSION['user'] = [
                        'full_name' => $user['full_name'],
                    ];

                    return httpResponseJson([
                        'success' => true,
                        'username' => $user['username'],
                    ]);
                }
            } else {
                return httpExceptionJson('Wrong login or password.');
            }
        }

        return httpExceptionJson();
    }

    public function registr()
    {
        hasPostMethod();

        $data['fullname'] = checkValid($_POST['fullname'] ?? '');
        $data['username'] = checkValid($_POST['username'] ?? '', 'login');
        $data['email'] = checkValid($_POST['email'] ?? '', 'email');
        $data['password'] = checkValid($_POST['password'] ?? '', 'pass');

        if ($errors = validate($data)) {
            return httpErrorsJson($errors);
        } else {
            $checkExist = $this->user->find([
                'username' => $data['username'],
                'email' => $data['email'],
            ], ' OR ');

            if ($checkExist) {
                return httpExceptionJson('Username or email already exists');
            } else {
                if ($this->user->insert($data)) {
                    return httpResponseJson([
                        'success' => true,
                    ]);
                }
            }
        }

        return httpExceptionJson();
    }

    public function personalUpdate()
    {
        hasPostMethod();

        $data['fullname'] = checkValid($_POST['fullname'] ?? '');

        if ($errors = validate($data)) {
            return httpErrorsJson($errors);
        } else {
            $fullname = $data['fullname'];

            $data = [
                'token' => $_COOKIE['token_uin'],
                'verify_token' => setToken($_SERVER['HTTP_USER_AGENT'] ?? 'simple-php / localhost'),
            ];

            if ($user = $this->user->find($data)) {
                $this->user->update(
                    ['full_name' => $fullname],
                    ['id' => $user['id']]
                );

                $_SESSION['user'] = [
                    'full_name' => $fullname,
                ];

                return httpResponseJson([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }
        }

        return httpExceptionJson();
    }

    public function changePwd()
    {
        hasPostMethod();

        $data['oldpassword'] = checkValid($_POST['oldpassword'] ?? '', 'pass');
        $data['password'] = checkValid($_POST['password'] ?? '', 'pass');
        $data['confirmpassword'] = checkValid($_POST['confirmpassword'] ?? '', 'pass');

        if ($errors = validate($data)) {
            return httpErrorsJson($errors);
        } else {
            $user = $this->user->find([
                'token' => $_COOKIE['token_uin'],
                'verify_token' => setToken($_SERVER['HTTP_USER_AGENT'] ?? 'simple-php / localhost'),
            ]);

            if ($user) {
                if ($user['password'] != hashPwd($data['oldpassword'])) {
                    $errors['oldpassword'] = 'Invalid current password';
                }

                if (strlen($data['password']) < 6) {
                    $errors['password'] = 'The new password must be at least 6 characters.';
                } else if (validSimpePwd($data['password'])) {
                    $errors['password'] = 'This password is too simple. Specify another.';
                }

                // Verification of the creation of a complex password.
                if (validSecurePwd($data['password'])) {
                    // The demo version is disabled.
                    // $errors['password'] = 'Password must be characters: a-z, A-Z, 0-9 and @, $, %, ^, &, =';
                }

                if ($data['password'] != $data['confirmpassword']) {
                    $errors['confirmpassword'] = 'Invalid password confirmation';
                }

                if ($errors) {
                    return httpErrorsJson($errors);
                } else {
                    $this->user->update(
                        ['password' => $data['password']],
                        ['id' => $user['id']]
                    );
                }

                return httpResponseJson([
                    'success' => true,
                    'message' => 'Password changed successfully',
                ]);
            }

            return httpResponseJson([
                'success' => true,
            ]);
        }
    }

    public function logout()
    {
        hasPostMethod();

        if (isset($_COOKIE['token_uin'])) {
            $data = [
                'token' => $_COOKIE['token_uin'],
                'verify_token' => setToken($_SERVER['HTTP_USER_AGENT'] ?? 'simple-php / localhost'),
            ];

            if ($user = $this->user->find($data)) {
                $this->user->update(
                    ['token' => setToken(mosMakePassword(rand(12, 15)))],
                    ['id' => $user['id']]
                );

                setcookie("token_uin", '', 0, '/');
                unset($_COOKIE['token_uin']);

                return httpResponseJson([
                    'success' => true,
                ]);
            }
        }

        return httpExceptionJson();
    }

    public function isLogin()
    {
        return (bool)(
            isset($_COOKIE['token_uin'])
            && $this->user->find([
                'token' => $_COOKIE['token_uin'],
                'verify_token' => setToken($_SERVER['HTTP_USER_AGENT'] ?? 'simple-php / localhost'),
            ])
        );
    }
}
