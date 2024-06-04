<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Middleware\GuestAdminMiddleware;
use App\Http\Middleware\MustBeAdminMiddleware;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::domain(Config::get('app.url'))->group(function () {
    Route::middleware('guest')->group(function () {
        Route::prefix('login')->name('login')->group(function () {
            Route::get('/', [LoginController::class, 'index']);
            Route::post('/', [LoginController::class, 'authenticated']);
        });

        Route::prefix('register')->group(function () {
            Route::get('/', [RegisterController::class, 'index'])->name('register.index');
            Route::post('/', [RegisterController::class, 'store'])->name('register.store');
        });

        Route::get('/forgot-password', [ForgotPasswordController::class, 'index'])->name('password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'forgot'])->name('password.email');
        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'index'])->name('password.reset');
        Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', LogoutController::class)->name('logout');
    });
});

Route::domain('dashboard.' . Config::get('app.url'))->group(function () {
    Route::prefix('login')->middleware(GuestAdminMiddleware::class)->group(function () {
        Route::get('/', [AdminLoginController::class, 'index'])->name('dashboard.login');
        Route::post('/', [AdminLoginController::class, 'authenticate'])->name('dashboard.login');
    });
    
    Route::middleware(MustBeAdminMiddleware::class)->group(function () {
        Route::post('/logout', LogoutController::class)->name('dashboard.logout');
    });
});
