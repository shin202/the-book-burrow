<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Http\Resources\Author\AuthorListResource;
use App\Http\Resources\Author\AuthorShowResource;
use App\Http\Resources\Book\BookCardCollection;
use App\Imports\Author\AuthorImport;
use App\Jobs\AuthorExportJob;
use App\Jobs\ImportJob;
use App\Jobs\NotifyUserOfCompletedExportJob;
use App\Jobs\NotifyUserOfCompletedImportJob;
use App\Models\Author;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AuthorService
{
    public function __construct(protected Author $author)
    {
    }

    public function list(int $limit = null): AnonymousResourceCollection
    {
        $authors = QueryBuilder::for(Author::class)
            ->defaultSort('first_name')
            ->limit($limit)
            ->get(['id', 'first_name', 'last_name', 'slug']);

        return AuthorListResource::collection($authors);
    }

    public function dashboardIndex(string $search = null, int $perPage = 5)
    {
        return QueryBuilder::for(Author::class)
            ->withCount('books')
            ->allowedFilters([
                AllowedFilter::scope('name', 'whereFirstNameOrLastNameStartsWith')
            ])
            ->defaultSort('first_name')
            ->whereFirstNameOrLastNameStartsWith($search)
            ->paginate($perPage);
    }

    public function landingIndex(string $search = null, int $perPage = 5)
    {
        return QueryBuilder::for(Author::class)
            ->defaultSorts('first_name')
            ->whereFirstNameOrLastNameStartsWith($search)
            ->select(['first_name', 'last_name', 'slug', 'avatar'])
            ->paginate($perPage);
    }

    public function show(string $slug, int $perPage = 10)
    {
        $author = $this->author
            ->newQuery()
            ->whereSlug($slug)
            ->firstOrFail();

        $books = $author->books()->with([
            'currentDeal',
            'authors' => fn($query) => $query->select(['authors.id', 'first_name', 'last_name', 'authors.slug'])->limit(1),
        ])
            ->select(['books.id', 'title', 'books.slug', 'cover_image', 'price'])
            ->withAvg('ratings as average_rating', 'rating')
            ->addSelect([
                'total_sold' => fn($query) => $query->from('order_items')
                    ->whereColumn('order_items.book_id', 'books.id')
                    ->selectRaw('sum(quantity)')
                    ->join('order_statuses', 'order_items.order_id', '=', 'order_statuses.order_id')
                    ->where('order_statuses.status', OrderStatusEnum::DELIVERED)
            ])
            ->orderByRaw('total_sold desc, average_rating desc, title asc')
            ->paginate($perPage);

        return [
            'author' => AuthorShowResource::make($author),
            'books' => BookCardCollection::make($books)
        ];
    }

    public function store(array $data)
    {
        if (!empty($data['avatar'])) {
            $data['avatar'] = Storage::put('uploads/authors/' . $data['slug'], $data['avatar']);
        }

        $author = $this->author->create($data);

        return $author;
    }

    public function update(string $slug, array $data): Author
    {
        $author = $this->findBySlug($slug);

        if (!empty($data['avatar'])) {
            $slug = $data['slug'] ?? $author->slug;
            Storage::deleteDirectory('uploads/authors/' . $author->slug);
            $data['avatar'] = Storage::put('uploads/authors/' . $slug, $data['avatar']);
            $author->avatar = $data['avatar'];
        }

        !empty($data['first_name']) && $author->first_name !== $data['first_name'] && $author->first_name = $data['first_name'];
        !empty($data['last_name']) && $author->last_name !== $data['last_name'] && $author->last_name = $data['last_name'];
        !empty($data['slug']) && $author->slug !== $data['slug'] && $author->slug = $data['slug'];
        !empty($data['biography']) && $author->biography !== $data['biography'] && $author->biography = $data['biography'];

        $author->save();

        return $author;
    }

    public function findBySlug(string $slug): Author
    {
        return $this->author->where('slug', $slug)->firstOrFail();
    }

    public function deleteBySlug(mixed $slugs): void
    {
        $slugs = explode(',', $slugs);
        $this->author->whereIn('slug', $slugs)->delete();

        foreach ($slugs as $slug) {
            Storage::deleteDirectory('uploads/authors/' . $slug);
        }
    }

    public function export(): string
    {
        $filePath = 'private/exports/authors.xlsx';

        $batch = Bus::batch([
            new AuthorExportJob($filePath),
            new NotifyUserOfCompletedExportJob(Auth::user(), $filePath)
        ])->name('Authors Export')->dispatch();

        return $batch->id;
    }

    public function import(string $filePath, User $user): void
    {
        Bus::chain([
            new ImportJob(new AuthorImport(), $filePath),
            new NotifyUserOfCompletedImportJob($user, $filePath)
        ])->dispatch();
    }
}
