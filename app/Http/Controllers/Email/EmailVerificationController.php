<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailVerificationRequest;
use App\Http\Requests\Auth\ResendEmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmailVerificationController extends Controller
{
    public function notice()
    {
        if (Auth::user()->hasVerifiedEmail()) return Inertia::location(route('landing.index'));

        return Inertia::render('Auth/EmailVerification', [
            'verified' => false,
        ]);
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return Inertia::render('Auth/EmailVerification', [
            'verified' => true,
        ]);
    }

    public function resend(ResendEmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->back()->with([
            'toast.severity' => 'success',
            'toast.message' => 'Verification email sent.',
        ]);
    }
}
