<?php

namespace App\Http\Resources\Publisher;

use App\Http\Resources\BaseCollection;
use Illuminate\Http\Request;

class PublisherCollection extends BaseCollection
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
