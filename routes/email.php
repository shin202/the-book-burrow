<?php

use App\Http\Controllers\Email\EmailVerificationController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::domain(Config::get('app.url'))->middleware('auth')->group(function () {
    Route::prefix('email')->name('verification.')->group(function () {
        Route::get('/verify', [EmailVerificationController::class, 'notice'])->name('notice');
        Route::get('/verify/{token}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verify');
        Route::post('/verification-notification', [EmailVerificationController::class, 'resend'])->name('resend');
    });
});
