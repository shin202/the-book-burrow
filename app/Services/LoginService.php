<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class LoginService
{
    public function authenticated(array $credentials): true
    {
        $credentialType = filter_var($credentials['usernameOrEmail'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $remember = $credentials['remember'] ?? false;
        $credentials = [
            $credentialType => $credentials['usernameOrEmail'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->isAdministrator()) {
                Session::regenerate();
                return true;
            }

            Auth::logout();
        }

        throw new BadRequestException(trans('auth.failed'));
    }
}
