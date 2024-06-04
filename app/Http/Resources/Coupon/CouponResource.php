<?php

namespace App\Http\Resources\Coupon;

use App\Enums\DiscountTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $value = $this->type === DiscountTypeEnum::FIXED ?
            Number::currency($this->value) :
            "$this->value%";

        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type,
            'value' => $value,
            'minimum_order_amount' => $this->when($this->minumum_order_amount, fn() => Number::currency($this->minimum_order_amount)),
            'usage_limit' => $this->usage_limit,
            'usage_per_user' => $this->usage_per_user,
            'valid_from' => $this->valid_from,
            'valid_to' => $this->valid_to,
            'status' => $this->status,
            'available' => $this->available,
            'description' => $this->description,
        ];
    }
}
