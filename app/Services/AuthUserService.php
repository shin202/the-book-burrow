<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthUserService
{
    public function markAllNotificationsAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function notification(string $id)
    {
        return Auth::user()->notifications()
            ->where('id', $id)
            ->first();
    }

    public function notifications(array $channel)
    {
        return Auth::user()->notifications
            ->whereIn('data.channel', $channel)
            ->forPage(1, 10);
    }
}
