<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustConfirmPasswordMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->isValidPasswordConfirmedAt($request)) {
            $request->session()->forget('auth.password_confirmed_at');
            $this->setIntendedUrl($request);
            return redirect()->back()->withErrors([
                'status' => 403,
                'message' => 'Please confirm your password to continue.',
            ]);
        }

        return $next($request);
    }

    private function isValidPasswordConfirmedAt(Request $request): bool
    {
        $passwordConfirmedAt = $request->session()->get('auth.password_confirmed_at');

        return isset($passwordConfirmedAt) &&
            now()->diffInSeconds(Carbon::createFromTimestamp($passwordConfirmedAt)) < config('auth.password_timeout');
    }

    private function setIntendedUrl(Request $request): void
    {
        $intended = $request->method() === 'GET' ? $request->fullUrl() : url()->previous();
        session()->put('url.intended', $intended);
    }
}
