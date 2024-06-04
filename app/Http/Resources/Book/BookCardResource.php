<?php

namespace App\Http\Resources\Book;

use App\Http\Resources\Author\AuthorListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class BookCardResource extends JsonResource
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
            'title' => $this->title,
            'cover_image' => $this->cover_image,
            'authors' => AuthorListResource::collection($this->whenLoaded('authors')),
            'original_price' => Number::currency($this->price),
            'has_discount' => isset($this->discount_price),
            'discount_price' => $this->discount_price ? Number::currency($this->discount_price) : null,
            'slug' => $this->slug,
            'average_rating' => round($this->average_rating, 2),
            'sale_from' => $this->whenLoaded('currentDeal', fn() => $this->currentDeal->start_date),
            'sale_to' => $this->whenLoaded('currentDeal', fn() => $this->currentDeal->end_date),
        ];
    }
}
