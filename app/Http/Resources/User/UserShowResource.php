<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Notification\NotificationResource;
use App\Http\Resources\Profile\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserShowResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'status' => $this->status,
            'avatar' => $this->avatar,
            'roles' => $this->whenLoaded('roles'),
            'profile' => ProfileResource::make($this->whenLoaded('profile')),
            'unreadNotifications' => NotificationResource::collection($this->getUnreadNotifications()),
            'settings' => $this->settings,
        ];
    }

    private function getUnreadNotifications()
    {
        $globalChannel = $this->isAdministrator() ? 'admin.notification' : null;

        $channel = $this->isAdministrator() ?
            "admin.{$this->id}.notification" :
            "user.{$this->id}.notification";

        return $this->unreadNotifications()
            ->where('data->channel', $channel)
            ->when($globalChannel, function ($query) use ($globalChannel) {
                $query->orWhere('data->channel', $globalChannel);
            })
            ->limit(10)
            ->get();
    }
}
