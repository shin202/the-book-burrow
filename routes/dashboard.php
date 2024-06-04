<?php

use App\Http\Controllers\Auth\PasswordConfirmationController;
use App\Http\Controllers\Dashboard\AuthorController;
use App\Http\Controllers\Dashboard\BannerController;
use App\Http\Controllers\Dashboard\BookController;
use App\Http\Controllers\Dashboard\CouponController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\DealController;
use App\Http\Controllers\Dashboard\GenreController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\PublisherController;
use App\Http\Controllers\Dashboard\RecommendationController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Middleware\MustBeAdminMiddleware;
use App\Http\Middleware\MustConfirmPasswordMiddleware;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::domain('dashboard.' . Config::get('app.url'))->group(function () {
    Route::middleware(MustBeAdminMiddleware::class)->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/download', [DashboardController::class, 'download'])->middleware('signed')->name('dashboard.download');

        Route::prefix('books')->group(function () {
            Route::get('/', [BookController::class, 'index'])->name('dashboard.books.index');
            Route::get('/create', [BookController::class, 'create'])->name('dashboard.books.create');
            Route::post('/', [BookController::class, 'store'])->name('dashboard.books.store');
            Route::get('/{slug}/edit', [BookController::class, 'edit'])->name('dashboard.books.edit');
            Route::patch('/{slug}', [BookController::class, 'update'])->name('dashboard.books.update');
            Route::delete('/{slugs}', [BookController::class, 'destroy'])->name('dashboard.books.destroy');

            Route::post('/import', [BookController::class, 'import'])->name('dashboard.books.import');
            Route::get('/export', [BookController::class, 'export'])->name('dashboard.books.export');
        });

        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('dashboard.notifications.index');
            Route::get('/read-all', [NotificationController::class, 'markAllAsRead'])->name('dashboard.notifications.read-all');
            Route::get('/{id}', [NotificationController::class, 'show'])->name('dashboard.notifications.show');
        });

        Route::prefix('authors')->group(function () {
            Route::get('/', [AuthorController::class, 'index'])->name('dashboard.authors.index');
            Route::get('/create', [AuthorController::class, 'create'])->name('dashboard.authors.create');
            Route::post('/', [AuthorController::class, 'store'])->name('dashboard.authors.store');
            Route::get('/{slug}/edit', [AuthorController::class, 'edit'])->name('dashboard.authors.edit');
            Route::patch('/{slug}', [AuthorController::class, 'update'])->name('dashboard.authors.update');
            Route::delete('/{slugs}', [AuthorController::class, 'destroy'])->name('dashboard.authors.destroy');

            Route::post('/import', [AuthorController::class, 'import'])->name('dashboard.authors.import');
            Route::get('/export', [AuthorController::class, 'export'])->name('dashboard.authors.export');
        });

        Route::prefix('publishers')->group(function () {
            Route::get('/', [PublisherController::class, 'index'])->name('dashboard.publishers.index');
            Route::get('/create', [PublisherController::class, 'create'])->name('dashboard.publishers.create');
            Route::post('/', [PublisherController::class, 'store'])->name('dashboard.publishers.store');
            Route::get('/{slug}/edit', [PublisherController::class, 'edit'])->name('dashboard.publishers.edit');
            Route::patch('/{slug}', [PublisherController::class, 'update'])->name('dashboard.publishers.update');
            Route::delete('/{slugs}', [PublisherController::class, 'destroy'])->name('dashboard.publishers.destroy');

            Route::post('/import', [PublisherController::class, 'import'])->name('dashboard.publishers.import');
            Route::get('/export', [PublisherController::class, 'export'])->name('dashboard.publishers.export');
        });

        Route::prefix('genres')->group(function () {
            Route::get('/', [GenreController::class, 'index'])->name('dashboard.genres.index');
            Route::get('/create', [GenreController::class, 'create'])->name('dashboard.genres.create');
            Route::post('/', [GenreController::class, 'store'])->name('dashboard.genres.store');
            Route::get('/{slug}/edit', [GenreController::class, 'edit'])->name('dashboard.genres.edit');
            Route::patch('/{slug}', [GenreController::class, 'update'])->name('dashboard.genres.update');
            Route::delete('/{slugs}', [GenreController::class, 'destroy'])->name('dashboard.genres.destroy');

            Route::post('/import', [GenreController::class, 'import'])->name('dashboard.genres.import');
            Route::get('/export', [GenreController::class, 'export'])->name('dashboard.genres.export');
        });

        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('dashboard.users.index');
            Route::get('/create', [UserController::class, 'create'])->name('dashboard.users.create');
            Route::post('/', [UserController::class, 'store'])->name('dashboard.users.store');
            Route::get('/{username}/edit', [UserController::class, 'edit'])->name('dashboard.users.edit');
            Route::patch('/{username}', [UserController::class, 'update'])->middleware(MustConfirmPasswordMiddleware::class)->name('dashboard.users.update');
            Route::delete('/{usernames}', [UserController::class, 'destroy'])->middleware(MustConfirmPasswordMiddleware::class)->name('dashboard.users.destroy');

            Route::get('/export', [UserController::class, 'export'])->name('dashboard.users.export');
        });

        Route::prefix('coupons')->group(function () {
            Route::get('/', [CouponController::class, 'index'])->name('dashboard.coupons.index');
            Route::get('/create', [CouponController::class, 'create'])->name('dashboard.coupons.create');
            Route::post('/', [CouponController::class, 'store'])->name('dashboard.coupons.store');
            Route::get('/{code}/edit', [CouponController::class, 'edit'])->name('dashboard.coupons.edit');
            Route::patch('/{code}', [CouponController::class, 'update'])->name('dashboard.coupons.update');
            Route::delete('/{codes}', [CouponController::class, 'destroy'])->name('dashboard.coupons.destroy');

            Route::post('/import', [CouponController::class, 'import'])->name('dashboard.coupons.import');
            Route::get('/export', [CouponController::class, 'export'])->name('dashboard.coupons.export');
        });

        Route::prefix('banners')->group(function () {
            Route::get('/', [BannerController::class, 'index'])->name('dashboard.banners.index');
            Route::get('/create', [BannerController::class, 'create'])->name('dashboard.banners.create');
            Route::post('/', [BannerController::class, 'store'])->name('dashboard.banners.store');
            Route::get('/{id}/edit', [BannerController::class, 'edit'])->name('dashboard.banners.edit');
            Route::patch('/{id}', [BannerController::class, 'update'])->name('dashboard.banners.update');
            Route::delete('/{ids}', [BannerController::class, 'destroy'])->name('dashboard.banners.destroy');
        });

        Route::prefix('deals')->name('dashboard.deals.')->group(function () {
            Route::get('/', [DealController::class, 'index'])->name('index');
            Route::get('/create', [DealController::class, 'create'])->name('create');
            Route::post('/', [DealController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [DealController::class, 'edit'])->name('edit');
            Route::patch('/{id}', [DealController::class, 'update'])->name('update');
            Route::delete('/{ids}', [DealController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('orders')->name('dashboard.orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{orderNumber}', [OrderController::class, 'show'])->name('show');
            Route::patch('/{orderNumber}/statuses', [OrderController::class, 'updateStatus'])->name('update');
            Route::delete('/{orderNumbers}', [OrderController::class, 'destroy'])->name('destroy');
        });

        Route::post('/confirm-password', [PasswordConfirmationController::class, 'confirm'])->name('dashboard.password.confirm');

        Route::prefix('settings')->name('dashboard.settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::prefix('general')->name('general.')->group(function () {
                Route::patch('/{key}', [SettingController::class, 'updateGeneral'])->name('update');
            });
        });

        Route::prefix('recommendation')->name('dashboard.recommendation.')->group(function () {
            Route::prefix('train')->name('train.')->group(function () {
                Route::post('/item-based', [RecommendationController::class, 'itemBasedTrain'])->name('item-based');
                Route::post('/item-content', [RecommendationController::class, 'itemContentTrain'])->name('item-content');
                Route::get('/datasets', [RecommendationController::class, 'datasets'])->name('datasets');
            });
        });
    });
});
