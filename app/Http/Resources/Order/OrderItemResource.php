<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'quantity' => $this->pivot->quantity,
            'price' => Number::currency($this->pivot->price),
            'options' => $this->pivot->options,
            'cover_image' => $this->cover_image,
            'total' => Number::currency($this->pivot->quantity * $this->pivot->price),
        ];
    }
}
