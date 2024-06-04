<?php

namespace App\Services;

use App\Http\Resources\Book\BookCardCollection;
use App\Http\Resources\Book\BookCardResource;
use App\Models\Book;

class SearchService
{
    public function __construct(protected Book $book)
    {
    }

    public function liveSearch(string $q = null, string $searchField = 'all')
    {
        if (is_null($q)) {
            return [];
        }

        return $this->book
            ->newQuery()
            ->with([
                'authors' => fn($query) => $query->select(['authors.id', 'first_name', 'last_name', 'slug'])->limit(1)
            ])
            ->select(['books.id', 'title', 'slug', 'cover_image'])
            ->when($searchField === 'all', function ($query) use ($q) {
                $query->whereTitleContains($q)
                    ->orWhere
                    ->whereAuthorNameContains($q);
            })
            ->when($searchField === 'title', function ($query) use ($q) {
                $query->whereTitleContains($q);
            })
            ->when($searchField === 'author', function ($query) use ($q) {
                $query->whereAuthorNameContains($q);
            })
            ->limit(5)
            ->get();
    }

    public function search(string $q = null, string $searchField = 'all', int $perPage = 10)
    {
        if (is_null($q) || $q === '') {
            return BookCardResource::collection([]);
        }

        $books = $this->book
            ->newQuery()
            ->with([
                'authors' => fn($query) => $query->select(['authors.id', 'first_name', 'last_name', 'slug'])->limit(1),
                'currentDeal'
            ])
            ->select(['id', 'title', 'slug', 'cover_image', 'price'])
            ->withAvg('ratings as average_rating', 'rating')
            ->when($searchField === 'all', function ($query) use ($q) {
                $query->whereTitleContains($q)
                    ->orWhere
                    ->whereAuthorNameContains($q);
            })
            ->when($searchField === 'title', function ($query) use ($q) {
                $query->whereTitleContains($q);
            })
            ->when($searchField === 'author', function ($query) use ($q) {
                $query->whereAuthorNameContains($q);
            })
            ->paginate($perPage);

        return BookCardCollection::make($books);
    }
}
