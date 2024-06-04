<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'pagination' => [
                'currentPage' => $this->currentPage(),
                'perPage' => $this->perPage(),
                'total' => $this->total(),
                'lastPage' => $this->lastPage(),
                'count' => $this->count(),
            ],
        ];
    }
}
