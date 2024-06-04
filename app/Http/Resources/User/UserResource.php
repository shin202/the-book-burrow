<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
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
            'avatar' => $this->avatar,
            'fullName' => $this->whenLoaded('profile', fn() => $this->profile->full_name),
            'gender' => $this->whenLoaded('profile', fn() => $this->gender),
            'age' => $this->whenLoaded('profile', fn() => $this->profile?->age),
            'country' => $this->whenLoaded('profile', fn() => $this->profile->country),
            'status' => $this->status,
        ];
    }
}
