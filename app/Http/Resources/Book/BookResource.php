<?php

namespace App\Http\Resources\Book;

use App\Http\Resources\Author\AuthorListResource;
use App\Http\Resources\Genre\GenreListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $discountPrice = $this->discount_price;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'cover_image' => $this->cover_image,
            'publisher' => $this->whenLoaded('publisher')?->name,
            'publication_date' => $this->publication_date,
            'quantity_in_stock' => $this->quantity_in_stock,
            'authors' => AuthorListResource::collection($this->whenLoaded('authors'))->pluck('full_name')->implode(', '),
            'genres' => GenreListResource::collection($this->whenLoaded('genres'))->pluck('name')->implode(', '),
            'slug' => $this->slug,
            'stock_status' => $this->stock_status,
            'original_price' => Number::currency($this->price),
            'has_discount' => isset($discountPrice),
            'discount_price' => $this->when(isset($discountPrice), fn() => Number::currency($discountPrice), 'N/A'),
        ];
    }
}
