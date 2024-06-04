<?php

namespace App\Notifications;

use App\Enums\BroadcastEventEnum;
use App\Models\Order;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification implements ShouldQueue
{
    use Queueable;

    protected Order $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(): array
    {
        return [
            'from' => $this->order->user->username,
            'channel' => 'admin-notification',
            'event' => BroadcastEventEnum::ORDER_PLACED,
            'title' => 'You have a new order',
            'summary' => "You received a new order from {$this->order->user->username}",
            'content' => view('notifications.order_placed', [
                'order' => $this->order,
            ])->toHtml(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param object $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'from' => $this->order->user->username,
            'event' => BroadcastEventEnum::ORDER_PLACED,
            'title' => 'Order Placed',
            'summary' => 'A new order has been placed.',
        ]);
    }

    public function broadcastAs(): string
    {
        return BroadcastEventEnum::ORDER_PLACED->value;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('admin.notification');
    }
}
