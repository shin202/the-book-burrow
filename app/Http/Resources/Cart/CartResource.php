<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Number;

class CartResource extends JsonResource
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
            'items' => CartItemResource::collection($this->items),
            'subtotal' => Number::currency($this->subtotal()),
            'total' => Number::currency($this->total()),
            'count' => $this->count,
            'discount' => Number::currency($this->discount()),
            'has_discount' => $this->discount() > 0,
            'new_subtotal' => Number::currency($this->newSubtotal()),
            'coupon' => $this->when(Session::has('coupons'), fn() => Session::get('coupons')['model']->code),
            'canCheckout' => $this->total() > 0,
        ];
    }
}
