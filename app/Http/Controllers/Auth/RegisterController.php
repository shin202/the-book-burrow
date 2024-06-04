<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\RegisterService;
use Inertia\Inertia;

class RegisterController extends Controller
{

    public function index()
    {
        return Inertia::render('Auth/Register');
    }

    public function store(RegisterRequest $request, RegisterService $registerService)
    {
        $registerService->register($request->validated());

        return redirect()->route('verification.notice');
    }
}
