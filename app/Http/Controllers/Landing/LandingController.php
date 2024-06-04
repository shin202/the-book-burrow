<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Http\Resources\Book\BookCardCollection;
use App\Http\Resources\Book\BookCardResource;
use App\Services\AuthorService;
use App\Services\BannerService;
use App\Services\BookService;
use App\Services\GenreService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function index(Request $request, BannerService $bannerService, BookService $bookService)
    {
        $banners = $bannerService->allActiveBanners();
        $popularBooks = $bookService->populars();
        $topRatedBooks = $bookService->topRated();
        $top10ThisWeek = $bookService->bestSellers('week')
            ->map(fn($book) => [
                'title' => $book->title,
                'slug' => $book->slug,
                'cover_image' => $book->cover_image,
                'author' => $book->authors->first()->full_name,
            ]);
        $discountBooks = $bookService->discounts();
        $bestSellerBooks = $bookService->bestSellers();
        $recentlyAddedBooks = $bookService->recently();
        $recommendedBooks = $bookService->recommended($request->user());

        return Inertia::render('Landing/Index', [
            'banners' => $banners,
            'popularBooks' => $popularBooks,
            'topRatedBooks' => $topRatedBooks,
            'top10ThisWeek' => $top10ThisWeek,
            'bestSellerBooks' => BookCardResource::collection($bestSellerBooks),
            'discountBooks' => BookCardResource::collection($discountBooks),
            'recentlyAddedBooks' => BookCardResource::collection($recentlyAddedBooks),
            'dealsOfWeek' => BookCardResource::collection($discountBooks->random(5)),
            'recommendedBooks' => BookCardResource::collection($recommendedBooks),
        ]);
    }

    public function shop(Request $request, BookService $bookService, GenreService $genreService, AuthorService $authorService)
    {
        $perPage = $request->get('perPage', 10);
        $sort = $request->get('sort', 'title');

        $books = $bookService->landingList($perPage);
        $genres = $genreService->list(20);
        $authors = $authorService->list(20);

        return Inertia::render('Landing/Shop/Index', [
            'books' => BookCardCollection::make($books),
            'genres' => $genres,
            'authors' => $authors,
        ]);
    }
}
