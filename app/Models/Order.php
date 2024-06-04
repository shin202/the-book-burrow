<?php

namespace App\Models;

use App\Builder\OrderBuilder;
use App\Enums\OrderStatusEnum;
use App\Events\OrderCreated;
use App\Traits\UpdateOnlyColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory, UpdateOnlyColumn;

    protected $fillable = [
        'order_number',
        'user_id',
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_country',
        'billing_zip',
        'billing_discount_code',
        'billing_discount',
        'billing_subtotal',
        'billing_total',
        'total_profit',
        'status',
        'payment_method'
    ];

    protected $casts = [
        'billing_discount' => 'double',
        'billing_subtotal' => 'double',
        'billing_total' => 'double',
        'total_profit' => 'double',
        'status' => OrderStatusEnum::class,
        'created_at' => 'datetime',
    ];
    protected $dispatchesEvents = [
        'created' => OrderCreated::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'order_items')
            ->withPivot(['quantity', 'price', 'discount', 'options'])
            ->withTimestamps();
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(OrderStatus::class)->orderByDesc('created_at');
    }

    public function currentStatus(): HasOne
    {
        return $this->hasOne(OrderStatus::class)
            ->latestOfMany('created_at');
    }

    public function newEloquentBuilder($query): OrderBuilder
    {
        return new OrderBuilder($query);
    }
}
