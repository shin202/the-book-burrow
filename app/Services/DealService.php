<?php

namespace App\Services;

use App\Http\Resources\Deal\DealCollection;
use App\Models\DailyDeal;
use Carbon\Carbon;
use Exception;
use Throwable;

class DealService
{
    public function __construct(protected DailyDeal $dailyDeal)
    {

    }

    public function paginate(string $search = null, int $perPage = 5)
    {
        $deals = $this->dailyDeal
            ->newQuery()
            ->with([
                'book' => function ($query) {
                    $query->select(['id', 'title', 'cover_image', 'slug']);
                }
            ])
            ->whereBookTitleStartsWith($search)
            ->paginate($perPage);
        return DealCollection::make($deals);
    }

    /**
     * @throws Throwable
     */
    public function store(array $data)
    {
        $overlap = $this->dailyDeal
            ->whereBook($data['book_id'])
            ->where('start_date', '<=', $data['end_date'])
            ->where('end_date', '>=', $data['start_date'])
            ->count();

        throw_if($overlap > 0, new Exception('This deal of the book is overlapping with another deal with the give date range', 422));

        return $this->dailyDeal->create($data);
    }

    /**
     * @throws Throwable
     */
    public function update(int $id, array $data)
    {
        $overlap = $this->dailyDeal
            ->where('id', '!=', $id)
            ->whereBook($data['book_id'])
            ->where('start_date', '<=', Carbon::parse($data['end_date']))
            ->where('end_date', '>=', Carbon::parse($data['start_date']))
            ->count();

        throw_if($overlap > 0, new Exception('This deal of the book is overlapping with another deal with the give date range', 422));

        $deal = $this->findById($id);
        $deal->updateOnly($data);

        return $deal;
    }

    public function findById(int $id)
    {
        return $this->dailyDeal->findOrFail($id);
    }

    public function destroy(mixed $id)
    {
        $id = explode(',', $id);
        return $this->dailyDeal->whereIn('id', $id)->delete();
    }
}
