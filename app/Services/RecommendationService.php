<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Rating;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class RecommendationService
{
    private string $baseUrl;

    public function __construct(protected Book $book, protected Rating $rating)
    {
        $this->baseUrl = config('general_settings.site.recommendation.url');
    }

    public function itemSimilarRecommended(array $slugs, int $limit = 5): array
    {
        $data = Http::withOptions([
            'verify' => false,
        ])
            ->get($this->baseUrl . '/item-based-similar', [
                'items' => join(',', $slugs),
                'top_n' => $limit,
            ])
            ->json() ?? [];

        return collect($data)->pluck('items')->flatten()->unique()->values()->toArray();
    }

    public function itemBasedRecommended(int $userId, int $limit = 5, bool $excludeKnown = true): array
    {
        return Http::withOptions([
            'verify' => false,
        ])
            ->get($this->baseUrl . '/item-based-cf/' . $userId, [
                'top_n' => $limit,
                'exclude_known' => $excludeKnown,
            ])
            ->json() ?? [];
    }

    public function contentBasedRecommended(array $slugs, int $limit = 10): array
    {
        $data = Http::withOptions([
            'verify' => false,
        ])
            ->get($this->baseUrl . '/content-based', [
                'items' => join(',', $slugs),
                'top_n' => $limit,
            ])
            ->json();

        return collect($data)->pluck('items')->flatten()->unique()->values()->toArray();
    }

    /**
     * @throws ConnectionException
     */
    public function itemBasedTrain(array $id = null, int $topK = 150)
    {
        $ratings = $this->rating
            ->newQuery()
            ->select(['user_id', 'book_id as item_id', 'rating'])
            ->when(!is_null($id), fn($query) => $query->whereIn('book_id', $id))
            ->get();

        $response = Http::withOptions([
            'verify' => false,
        ])
            ->withBody(json_encode($ratings))
            ->post($this->baseUrl . '/train/item-based', [
                'only_top_k' => $topK,
            ]);

        return $response->json();
    }

    /**
     * @throws ConnectionException
     */
    public function itemContentTrain(array $id = null)
    {
        $books = $this->book
            ->newQuery()
            ->with([
                'authors' => fn($query) => $query->select(['authors.id', 'slug']),
                'genres' => fn($query) => $query->select(['genres.id', 'slug']),
            ])
            ->select(['id', 'slug', 'description'])
            ->withCount('ratings as ratings_count')
            ->withAvg('ratings as average_rating', 'rating')
            ->withCount('orders as orders_count')
            ->withCount('reviews as reviews_count')
            ->when(!is_null($id), fn($query) => $query->whereIn('id', $id))
            ->get();

        $books = $books->map(fn($book) => [
            'id' => $book->id,
            'slug' => $book->slug,
            'description' => strip_tags($book->description),
            'ratings_count' => $book->ratings_count,
            'average_rating' => $book->average_rating,
            'orders_count' => $book->orders_count,
            'reviews_count' => $book->reviews_count,
            'authors' => $book->authors->pluck('slug')->implode(','),
            'genres' => $book->genres->pluck('slug')->implode(','),
        ]);

        $response = Http::withOptions([
            'verify' => false,
        ])
            ->withBody(json_encode($books))
            ->post($this->baseUrl . '/train/item-content');

        return $response->json();
    }

    public function datasets(string $model = 'item-based-cf'): array|CursorPaginator
    {
        if ($model === 'item-based-cf') {
            return $this->rating
                ->newQuery()
                ->join('books', 'ratings.book_id', '=', 'books.id')
                ->join('users', 'ratings.user_id', '=', 'users.id')
                ->select(['ratings.id', 'username', 'books.title', 'rating', 'ratings.created_at'])
                ->cursorPaginate(10);
        }

        if ($model === 'content-based') {
            return $this->book
                ->newQuery()
                ->select(['id', 'title'])
                ->withCount('ratings as ratings_count')
                ->addSelect([
                    'average_rating' => fn($query) => $query->from('ratings')
                        ->whereColumn('ratings.book_id', 'books.id')
                        ->selectRaw('round(avg(rating), 2)'),
                ])
                ->withCount('orders as orders_count')
                ->withCount('reviews as reviews_count')
                ->cursorPaginate(10);
        }

        return [];
    }
}
