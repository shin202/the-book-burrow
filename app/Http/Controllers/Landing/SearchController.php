<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function liveSearch(Request $request, SearchService $searchService)
    {
        $query = $request->input('query');
        $searchField = $request->input('searchField', 'all');
        $books = $searchService->liveSearch($query, $searchField)
            ->map(function ($book) {
                $author = $book->authors->first();
                return [
                    'title' => $book->title,
                    'slug' => $book->slug,
                    'cover_image' => $book->cover_image,
                    'author' => [
                        'full_name' => $author->full_name,
                        'slug' => $author->slug,
                    ]
                ];
            });

        return response()->json($books);
    }

    public function index(Request $request, SearchService $searchService)
    {
        $query = $request->input('query');
        $searchField = $request->input('searchField', 'all');
        $perPage = $request->input('perPage', 10);

        $startTime = microtime(true);
        $books = $searchService->search($query, $searchField, $perPage);
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        return Inertia::render('Landing/Search/Index', [
            'books' => $books,
            'execution_time' => $executionTime,
        ]);
    }
}
