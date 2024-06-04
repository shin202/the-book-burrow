<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\ImportBookRequest;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\Book\BookCollection;
use App\Http\Resources\Book\BookUpdateResource;
use App\Models\Book;
use App\Services\AuthorService;
use App\Services\BookService;
use App\Services\GenreService;
use App\Services\PublisherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class BookController extends Controller
{
    /**
     * Render the book index page.
     *
     * @param Request $request
     * @param BookService $bookService
     * @return Response
     */
    public function index(Request $request, BookService $bookService)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);
        $books = BookCollection::make($bookService->dashboardList($search, $perPage));

        return Inertia::render('Dashboard/Book/Index', [
            'books' => $books,
        ]);
    }

    /**
     * Render the book create page.
     *
     * @param AuthorService $authorService
     * @param GenreService $genreService
     * @param PublisherService $publisherService
     * @return Response
     */
    public function create(
        AuthorService    $authorService,
        GenreService     $genreService,
        PublisherService $publisherService,
    )
    {
        $authors = $authorService->list();
        $genres = $genreService->list();
        $publishers = $publisherService->list();

        return Inertia::render('Dashboard/Book/Create', [
            'authors' => $authors,
            'genres' => $genres,
            'publishers' => $publishers,
        ]);
    }

    public function edit(
        string           $slug,
        BookService      $bookService,
        AuthorService    $authorService,
        PublisherService $publisherService,
        GenreService     $genreService,
    )
    {
        $book = $bookService->findBySlug($slug);
        $book->load(['authors', 'genres', 'publisher', 'latestCost']);
        $authors = $authorService->list();
        $genres = $genreService->list();
        $publishers = $publisherService->list();

        return Inertia::render('Dashboard/Book/Edit', [
            'book' => BookUpdateResource::make($book),
            'authors' => $authors,
            'genres' => $genres,
            'publishers' => $publishers,
        ]);
    }

    public function update(string $slug, UpdateBookRequest $request, BookService $bookService)
    {
        $book = $bookService->update($slug, $request->validated());

        return redirect()->route('dashboard.books.edit', $book->slug)
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Book updated successfully.'
            ]);
    }

    /**
     * Delete list of books with the given slugs.
     *
     * @param mixed $slugs
     * @param BookService $bookService
     * @return RedirectResponse
     */
    public function destroy(mixed $slugs, BookService $bookService)
    {
        Gate::authorize('deleteMany', Book::class);

        $bookService->deleteBySlug($slugs);

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Books deleted successfully.'
            ]);
    }

    public function import(ImportBookRequest $request, BookService $bookService)
    {
        $filePath = $request->file('file')->store('private/imports');
        $bookService->import($filePath, $request->user());

        return redirect()->back();
    }

    /**
     * Store a new book.
     *
     * @param StoreBookRequest $request
     * @param BookService $bookService
     * @return RedirectResponse
     */
    public function store(StoreBookRequest $request, BookService $bookService)
    {
        $bookService->store($request->validated());

        return redirect()->back();
    }

    public function export(BookService $bookService)
    {
        $filePath = 'private/exports/books.xlsx';
        $bookService->export($filePath, Auth::user());

        return redirect()->back();
    }
}
