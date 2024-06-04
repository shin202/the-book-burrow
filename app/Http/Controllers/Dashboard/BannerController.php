<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\StoreBannerRequest;
use App\Http\Requests\Banner\UpdateBannerRequest;
use App\Http\Resources\Banner\BannerShowResource;
use App\Services\BannerService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, BannerService $bannerService)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);
        $banners = $bannerService->searchWithPaginate($search, $perPage);

        return Inertia::render('Dashboard/Banner/Index', [
            'banners' => $banners
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Dashboard/Banner/Creupdate', [
            'formType' => 'create'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBannerRequest $request, BannerService $bannerService)
    {
        $bannerService->store($request->validated());

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Banner created successfully'
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
    public function edit(int $id, BannerService $bannerService)
    {
        $banner = $bannerService->findById($id);

        return Inertia::render('Dashboard/Banner/Creupdate', [
            'formType' => 'update',
            'banner' => BannerShowResource::make($banner)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBannerRequest $request, int $id, BannerService $bannerService)
    {
        $banner = $bannerService->update($id, $request->validated());

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Banner updated successfully'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(mixed $ids, BannerService $bannerService)
    {
        $bannerService->destroy($ids);

        return redirect()->back()
            ->with([
                'toast.severity' => 'success',
                'toast.message' => 'Banner deleted successfully'
            ]);
    }
}
