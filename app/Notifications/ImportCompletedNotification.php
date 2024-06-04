<?php

namespace App\Notifications;

use App\Enums\EventEnum;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ImportCompletedNotification extends Notification
{
    use Queueable;

    protected User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
            'channel' => "admin.{$this->user->id}.notification",
            'event' => EventEnum::IMPORT_COMPLETED,
            'from' => 'System',
            'title' => 'Data Imported',
            'summary' => 'Data has been imported successfully.',
            'content' => view('notifications.import_completed')->toHtml()
        ];
    }

    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage([
            'channel' => "admin.{$this->user->id}.notification",
            'event' => EventEnum::IMPORT_COMPLETED,
            'from' => 'System',
            'title' => 'Data Imported',
            'summary' => 'Data has been imported successfully.',
        ]);
    }

    public function broadcastAs(): string
    {
        return EventEnum::IMPORT_COMPLETED->value;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("admin.{$this->user->id}.notification");
    }
}
