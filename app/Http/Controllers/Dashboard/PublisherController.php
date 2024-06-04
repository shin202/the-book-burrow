<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Publisher\ImportPublisherRequest;
use App\Http\Requests\Publisher\StorePublisherRequest;
use App\Http\Requests\Publisher\UpdatePublisherRequest;
use App\Http\Resources\Publisher\PublisherShowResource;
use App\Models\Publisher;
use App\Services\PublisherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PublisherController extends Controller
{
    public function index(Request $request, PublisherService $publisherService)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);
        $publishers = $publisherService->searchWithPagination($search, $perPage);

        return Inertia::render('Dashboard/Publisher/Index', [
            'publishers' => $publishers,
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Publisher/Create');
    }

    public function store(StorePublisherRequest $request, PublisherService $publisherService)
    {
        $publisherService->store($request->validated());

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Publisher created successfully',
            ]);
    }

    public function edit(string $slug, PublisherService $publisherService)
    {
        $publisher = $publisherService->findBySlug($slug);

        return Inertia::render('Dashboard/Publisher/Edit', [
            'publisher' => PublisherShowResource::make($publisher),
        ]);
    }

    public function update(string $slug, UpdatePublisherRequest $request, PublisherService $publisherService)
    {
        $publisher = $publisherService->update($slug, $request->validated());

        return redirect()->route('dashboard.publishers.edit', $publisher->slug)
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Publisher updated successfully',
            ]);
    }

    public function destroy(mixed $slugs, PublisherService $publisherService)
    {
        Gate::authorize('deleteMany', Publisher::class);

        $publisherService->deleteBySlug($slugs);

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Publishers deleted successfully',
            ]);
    }

    public function import(ImportPublisherRequest $request, PublisherService $publisherService)
    {
        $filePath = $request->fulfill();
        $publisherService->import($filePath, Auth::user());

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Please wait',
            ]);
    }

    public function export(PublisherService $publisherService)
    {
        $filePath = 'private/exports/publishers.xlsx';
        $publisherService->export($filePath, Auth::user());

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Please wait',
            ]);
    }
}
