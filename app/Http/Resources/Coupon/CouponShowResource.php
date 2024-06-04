<?php

namespace App\Http\Resources\Coupon;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponShowResource extends JsonResource
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
            'code' => $this->code,
            'type' => $this->type,
            'value' => $this->value,
            'minimum_order_amount' => $this->minimum_order_amount,
            'usage_limit' => $this->usage_limit,
            'usage_per_user' => $this->usage_per_user,
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            'is_active' => $this->is_active,
            'couponable_type' => $this->couponable_type,
            'couponable_id' => $this->couponable_id,
            'description' => $this->description,
        ];
    }
}
