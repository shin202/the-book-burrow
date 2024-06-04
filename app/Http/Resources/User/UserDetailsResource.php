<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailsResource extends JsonResource
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
            'firstName' => $this->getProfile('first_name'),
            'lastName' => $this->getProfile('last_name'),
            'gender' => $this->getProfile('gender'),
            'dateOfBirth' => $this->getProfile('date_of_birth'),
            'country' => $this->getProfile('country'),
            'roleId' => $this->whenLoaded('roles', fn() => $this->roles->first()?->id),
        ];
    }

    private function getProfile(string $field)
    {
        return $this->whenLoaded('profile', fn() => $this->profile->$field);
    }
}
