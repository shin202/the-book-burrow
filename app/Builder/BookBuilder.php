<?php

namespace App\Builder;

use App\Enums\StockStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class BookBuilder extends Builder
{
    public function __construct($query)
    {
        parent::__construct($query);
    }

    public function whereTitleContains(string $title = null): self
    {
        return $this->when($title, function ($query) use ($title) {
            return $query->where('title', 'like', "%$title%");
        });
    }

    public function whereSlug(string $slug): self
    {
        return $this->where('slug', $slug);
    }

    public function whereAuthor(string $slug)
    {
        return $this->whereHas('authors', function ($query) use ($slug) {
            $query->where('slug', $slug);
        });
    }

    public function whereAuthorNameContains(string $name = null)
    {
        return $this->when($name, function ($query) use ($name) {
            return $query->whereHas('authors', function ($query) use ($name) {
                $query->where('first_name', 'like', "%$name%")
                    ->orWhere('last_name', 'like', "%$name%");
            });
        });
    }

    public function whereGenre(string $slug)
    {
        return $this->whereHas('genres', function ($query) use ($slug) {
            $query->where('slug', $slug);
        });
    }

    public function wherePublisher(string $slug)
    {
        return $this->whereHas('publisher', function ($query) use ($slug) {
            $query->where('slug', $slug);
        });
    }

    public function wherePublicationDate(string $date)
    {
        return $this->whereDate('publication_date', Carbon::parse($date));
    }

    public function whereRating($rating)
    {
        return $this->join('ratings', 'books.id', '=', 'ratings.book_id')
            ->selectRaw('books.*, AVG(ratings.rating) as average_rating')
            ->groupBy('books.id')
            ->havingRaw('AVG(ratings.rating) >= ?', [$rating]);
    }

    public function whereStatus(string $status)
    {
        return $this->when($status === StockStatusEnum::IN_STOCK->value, function ($query) {
            return $query->where('quantity_in_stock', '>', 10);
        })
            ->when($status === StockStatusEnum::OUT_OF_STOCK->value, function ($query) {
                return $query->where('quantity_in_stock', 0);
            })
            ->when($status === StockStatusEnum::LOW_STOCK->value, function ($query) {
                return $query->where('quantity_in_stock', '<=', 10);
            });
    }

    public function bestSellerBy(string $unit = null)
    {
        return $this->join('order_items', 'books.id', '=', 'order_items.book_id')
            ->when(isset($unit), function (Builder $query) use ($unit) {
                $startOf = 'startOf' . ucfirst($unit);
                $endOf = 'endOf' . ucfirst($unit);
                return $query->whereBetween('order_items.created_at', [now()->$startOf(), now()->$endOf()]);
            })
            ->selectRaw('SUM(order_items.quantity) as total_sold, SUM(order_items.price * order_items.quantity) as total_revenue')
            ->groupBy('books.id')
            ->orderByDesc('total_sold');
    }

    /**
     * Get all books that have a discount
     *
     * @return self
     */
    public function discounts(): self
    {
        return $this
            ->whereHas('currentDeal');
    }

    /**
     * Get books that recently published
     *
     * @return self
     */
    public function recently(): self
    {
        return $this->orderByDesc('publication_date');
    }
}
