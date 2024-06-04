<?php

namespace App\Http\Resources\Book;

use App\Http\Resources\Author\AuthorListResource;
use App\Http\Resources\Genre\GenreListResource;
use App\Http\Resources\Publisher\PublisherListResource;
use App\Http\Resources\Review\ReviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class BookShowResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $discountPrice = $this->discount_price;
        $reviews = $this->whenLoaded('reviews');
        $user_rating = $this->ratings()
            ->where('user_id', $request->user()?->id)
            ->first();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'description' => $this->description,
            'cover_image' => $this->cover_image,
            'publisher' => PublisherListResource::make($this->whenLoaded('publisher')),
            'publication_date' => $this->publication_date,
            'quantity_in_stock' => $this->quantity_in_stock,
            'slug' => $this->slug,
            'authors' => AuthorListResource::collection($this->whenLoaded('authors')),
            'genres' => GenreListResource::collection($this->whenLoaded('genres')),
            'stock_status' => $this->stock_status,
            'original_price' => Number::currency($this->price),
            'has_discount' => isset($discountPrice),
            'discount_price' => $this->when(isset($discountPrice), fn() => $discountPrice, null),
            'reviews' => ReviewResource::collection($reviews),
            'reviews_count' => $this->reviews_count,
            'average_rating' => round($this->average_rating, 2),
            'rating_group_count' => $this->rating_group_count,
            'is_rated' => isset($user_rating),
            'user_rating' => $user_rating->rating ?? null,
        ];
    }
}
