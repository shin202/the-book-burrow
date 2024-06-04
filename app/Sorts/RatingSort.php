<?php

namespace App\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class RatingSort implements Sort
{

    public function __invoke(Builder $query, bool $descending, string $property): void
    {
        $direction = $descending ? 'desc' : 'asc';

        $query->withAvg('ratings as average_rating', 'rating')
            ->orderBy('average_rating', $direction);
    }
}
