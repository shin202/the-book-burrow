<?php

namespace App\Http\Resources\Publisher;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublisherResource extends JsonResource
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
            'name' => $this->name,
            'description' => substr($this->description, 0, 100),
            'slug' => $this->slug,
            'status' => $this->status,
            'contact_information' => $this->contact_information,
        ];
    }
}
