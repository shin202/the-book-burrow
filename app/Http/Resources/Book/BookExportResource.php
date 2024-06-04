<?php

namespace App\Http\Resources\Book;

use App\Http\Resources\Author\AuthorListResource;
use App\Http\Resources\Genre\GenreListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookExportResource extends JsonResource
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
            'description' => $this->description,
            'number_of_pages' => $this->number_of_pages,
            'publisher' => $this->whenLoaded('publisher')->slug,
            'publication_date' => $this->publication_date,
            'authors' => AuthorListResource::collection($this->whenLoaded('authors'))->pluck('slug')->implode(', '),
            'genres' => GenreListResource::collection($this->whenLoaded('genres'))->pluck('slug')->implode(', '),
            'slug' => $this->slug,
        ];
    }
}
