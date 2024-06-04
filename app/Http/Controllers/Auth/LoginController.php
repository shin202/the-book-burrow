<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\LoginService;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class LoginController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/Login');
    }

    public function authenticated(LoginRequest $request, LoginService $loginService)
    {
        try {
            $loginService->authenticated($request->validated());
        } catch (BadRequestException $e) {
            return redirect()->back()->withErrors(['usernameOrEmail' => $e->getMessage()]);
        }

        return redirect()->intended(route('landing.index'));
    }
}
