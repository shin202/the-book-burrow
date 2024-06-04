<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $model = $this->model;

        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => Number::currency($this->price),
            'title' => $model->title,
            'slug' => $model->slug,
            'product_id' => $model->id,
            'cover_image' => $model->cover_image,
            'total' => Number::currency($this->total),
            'subtotal' => Number::currency($this->subtotal),
            'discount_total' => Number::currency($this->discountTotal),
            'has_discount' => $this->discount > 0,
            'new_subtotal' => Number::currency($this->newSubtotal),
        ];
    }
}
