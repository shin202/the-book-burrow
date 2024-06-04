<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangeAuthUserPasswordRequest;
use App\Http\Requests\User\DeleteAuthUserAvatarRequest;
use App\Http\Requests\User\UpdateAuthUserAvatarRequest;
use App\Http\Requests\User\UpdateAuthUserPersonalInfoRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        Auth::user()->load('profile');

        return Inertia::render('User/Profile/Index', [
            'isAuthenticated' => Route::has('login') && auth()->check(),
        ]);
    }

    public function updateAvatar(UpdateAuthUserAvatarRequest $request)
    {
        $request->fulfill();
        return redirect()->back()->with([
            'toast.severity' => 'success',
            'toast.message' => 'Your avatar has been updated.',
        ]);
    }

    public function deleteAvatar(DeleteAuthUserAvatarRequest $request)
    {
        $request->fulfill();
        return redirect()->back()->with([
            'toast.severity' => 'success',
            'toast.message' => 'Your avatar has been deleted.',
        ]);
    }

    public function updatePersonalInformation(UpdateAuthUserPersonalInfoRequest $request)
    {
        $request->fulfill();
        return redirect()->back()->with([
            'toast.severity' => 'success',
            'toast.message' => 'Your personal information has been updated.',
        ]);
    }

    public function changePassword(ChangeAuthUserPasswordRequest $request)
    {
        return $request->fulfill();
    }
}
