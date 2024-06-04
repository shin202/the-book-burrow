<?php

use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('my-profile')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('my-profile.index');
        Route::put('/avatar', [UserController::class, 'updateAvatar'])->name('my-profile.update-avatar');
        Route::delete('/avatar', [UserController::class, 'deleteAvatar'])->name('my-profile.delete-avatar');
        Route::patch('/personal-information', [UserController::class, 'updatePersonalInformation'])->name('my-profile.update-personal-information');

        Route::prefix('orders')->name('my-profile.orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])
                ->name('index');

            Route::get('/{orderNumber}', [OrderController::class, 'show'])
                ->name('show');
            Route::get('/{orderNumber}/invoice', [OrderController::class, 'invoice'])
                ->name('invoice');
        });
    });
});
