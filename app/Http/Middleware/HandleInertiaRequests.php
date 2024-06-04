<?php

namespace App\Http\Middleware;

use App\Http\Resources\Cart\CartResource;
use App\Http\Resources\User\UserShowResource;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $user?->load([
            'roles' => fn($query) => $query->select('key', 'value', 'roles.id'),
        ]);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? UserShowResource::make($user) : null,
            ],
            'toast' => [
                'message' => session('toast.message'),
                'severity' => session('toast.severity'),
            ],
            'isAuthenticated' => Route::has('login') && auth()->check(),
            'cart' => $user?->isAdministrator() ? null : CartResource::make(CartService::instance()),
        ];
    }
}
