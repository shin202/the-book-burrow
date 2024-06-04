<?php

namespace App\Services;

use App\Exports\CouponExport;
use App\Imports\Coupon\CouponImport;
use App\Jobs\ExportJob;
use App\Jobs\ImportJob;
use App\Jobs\NotifyUserOfCompletedExportJob;
use App\Jobs\NotifyUserOfCompletedImportJob;
use App\Models\Author;
use App\Models\Book;
use App\Models\Coupon;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CouponService
{
    public function __construct(protected Coupon $coupon)
    {

    }

    public function dashboardIndex(string $search = null, int $perPage = 5)
    {
        return QueryBuilder::for(Coupon::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::beginsWithStrict('code'),
                AllowedFilter::exact('type'),
                AllowedFilter::exact('is_active'),
                AllowedFilter::scope('valid_from', 'whereValidFrom'),
                AllowedFilter::scope('valid_to', 'whereValidTo'),
                AllowedFilter::scope('value', 'whereValueGreaterThanOrEquals'),
                AllowedFilter::scope('status', 'whereStatus'),
            ])->whereCodeStartsWith($search)
            ->paginate($perPage);
    }

    public function availables()
    {
        return $this->coupon
            ->newQuery()
            ->whereActive()
            ->whereBookIdIn(CartService::instance()->items()->pluck('productId')->toArray())
            ->get();
    }

    public function store(array $data)
    {
        return $this->coupon->create([
            ...$data,
            'couponable_type' => $this->getCouponable($data['couponable_type']),
        ]);
    }

    private function getCouponable(string $value): string
    {
        $couponables = [
            'user' => User::class,
            'book' => Book::class,
            'author' => Author::class,
            'genre' => Genre::class
        ];

        return $couponables[$value];
    }

    public function update(string $code, array $data)
    {
        $coupon = $this->findByCode($code);

        $coupon->updateOnly([
            ...$data,
            'couponable_type' => $this->getCouponable($data['couponable_type']),
        ]);

        return $coupon;
    }

    public function findByCode(string $code)
    {
        return $this->coupon->whereCode($code)->firstOrFail();
    }

    public function deleteByCode(mixed $code): void
    {
        $code = explode(',', $code);
        $this->coupon->whereIn('code', $code)->delete();
    }

    public function import(string $filePath, User $user): void
    {
        Bus::chain([
            new ImportJob(new CouponImport(), $filePath),
            new NotifyUserOfCompletedImportJob($user, $filePath)
        ])->dispatch();
    }

    public function export(string $filePath, User $user): void
    {
        Bus::chain([
            new ExportJob(new CouponExport(), $filePath),
            new NotifyUserOfCompletedExportJob($user, $filePath)
        ])->dispatch();
    }
}
