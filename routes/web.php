<?php

use App\Http\Controllers\Landing\AuthorController;
use App\Http\Controllers\Landing\BookController;
use App\Http\Controllers\Landing\CartController;
use App\Http\Controllers\Landing\CheckoutController;
use App\Http\Controllers\Landing\LandingController;
use App\Http\Controllers\Landing\OrderController;
use App\Http\Controllers\Landing\SearchController;
use App\Http\Middleware\CanCheckoutMiddleware;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;


require __DIR__ . '/auth.php';
require __DIR__ . '/email.php';
require __DIR__ . '/user.php';
require __DIR__ . '/dashboard.php';

Route::domain(Config::get('app.url'))->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('landing.index');

    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/{slug}', [BookController::class, 'show'])->name('show');
        Route::post('/{slug}/reviews', [BookController::class, 'storeReview'])
            ->middleware('auth')
            ->name('reviews.store');
        Route::post('/{slug}/ratings', [BookController::class, 'storeRating'])
            ->middleware('auth')
            ->name('ratings.store');
    });

    Route::prefix('cart')->name('cart.')->group(function () {
        Route::post('/coupons', [CartController::class, 'applyCoupon'])->name('coupons.store');
        Route::delete('/coupons', [CartController::class, 'destroyCoupon'])->name('coupons.destroy');

        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/', [CartController::class, 'store'])->name('store');
        Route::put('/{id}', [CartController::class, 'update'])->name('update');
        Route::delete('/{id}', [CartController::class, 'destroy'])->name('destroy');
        Route::delete('/', [CartController::class, 'clear'])->name('clear');
    });

    Route::prefix('checkout')->name('checkout.')->middleware('auth')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->middleware(CanCheckoutMiddleware::class)->name('index');
        Route::post('/', [CheckoutController::class, 'process'])->name('process');
        Route::get('/success', [CheckoutController::class, 'success'])->name('success');
    });

    Route::prefix('order')->name('order.')->middleware('auth')->group(function () {
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/success', [OrderController::class, 'success'])->name('success');
    });

    Route::prefix('shop')->name('shop.')->group(function () {
        Route::get('/', [LandingController::class, 'shop'])->name('index');
    });

    Route::prefix('authors')->name('authors.')->group(function () {
        Route::get('/', [AuthorController::class, 'index'])->name('index');
        Route::get('/{slug}', [AuthorController::class, 'show'])->name('show');
    });

    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/', [SearchController::class, 'index'])->name('index');
        Route::get('/live', [SearchController::class, 'liveSearch'])->name('live');
    });

    Route::prefix('contact')->name('contact.')->group(function () {
        Route::get('/', function () {
            echo "HELLLLLLLLLLLLLO";
        })->name('index');
    });

    Route::stripeWebhooks('stripe/webhook');
});


