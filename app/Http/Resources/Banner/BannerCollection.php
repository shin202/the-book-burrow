<?php

namespace App\Http\Resources\Banner;

use App\Http\Resources\BaseCollection;
use Illuminate\Http\Request;

class BannerCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
