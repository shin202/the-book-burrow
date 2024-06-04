<?php

namespace App\Http\Resources\Book;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookUpdateResource extends JsonResource
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
            'title' => $this->title,
            'authors' => $this->whenLoaded('authors')->pluck('id'),
            'genres' => $this->whenLoaded('genres')->pluck('id'),
            'publisher_id' => $this->publisher_id,
            'number_of_pages' => $this->number_of_pages,
            'slug' => $this->slug,
            'description' => $this->description,
            'cover_image' => $this->cover_image,
            'isbn' => $this->isbn,
            'cost' => $this->whenLoaded('latestCost', fn() => $this->latestCost, null)?->cost,
            'price' => $this->price,
            'quantity_in_stock' => $this->quantity_in_stock,
            'publication_date' => $this->publication_date,
        ];
    }
}
