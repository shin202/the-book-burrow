<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coupon\ImportCouponRequest;
use App\Http\Requests\Coupon\StoreCouponRequest;
use App\Http\Requests\Coupon\UpdateCouponRequest;
use App\Http\Resources\Coupon\CouponCollection;
use App\Http\Resources\Coupon\CouponShowResource;
use App\Services\AuthorService;
use App\Services\BookService;
use App\Services\CouponService;
use App\Services\GenreService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, CouponService $couponService)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);
        $coupons = $couponService->dashboardIndex($search, $perPage);

        return Inertia::render('Dashboard/Coupon/Index', [
            'coupons' => CouponCollection::make($coupons),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(
        UserService   $userService,
        BookService   $bookService,
        AuthorService $authorService,
        GenreService  $genreService
    )
    {
        $users = $userService->list();
        $books = $bookService->list();
        $authors = $authorService->list();
        $genres = $genreService->list();

        return Inertia::render('Dashboard/Coupon/Creupdate', [
            'formType' => 'create',
            'users' => $users,
            'books' => $books,
            'authors' => $authors,
            'genres' => $genres,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(
        string        $code,
        CouponService $couponService,
        UserService   $userService,
        BookService   $bookService,
        AuthorService $authorService,
        GenreService  $genreService
    )
    {
        $coupon = $couponService->findByCode($code);
        $users = $userService->list();
        $books = $bookService->list();
        $authors = $authorService->list();
        $genres = $genreService->list();

        return Inertia::render('Dashboard/Coupon/Creupdate', [
            'formType' => 'update',
            'coupon' => CouponShowResource::make($coupon),
            'users' => $users,
            'books' => $books,
            'authors' => $authors,
            'genres' => $genres,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCouponRequest $request, string $code, CouponService $couponService)
    {
        $coupon = $couponService->update($code, $request->validated());

        return redirect()->route("dashboard.coupons.edit", $coupon->code)
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Coupon updated successfully'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(mixed $codes, CouponService $couponService)
    {
        $couponService->deleteByCode($codes);

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Coupon deleted successfully'
            ]);
    }

    public function import(ImportCouponRequest $request, CouponService $couponService)
    {
        $filePath = $request->file('file')->store('private/imports');
        $couponService->import($filePath, $request->user());

        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCouponRequest $request, CouponService $couponService)
    {
        $couponService->store($request->validated());

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Coupon created successfully'
            ]);
    }

    public function export(Request $request, CouponService $couponService)
    {
        $filePath = "private/exports/coupons.xlsx";
        $couponService->export($filePath, $request->user());

        return redirect()->back();
    }
}
