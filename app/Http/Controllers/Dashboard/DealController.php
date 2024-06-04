<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Deal\StoreDealRequest;
use App\Http\Requests\Deal\UpdateDealRequest;
use App\Http\Resources\Deal\DealShowResource;
use App\Services\BookService;
use App\Services\DealService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DealService $dealService, BookService $bookService)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);
        $deals = $dealService->paginate($search, $perPage);
        $books = $bookService->list();

        return Inertia::render('Dashboard/Deal/Index', [
            'deals' => $deals,
            'books' => $books,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(BookService $bookService)
    {
        $books = $bookService->list();

        return Inertia::render('Dashboard/Deal/Creupdate', [
            'formType' => 'create',
            'books' => $books,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDealRequest $request, DealService $dealService)
    {
        try {
            $dealService->store($request->validated());
        } catch (Throwable $ex) {
            return redirect()
                ->back()
                ->withErrors(['message' => $ex->getMessage()]);
        }

        return redirect()
            ->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Deal created successfully',
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id, DealService $dealService, BookService $bookService)
    {
        $deal = $dealService->findById($id);
        $books = $bookService->list();

        return Inertia::render('Dashboard/Deal/Creupdate', [
            'formType' => 'update',
            'deal' => DealShowResource::make($deal),
            'books' => $books,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDealRequest $request, int $id, DealService $dealService)
    {
        try {
            $dealService->update($id, $request->validated());
        } catch (Throwable $ex) {
            return redirect()
                ->back()
                ->withErrors(['message' => $ex->getMessage()]);
        }

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Deal updated successfully',
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(mixed $ids, DealService $dealService)
    {
        $dealService->destroy($ids);

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Deal deleted successfully',
            ]);
    }
}
