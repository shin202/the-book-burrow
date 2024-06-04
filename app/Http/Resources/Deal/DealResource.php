<?php

namespace App\Http\Resources\Deal;

use App\Http\Resources\Book\BookListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
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
            'book' => BookListResource::make($this->whenLoaded('book')),
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discountValueFmt,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];
    }
}
