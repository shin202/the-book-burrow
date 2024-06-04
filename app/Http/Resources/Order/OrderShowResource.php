<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class OrderShowResource extends JsonResource
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
            'order_number' => $this->order_number,
            'user' => $this->whenLoaded('user', $this->user),
            'billing_name' => $this->billing_name,
            'billing_email' => $this->billing_email,
            'billing_phone' => $this->billing_phone,
            'billing_address' => $this->billing_address,
            'billing_city' => $this->billing_city,
            'billing_state' => $this->billing_state,
            'billing_country' => $this->billing_country,
            'billing_zip' => $this->billing_zip,
            'billing_discount' => $this->when($this->billing_discount > 0, fn() => Number::currency($this->billing_discount), 'N/A'),
            'billing_discount_code' => $this->billing_discount_code ?: 'N/A',
            'billing_subtotal' => Number::currency($this->billing_subtotal),
            'billing_total' => Number::currency($this->billing_total),
            'total_profit' => Number::currency($this->total_profit),
            'status' => $this->whenLoaded('statuses',
                fn() => $this->statuses->first()->status ?? $this->currentStatus->status),
            'payment' => $this->whenLoaded('payment', $this->payment),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
            'status_history' => $this->whenLoaded('statuses'),
            'payment_method' => $this->when($this->payment_method, fn() => $this->payment_method, 'N/A'),
        ];
    }
}
