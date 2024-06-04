<?php

namespace App\Http\Resources\Author;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
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
            'full_name' => $this->full_name,
            'biography' => $this->when($this->biography, substr($this->biography, 0, 100) . '...', 'N/A'),
            'avatar' => $this->avatar,
            'slug' => $this->slug,
        ];
    }
}
