<?php

namespace App\Broadcasting;

use App\Models\User;

class OrderNotificationChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, $orderNumber): array|bool
    {
        return $user->orders()->where('order_number', $orderNumber)->exists();
    }
}
