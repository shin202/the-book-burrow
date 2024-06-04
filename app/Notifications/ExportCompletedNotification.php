<?php

namespace App\Notifications;

use App\Enums\EventEnum;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ExportCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected User $user;
    protected string $filePath;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $filePath)
    {
        $this->user = $user;
        $this->filePath = $filePath;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toDataBase(): array
    {
        return [
            'channel' => "admin.{$this->user->id}.notification",
            'event' => EventEnum::EXPORT_COMPLETED,
            'from' => 'System',
            'title' => 'Data Exported',
            'summary' => 'Data has been exported successfully. You can download now.',
            'content' => view('notifications.export_completed', [
                'downloadUrl' => $this->downloadUrl(),
            ])->toHtml(),
        ];
    }

    private function downloadUrl(): string
    {
        return URL::temporarySignedRoute('dashboard.download',
            now()->addMinutes(5), ['filePath' => $this->filePath]
        );
    }

    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage([
            'channel' => "admin.{$this->user->id}.notification",
            'event' => EventEnum::EXPORT_COMPLETED,
            'from' => 'System',
            'title' => 'Data Exported',
            'summary' => 'Data has been exported successfully. You can download now.',
        ]);
    }

    public function broadcastAs(): string
    {
        return EventEnum::EXPORT_COMPLETED->value;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("admin.{$this->user->id}.notification");
    }
}
