<?php

namespace App\Builder;

use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class CouponBuilder extends Builder
{
    public function __construct($query)
    {
        parent::__construct($query);
    }

    public function whereCodeStartsWith(string $code = null)
    {
        return $this->when($code, function ($query) use ($code) {
            return $query->where('code', 'like', "$code%");
        });
    }

    public function whereCode(string $code)
    {
        return $this->where('code', $code);
    }

    public function whereBookIdIn(array $bookId)
    {
        return $this->whereIn('couponable_id', $bookId)
            ->where('couponable_type', Book::class);
    }

    public function whereActive()
    {
        return $this->where('is_active', true);
    }

    public function whereValueGreaterThanOrEquals(int $value): self
    {
        return $this->where('value', '>=', $value);
    }

    public function whereValidFrom(string $date): self
    {
        return $this->where('valid_from', '>=', Carbon::parse($date));
    }

    public function whereValidTo(string $date): self
    {
        return $this->where('valid_to', '<=', Carbon::parse($date));
    }

    public function whereStatus($status): self
    {
        return $this->where('is_active', $status);
    }
}
