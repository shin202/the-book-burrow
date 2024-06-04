<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordConfirmationController extends Controller
{
    public function confirm(Request $request)
    {
        if (!Hash::check($request->currentPassword, $request->user()->password)) {
            return redirect()
                ->back()
                ->withErrors([
                    'currentPassword' => 'The provided password is incorrect.',
                ]);
        }

        $request->session()->passwordConfirmed();

        return redirect()->intended();
    }
}
