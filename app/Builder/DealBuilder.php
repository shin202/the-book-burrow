<?php

namespace App\Builder;

use Illuminate\Database\Eloquent\Builder;

class DealBuilder extends Builder
{

    public function __construct($query)
    {
        parent::__construct($query);
    }

    public function whereBookTitleStartsWith(string $title = null)
    {
        return $this->when($title, function (Builder $query) use ($title) {
            return $query->whereHas('book', function (Builder $query) use ($title) {
                return $query->where('title', 'like', "$title%");
            });
        });
    }

    public function whereBook(int $bookId)
    {
        return $this->where('book_id', $bookId);
    }

    public function currentDeals()
    {
        return $this->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderByDesc('start_date');
    }
}
