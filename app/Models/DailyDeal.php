<?php

namespace App\Models;

use App\Builder\DealBuilder;
use App\Enums\DiscountTypeEnum;
use App\Traits\UpdateOnlyColumn;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use NumberFormatter;

class DailyDeal extends Model
{
    use HasFactory, UpdateOnlyColumn;

    protected $fillable = [
        'book_id',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'discount_type' => DiscountTypeEnum::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function newEloquentBuilder($query): DealBuilder
    {
        return new DealBuilder($query);
    }

    public function discount(float|int $price)
    {
        return $this->discount_type === DiscountTypeEnum::PERCENTAGE ?
            ($this->discount_value / 100) * $price :
            $this->discount_value;
    }

    protected function discountValueFmt(): Attribute
    {
        $fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);

        return Attribute::make(
            get: function (mixed $value, array $attributes) use ($fmt) {
                return $attributes['discount_type'] === DiscountTypeEnum::PERCENTAGE->value ?
                    $attributes['discount_value'] . '%' : $fmt->formatCurrency($attributes['discount_value'], 'USD');
            },
        );
    }
}
