<?php

namespace App\Services;

use App\Exports\PublisherExport;
use App\Http\Resources\Publisher\PublisherCollection;
use App\Http\Resources\Publisher\PublisherListResource;
use App\Imports\Publisher\PublisherImport;
use App\Jobs\ExportJob;
use App\Jobs\ImportJob;
use App\Jobs\NotifyUserOfCompletedExportJob;
use App\Jobs\NotifyUserOfCompletedImportJob;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Bus;

class PublisherService
{

    public function __construct(protected Publisher $publisher)
    {
    }

    public function list(): AnonymousResourceCollection
    {
        return PublisherListResource::collection($this->publisher->all());
    }

    public function searchWithPagination(string $search = null, int $perPage = 5)
    {
        $publishers = $this->publisher->search($search)->paginate($perPage);
        return PublisherCollection::make($publishers);
    }

    public function store(array $data): Publisher
    {
        $contactInformation = [
            'email' => $data['email'],
            'phone' => $data['phone'],
            'website' => $data['website'],
        ];

        $data = [
            'name' => $data['name'],
            'description' => $data['description'],
            'contact_information' => $contactInformation,
            'slug' => $data['slug'],
        ];

        return $this->publisher->create($data);
    }

    public function update(string $slug, array $data)
    {
        $publisher = $this->findBySlug($slug);

        !empty($data['name']) && $publisher->name !== $data['name'] && $publisher->name = $data['name'];
        !empty($data['description']) && $publisher->description !== $data['description'] && $publisher->description = $data['description'];
        !empty($data['slug']) && $publisher->slug !== $data['slug'] && $publisher->slug = $data['slug'];
        !empty($data['email']) && $publisher->contact_information['email'] !== $data['email'] && $publisher->contact_information = [...$publisher->contact_information, 'email' => $data['email']];
        !empty($data['phone']) && $publisher->contact_information['phone'] !== $data['phone'] && $publisher->contact_information = [...$publisher->contact_information, 'phone' => $data['phone']];
        !empty($data['website']) && $publisher->contact_information['website'] !== $data['website'] && $publisher->contact_information = [...$publisher->contact_information, 'website' => $data['website']];

        $publisher->save();

        return $publisher;
    }

    public function findBySlug(string $slug)
    {
        return $this->publisher->whereSlug($slug)->firstOrFail();
    }

    public function deleteBySlug(mixed $slugs): void
    {
        $slugs = explode(',', $slugs);
        $this->publisher->whereIn('slug', $slugs)->delete();
    }

    public function import(string $filePath, User $user): string
    {
        $batch = Bus::batch([
            new ImportJob(new PublisherImport(), $filePath),
            new NotifyUserOfCompletedImportJob($user, $filePath)
        ])->name('Publishers Import')->dispatch();

        return $batch->id;
    }

    public function export(string $filePath, User $user): string
    {
        $batch = Bus::batch([
            new ExportJob(new PublisherExport(), $filePath),
            new NotifyUserOfCompletedExportJob($user, $filePath)
        ])->name('Publishers Export')->dispatch();

        return $batch->id;
    }
}
