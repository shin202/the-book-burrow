<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterService
{
    public function __construct(protected User $user, protected Role $role)
    {

    }

    public function register(array $data): void
    {
        $user = $this->user->create($data);
        $role = $this->role->baseUser();
        $user->roles()->attach($role);
        $emailVerifyToken = $user->emailVerifyTokens()->create([
            'token' => sha1(Str::random(32)),
        ]);

        $user->notify(new VerifyEmailNotification($emailVerifyToken->token));

        Auth::login($user);
    }
}
