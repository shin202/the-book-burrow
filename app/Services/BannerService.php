<?php

namespace App\Services;

use App\Enums\BannerStatusEnum;
use App\Http\Resources\Banner\BannerCollection;
use App\Http\Resources\Banner\BannerShowResource;
use App\Models\Banner;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class BannerService
{
    public function __construct(protected Banner $banner)
    {
    }

    public function allActiveBanners(): AnonymousResourceCollection
    {
        $banners = $this->banner->whereStatus(BannerStatusEnum::ACTIVE)->get();
        return BannerShowResource::collection($banners);
    }

    public function searchWithPaginate(string $search = null, int $perPage = 5)
    {
        $banners = $this->banner->whereTitleStartsWith($search)->paginate($perPage);
        return BannerCollection::make($banners);
    }

    public function store(array $data)
    {
        $data['image'] = Storage::put('uploads/banners', $data['image']);
        return $this->banner->create($data);
    }

    public function update(int $id, array $data)
    {
        $banner = $this->findById($id);

        if (isset($data['image'])) {
            Storage::delete($banner->image);
            $data['image'] = Storage::put('uploads/banners', $data['image']);
        }

        $banner->updateOnly($data);
        return $banner;
    }

    public function findById(int $id)
    {
        return $this->banner->findOrFail($id);
    }

    public function destroy(mixed $id): void
    {
        $id = explode(',', $id);
        $this->banner->whereIn('id', $id)->delete();
    }
}
