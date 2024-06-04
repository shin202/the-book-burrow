<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Author\ImportAuthorRequest;
use App\Http\Requests\Author\StoreAuthorRequest;
use App\Http\Requests\Author\UpdateAuthorRequest;
use App\Http\Resources\Author\AuthorCollection;
use App\Http\Resources\Author\AuthorShowResource;
use App\Models\Author;
use App\Services\AuthorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AuthorController extends Controller
{
    public function index(Request $request, AuthorService $authorService)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);
        $authors = $authorService->dashboardIndex($search, $perPage);

        return Inertia::render('Dashboard/Author/Index', [
            'authors' => AuthorCollection::make($authors)
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Author/Creupdate', [
            'formType' => 'create'
        ]);
    }

    public function edit(string $slug, AuthorService $authorService)
    {
        $author = $authorService->findBySlug($slug);
        return Inertia::render('Dashboard/Author/Creupdate', [
            'formType' => 'update',
            'author' => AuthorShowResource::make($author)
        ]);
    }

    public function update(string $slug, UpdateAuthorRequest $request, AuthorService $authorService)
    {
        $author = $authorService->update($slug, $request->validated());
        return redirect()->route('dashboard.authors.edit', $author->slug);
    }

    public function destroy(mixed $slugs, AuthorService $authorService)
    {
        Gate::authorize('deleteMany', Author::class);

        $authorService->deleteBySlug($slugs);
        return redirect()->back();
    }

    public function export(AuthorService $authorService)
    {
        $authorService->export();

        return redirect()->back();
    }

    public function import(ImportAuthorRequest $request, AuthorService $authorService)
    {
        $filePath = $request->file('file')->store('private/imports');
        $authorService->import($filePath, $request->user());

        return redirect()->back();
    }

    public function store(StoreAuthorRequest $request, AuthorService $authorService)
    {
        $authorService->store($request->validated());

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Author created successfully'
            ]);
    }
}
