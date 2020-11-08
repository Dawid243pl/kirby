<?php

use Kirby\Exception\InvalidArgumentException;
use Kirby\Exception\NotFoundException;
use Kirby\Exception\PermissionException;

/**
 * Authentication
 */
return [
    [
        'pattern' => 'auth',
        'method'  => 'GET',
        'action'  => function () {
            if ($user = $this->kirby()->auth()->user()) {
                return $this->resolve($user)->view('auth');
            }

            throw new NotFoundException('The user cannot be found');
        }
    ],
    [
        'pattern' => 'auth/code',
        'method'  => 'POST',
        'auth'    => false,
        'action'  => function () {
            $auth = $this->kirby()->auth();

            // csrf token check
            if ($auth->type() === 'session' && $auth->csrf() === false) {
                throw new InvalidArgumentException('Invalid CSRF token');
            }

            $user = $auth->verifyChallenge($this->requestBody('code'));
            if ($user !== null) {
                return [
                    'code'   => 200,
                    'status' => 'ok',
                    'user'   => $this->resolve($user)->view('auth')->toArray()
                ];
            } else {
                throw new PermissionException(['key' => 'access.code']);
            }
        }
    ],
    [
        'pattern' => 'auth/login',
        'method'  => 'POST',
        'auth'    => false,
        'action'  => function () {
            $auth = $this->kirby()->auth();

            // csrf token check
            if ($auth->type() === 'session' && $auth->csrf() === false) {
                throw new InvalidArgumentException('Invalid CSRF token');
            }

            $email    = $this->requestBody('email');
            $long     = $this->requestBody('long');
            $password = $this->requestBody('password');

            if ($password) {
                $user = $this->kirby()->auth()->login($email, $password, $long);

                return [
                    'code'   => 200,
                    'status' => 'ok',
                    'user'   => $this->resolve($user)->view('auth')->toArray()
                ];
            } else {
                $mode = $this->kirby()->system()->loginMode();
                if (in_array($mode, ['password-reset', 'email']) !== true) {
                    throw new InvalidArgumentException('Login without password is not enabled');
                }

                $challenge = $auth->createChallenge($email, $long, $mode === 'password-reset' ? 'reset' : 'login');

                return [
                    'code'   => 200,
                    'status' => 'ok',

                    // don't leak users that don't exist at this point
                    'challenge' => $challenge ?? 'email'
                ];
            }
        }
    ],
    [
        'pattern' => 'auth/logout',
        'method'  => 'POST',
        'auth'    => false,
        'action'  => function () {
            $this->kirby()->auth()->logout();
            return true;
        }
    ],
];
