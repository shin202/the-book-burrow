<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Genre\ImportGenreRequest;
use App\Http\Requests\Genre\StoreGenreRequest;
use App\Http\Requests\Genre\UpdateGenreRequest;
use App\Http\Resources\Genre\GenreShowResource;
use App\Models\Genre;
use App\Services\GenreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class GenreController extends Controller
{
    public function index(Request $request, GenreService $genreService)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);
        $genres = $genreService->searchWithPagination($search, $perPage);

        return Inertia::render('Dashboard/Genre/Index', [
            'genres' => $genres,
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Genre/Create');
    }

    public function edit(string $slug, GenreService $genreService)
    {
        $genre = $genreService->findBySlug($slug);

        return Inertia::render('Dashboard/Genre/Edit', [
            'genre' => GenreShowResource::make($genre),
        ]);
    }

    public function update(string $slug, UpdateGenreRequest $request, GenreService $genreService)
    {
        $genre = $genreService->update($slug, $request->validated());

        return redirect()->route('dashboard.genres.edit', $genre->slug)
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Genre updated successfully',
            ]);
    }

    public function destroy(mixed $slugs, GenreService $genreService)
    {
        Gate::authorize('deleteMany', Genre::class);

        $genreService->deleteBySlug($slugs);
        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Genre deleted successfully',
            ]);
    }

    public function import(ImportGenreRequest $request, GenreService $genreService)
    {
        $filePath = $request->file('file')->store('private/imports');
        $genreService->import($filePath, Auth::user());

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Genres import has started. You will be notified once it is completed',
            ]);

    }

    public function store(StoreGenreRequest $request, GenreService $genreService)
    {
        $genreService->store($request->validated());
        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Genre created successfully',
            ]);
    }

    public function export(GenreService $genreService)
    {
        $filePath = 'private/exports/genres.xlsx';
        $genreService->export($filePath, Auth::user());

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Genres export has started. You will be notified once it is completed',
            ]);
    }
}
