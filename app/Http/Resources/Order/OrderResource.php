<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class OrderResource extends JsonResource
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
            'order_number' => $this->order_number,
            'user' => $this->whenLoaded('user', $this->user),
            'billing_total' => Number::currency($this->billing_total),
            'total_profit' => isset($this->total_profit) ? Number::currency($this->total_profit) : null,
            'status' => $this->currentStatus->status,
            'created_at' => $this->created_at,
        ];
    }
}
