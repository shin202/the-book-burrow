<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class SendOrderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly Order $order, protected ?string $status = null)
    {
        $this->status = $status ?? $this->order->currentStatus->status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast'];
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('orders.' . $this->order->order_number);
    }

    public function broadcastAs(): string
    {
        return "OrderStatusUpdated";
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => "Order {$this->order->order_number} status has been updated to {$this->status}",
            'status' => $this->status,
            'timestamp' => now()
        ]);
    }
}
