<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\SendOrderPlacedNotification;
use App\Models\User;
use App\Notifications\OrderConfirmation;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Bus;

class SendOrderPlacedNotificationListener implements ShouldQueue, ShouldHandleEventsAfterCommit
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order->load([
            'items',
            'user' => fn($query) => $query->select('id', 'username', 'email'),
        ]);

        // Send order confirmation email to the user
        $order->user->notify(new OrderConfirmation($order));

        // Send order placed notification to all admins
        $jobs = [];
        $admins = User::administrators()->get();
        foreach ($admins as $admin) {
            $jobs[] = new SendOrderPlacedNotification($order, $admin);
        }

        Bus::batch($jobs)->name('SendOrderPlacedNotificationListener')->dispatch();
    }
}
