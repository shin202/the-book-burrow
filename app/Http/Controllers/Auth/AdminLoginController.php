<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Services\AdminLoginService;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AdminLoginController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/AdminLogin');
    }

    public function authenticate(AdminLoginRequest $request, AdminLoginService $adminLoginService)
    {
        try {
            $adminLoginService->authenticated($request->validated());
        } catch (BadRequestException $e) {
            return redirect()->back()->withErrors(['usernameOrEmail' => $e->getMessage()]);
        }

        return redirect()->intended();
    }
}
