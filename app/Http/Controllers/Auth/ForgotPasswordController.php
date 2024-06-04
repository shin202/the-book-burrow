<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;

class ForgotPasswordController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function forgot(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->validated());

        return $status === Password::RESET_LINK_SENT ?
            redirect()->back()->with([
                'toast.severity' => 'success',
                'toast.message' => __($status),
            ]) : redirect()->back()->withErrors([
                'email' => __($status),
            ]);
    }
}
