<?php

namespace App\Services;

use App\Exports\BookExport;
use App\Http\Resources\Book\BookCardResource;
use App\Http\Resources\Book\BookListResource;
use App\Imports\Book\BookImport;
use App\Jobs\ExportJob;
use App\Jobs\ImportJob;
use App\Jobs\NotifyUserOfCompletedExportJob;
use App\Jobs\NotifyUserOfCompletedImportJob;
use App\Models\Book;
use App\Models\User;
use App\Sorts\RatingSort;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class BookService
{
    public function __construct(protected Book $book, protected RecommendationService $recommendationService)
    {
        //
    }

    public function list(): AnonymousResourceCollection
    {
        return Cache::remember('list-books', now()->addMinutes(5), function () {
            $books = $this->book->all(['id', 'title', 'cover_image', 'slug']);
            return BookListResource::collection($books);
        });
    }

    public function dashboardList(string $search = null, int $perPage = 5)
    {
        $relations = [
            'authors' => fn($query) => $query->select('authors.id', 'first_name', 'last_name'),
            'genres' => fn($query) => $query->select('genres.id', 'genres.name', 'genres.slug')->limit(3),
            'publisher' => fn($query) => $query->select('publishers.id', 'publishers.name', 'publishers.slug'),
            'currentDeal',
        ];

        $selectedFields = [
            'id',
            'title',
            'isbn',
            'cover_image',
            'publication_date',
            'slug',
            'quantity_in_stock',
            'price',
            'publisher_id',
        ];

        $filterFields = [
            'title',
            'price',
            AllowedFilter::exact('id'),
            AllowedFilter::scope('publication_date', 'wherePublicationDate'),
            AllowedFilter::scope('author', 'whereAuthor'),
            AllowedFilter::scope('genre', 'whereGenre'),
            AllowedFilter::scope('publisher', 'wherePublisher'),
            AllowedFilter::scope('status', 'whereStatus')
        ];

        return QueryBuilder::for(Book::class)
            ->with($relations)
            ->allowedFilters($filterFields)
            ->allowedSorts(['title', 'price', 'publication_date', 'id'])
            ->whereTitleContains($search)
            ->paginate($perPage, $selectedFields);
    }

    public function landingList(int $perPage = 10)
    {
        return QueryBuilder::for(Book::class)
            ->with([
                'authors' => fn($query) => $query->select(['authors.id', 'first_name', 'last_name', 'slug'])->limit(1),
                'currentDeal'
            ])
            ->withAvg('ratings as average_rating', 'rating')
            ->allowedFilters([
                AllowedFilter::callback('price', function ($query, $value) {
                    $query->whereBetween('price', $value);
                }),
                AllowedFilter::scope('author', 'whereAuthor'),
                AllowedFilter::scope('genre', 'whereGenre'),
                AllowedFilter::scope('rating', 'whereRating'),
            ])
            ->allowedSorts([
                'title',
                'publication_date',
                'price',
                AllowedSort::custom('rating', new RatingSort())
            ])
            ->defaultSort('title')
            ->paginate($perPage);
    }

    public function recommended(User $user = null, int $limit = 5): AnonymousResourceCollection|array
    {
        if (!$user) {
            return [];
        }

        $slugs = $this->recommendationService->itemBasedRecommended($user->id, $limit);

        // If no recommendations are returned, recommend based on user's order history
        if (count($slugs) === 0) {
            $userOrderedBooks = $user->orders()
                ->with('items', function ($query) {
                    $query->select(['book_id', 'slug']);
                });

            $slugs = $userOrderedBooks->get()
                ->pluck('items')
                ->flatten()
                ->pluck('slug')
                ->unique()
                ->toArray();

            $itemSimilar = $this->recommendationService->itemSimilarRecommended($slugs, $limit);
            $contentBased = $this->recommendationService->contentBasedRecommended($slugs, $limit);
            $allRecommendations = array_unique(array_merge($itemSimilar, $contentBased));

            $slugs = array_diff($allRecommendations, $slugs);
        }

        $books = $this->book
            ->newQuery()
            ->with([
                'authors' => fn($query) => $query->select(['authors.id', 'first_name', 'last_name', 'slug'])->limit(1),
                'currentDeal'
            ])
            ->whereIn('slug', $slugs)
            ->select(['id', 'title', 'cover_image', 'price', 'slug'])
            ->withAvg('ratings as average_rating', 'rating')
            ->withSum('orders as total_sold', 'order_items.quantity')
            ->havingRaw('average_rating >= ? and total_sold >= ?', [2.5, 10])
            ->orderByRaw('total_sold desc, average_rating desc')
            ->limit($limit)
            ->get();

        return BookCardResource::collection($books);
    }

    public function related(string $slug, int $limit = 5): array
    {
        $userAlsoEnjoyed = $this->recommendationService->itemSimilarRecommended([$slug], $limit);

        $samesLikeThis = $this->recommendationService->contentBasedRecommended([$slug], $limit);

        $books = $this->book
            ->newQuery()
            ->with([
                'authors' => fn($query) => $query->select(['authors.id', 'first_name', 'last_name', 'slug'])->limit(1),
                'currentDeal'
            ])
            ->whereIn('slug', array_merge($userAlsoEnjoyed, $samesLikeThis))
            ->select(['id', 'title', 'cover_image', 'price', 'slug'])
            ->withAvg('ratings as average_rating', 'rating')
            ->get(['id', 'title', 'cover_image', 'price', 'slug']);

        return [
            'userAlsoEnjoyed' => BookCardResource::collection($books->whereIn('slug', $userAlsoEnjoyed)),
            'samesLikeThis' => BookCardResource::collection($books->whereIn('slug', $samesLikeThis)),
        ];
    }

    public function populars(int $limit = 10): AnonymousResourceCollection
    {
        $populars = Http::withOptions([
            'verify' => false,
        ])
            ->get(config('general_settings.site.recommendation.url') . '/popular', [
                'top_n' => $limit,
            ])
            ->json();

        $books = $this->book
            ->newQuery()
            ->with([
                'authors' => fn($query) => $query->select(['authors.id', 'first_name', 'last_name', 'slug'])->limit(1),
                'currentDeal'
            ])
            ->whereIn('slug', $populars)
            ->select(['id', 'title', 'cover_image', 'price', 'slug'])
            ->withAvg('ratings as average_rating', 'rating')
            ->get();

        return BookCardResource::collection($books);
    }

    public function topRated(int $limit = 10)
    {
        $books = $this->book
            ->newQuery()
            ->with([
                'authors' => fn($query) => $query->select(['authors.id', 'first_name', 'last_name', 'slug'])->limit(1),
                'currentDeal'
            ])
            ->select(['id', 'title', 'cover_image', 'price', 'slug'])
            ->withAvg('ratings as average_rating', 'rating')
            ->withCount('ratings as ratings_count')
            ->havingRaw('ratings_count >= ?', [50])
            ->orderByDesc('average_rating')
            ->limit($limit)
            ->get();

        return BookCardResource::collection($books);
    }

    public function bestSellers(string $unit = null)
    {
        return $this->book
            ->newQuery()
            ->with([
                'authors' => fn($query) => $query->select(['authors.id', 'first_name', 'last_name', 'slug'])->limit(1),
                'currentDeal'
            ])
            ->select(['books.id', 'title', 'cover_image', 'books.price', 'slug'])
            ->withAvg('ratings as average_rating', 'rating')
            ->bestSellerBy($unit)
            ->limit(10)
            ->get();
    }

    public function discounts(int $limit = 10)
    {
        return $this->book
            ->newQuery()
            ->with([
                'authors' => fn($query) => $query->select(['authors.id', 'first_name', 'last_name', 'slug'])->limit(1),
                'currentDeal'
            ])
            ->select(['id', 'title', 'cover_image', 'price', 'slug'])
            ->withAvg('ratings as average_rating', 'rating')
            ->discounts()
            ->limit($limit)
            ->get();
    }

    public function recently(int $limit = 10)
    {
        return $this->book
            ->newQuery()
            ->with([
                'authors' => fn($query) => $query->select(['authors.id', 'first_name', 'last_name', 'slug'])->limit(1),
                'currentDeal'
            ])
            ->select(['id', 'title', 'cover_image', 'price', 'slug'])
            ->withAvg('ratings as average_rating', 'rating')
            ->recently()
            ->limit($limit)
            ->get();
    }

    public function store(array $data)
    {
        $coverImagePath = Storage::put('uploads/books/' . $data['slug'], $data['cover_image']);
        $bookData = Arr::except($data, ['authors', 'genres', 'cost']);
        $bookData['cover_image'] = $coverImagePath;

        $costData = [
            'cost' => $data['cost'],
            'effective_date' => now()
        ];

        $book = $this->book->create($bookData);
        $book->costs()->create($costData);
        $book->authors()->attach($data['authors']);
        $book->genres()->attach($data['genres']);

        return $book;
    }

    public function update(string $slug, array $data): Book
    {
        $book = $this->findBySlug($slug);

        $bookData = Arr::except($data, ['authors', 'genres', 'cost']);

        if (isset($data['cover_image'])) {
            $slug = $data['slug'] ?? $book->slug;
            Storage::deleteDirectory('uploads/books/' . $book->slug);
            $coverImagePath = Storage::put('uploads/books/' . $slug, $data['cover_image']);
            $bookData['cover_image'] = $coverImagePath;
        }

        $book->updateOnly($bookData);

        if (isset($data['cost'])) {
            $book->costs()->create([
                'cost' => $data['cost'],
                'effective_date' => now()
            ]);
        }

        isset($data['authors']) && $book->authors()->sync($data['authors']);
        isset($data['genres']) && $book->genres()->sync($data['genres']);

        $book->save();

        return $book;
    }

    public function findBySlug(string $slug): Book
    {
        return $this->book->newQuery()->whereSlug($slug)->firstOrFail();
    }

    public function deleteBySlug(mixed $slugs): void
    {
        Gate::authorize('deleteMany', Book::class);
        $slugs = explode(',', $slugs);
        $this->book->whereIn('slug', $slugs)->delete();

        foreach ($slugs as $slug) {
            Storage::deleteDirectory('uploads/books/' . $slug);
        }
    }

    public function import(string $filePath, User $user): void
    {
        Bus::chain([
            new ImportJob(new BookImport(), $filePath),
            new NotifyUserOfCompletedImportJob($user, $filePath)
        ])->dispatch();
    }

    public function export(string $filePath, User $user): void
    {
        Bus::chain([
            new ExportJob(new BookExport($user->id), $filePath),
            new NotifyUserOfCompletedExportJob($user, $filePath)
        ])->dispatch();
    }

    public function storeReview(string $slug, User $user, array $data)
    {
        $book = $this->findBySlug($slug);
        $book->reviews()->create([
            ...$data,
            'user_id' => $user->id
        ]);
    }

    public function storeRating(string $slug, User $user, array $data)
    {
        $book = $this->findBySlug($slug);
        $book->ratings()->create([
            ...$data,
            'user_id' => $user->id
        ]);
    }

    public function show(string $slug)
    {
        $book = $this->book
            ->newQuery()
            ->withAvg('ratings as average_rating', 'rating')
            ->with([
                'authors' => fn($query) => $query->select(['authors.id', 'first_name', 'last_name', 'slug'])->limit(1),
                'genres' => fn($query) => $query->select(['genres.id', 'name', 'slug']),
                'publisher' => fn($query) => $query->select(['publishers.id', 'name', 'slug']),
                'currentDeal',
                'reviews' => fn($query) => $query
                    ->join('users', 'users.id', '=', 'reviews.user_id')
                    ->leftJoin('ratings', function ($join) {
                        $join->on('ratings.book_id', '=', 'reviews.book_id')
                            ->on('ratings.user_id', '=', 'users.id');
                    })
                    ->select(['reviews.book_id', 'review_text', 'users.username', 'reviews.created_at', 'ratings.rating'])
                    ->limit(5)
            ])
            ->withCount('reviews as reviews_count')
            ->whereSlug($slug)
            ->firstOrFail();

        return $book;
    }
}
