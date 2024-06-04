<?php

namespace App\Services;

use App\Exports\GenreExport;
use App\Http\Resources\Genre\GenreCollection;
use App\Http\Resources\Genre\GenreListResource;
use App\Imports\Genre\GenreImport;
use App\Jobs\ExportJob;
use App\Jobs\ImportJob;
use App\Jobs\NotifyUserOfCompletedExportJob;
use App\Jobs\NotifyUserOfCompletedImportJob;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Bus;
use Spatie\QueryBuilder\QueryBuilder;

class GenreService
{

    public function __construct(protected Genre $genre)
    {
    }

    public function list(int $limit = null): AnonymousResourceCollection
    {
        $genres = QueryBuilder::for(Genre::class)
            ->defaultSort('name')
            ->limit($limit)
            ->get(['id', 'name', 'slug']);

        return GenreListResource::collection($genres);
    }

    public function searchWithPagination(string $search = null, int $perPage = 5)
    {
        $genres = $this->genre->search($search)->paginate($perPage);
        return GenreCollection::make($genres);
    }

    public function store(array $data)
    {
        return $this->genre->create($data);
    }

    public function update(string $slug, array $data)
    {
        $genre = $this->findBySlug($slug);

        !empty($data['name']) && $genre->name !== $data['name'] && $genre->name = $data['name'];
        !empty($data['description']) && $genre->description !== $data['description'] && $genre->description = $data['description'];
        !empty($data['slug']) && $genre->slug !== $data['slug'] && $genre->slug = $data['slug'];

        $genre->save();

        return $genre;
    }

    public function findBySlug(string $slug)
    {
        return $this->genre->whereSlug($slug)->firstOrFail();
    }

    public function deleteBySlug(mixed $slugs): void
    {
        $slugs = explode(',', $slugs);
        $this->genre->whereIn('slug', $slugs)->delete();
    }

    public function import(string $filePath, User $user): void
    {
        Bus::chain([
            new ImportJob(new GenreImport(), $filePath),
            new NotifyUserOfCompletedImportJob($user, $filePath)
        ])->dispatch();
    }

    public function export(string $filePath, User $user): void
    {
        Bus::chain([
            new ExportJob(new GenreExport($user->id), $filePath),
            new NotifyUserOfCompletedExportJob($user, $filePath)
        ])->dispatch();
    }
}
