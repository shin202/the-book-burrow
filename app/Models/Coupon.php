<?php

namespace App\Models;

use App\Builder\CouponBuilder;
use App\Casts\CouponMorphCast;
use App\Enums\CouponStatusEnum;
use App\Enums\DiscountTypeEnum;
use App\Traits\UpdateOnlyColumn;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Coupon extends Model
{
    use HasFactory, UpdateOnlyColumn;

    protected $fillable = [
        'code',
        'value',
        'type',
        'minimum_order_amount',
        'valid_from',
        'valid_to',
        'usage_limit',
        'usage_per_user',
        'is_active',
        'couponable_type',
        'couponable_id',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'type' => DiscountTypeEnum::class,
        'is_active' => 'boolean',
        'couponable_type' => CouponMorphCast::class
    ];

    public function couponable(): MorphTo
    {
        return $this->morphTo();
    }

    public function discount($value)
    {
        return $this->type === DiscountTypeEnum::FIXED ?
            $this->value :
            $value * ($this->value / 100);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_usages', 'coupon_id', 'user_id');
    }

    public function newEloquentBuilder($query): CouponBuilder
    {
        return new CouponBuilder($query);
    }

    public function available(): Attribute
    {
        return Attribute::make(fn() => $this->usage_limit - $this->used_count);
    }

    public function status(): Attribute
    {
        return Attribute::make(function () {
            if (!$this->isAvailable()) {
                return CouponStatusEnum::LIMIT_REACHED;
            }

            if ($this->isExpired()) {
                return CouponStatusEnum::EXPIRED;
            }

            return CouponStatusEnum::AVAILABLE;
        });
    }

    public function isAvailable(): bool
    {
        return $this->available > 0;
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->valid_to);
    }
}
