<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreRatingRequest;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Resources\Book\BookShowResource;
use App\Services\BookService;
use Inertia\Inertia;

class BookController extends Controller
{
    public function index()
    {

    }

    public function show(string $slug, BookService $bookService)
    {
        $book = $bookService->show($slug);
        $relatedBooks = $bookService->related($slug);

        return Inertia::render('Landing/Book/Show', [
            'book' => BookShowResource::make($book),
            'relatedBooks' => $relatedBooks,
        ]);
    }

    public function storeReview(string $slug, StoreReviewRequest $request, BookService $bookService)
    {
        $bookService->storeReview($slug, $request->user(), $request->validated());

        return redirect()->back();
    }

    public function storeRating(string $slug, StoreRatingRequest $request, BookService $bookService)
    {
        $bookService->storeRating($slug, $request->user(), $request->validated());

        return redirect()->back();
    }
}
