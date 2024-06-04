<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Http\Resources\Author\AuthorCollection;
use App\Services\AuthorService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthorController extends Controller
{
    public function index(Request $request, AuthorService $authorService)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);
        $authors = $authorService->landingIndex($search, $perPage);

        return Inertia::render('Landing/Author/Index', [
            'authors' => AuthorCollection::make($authors)
        ]);
    }

    public function show(string $slug, Request $request, AuthorService $authorService)
    {
        $perPage = $request->input('perPage', 10);
        $authorWithBooks = $authorService->show($slug, $perPage);

        return Inertia::render('Landing/Author/Show', [
            ...$authorWithBooks,
        ]);
    }
}
