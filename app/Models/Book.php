<?php

namespace App\Models;

use App\Builder\BookBuilder;
use App\Casts\ImageCast;
use App\Enums\StockStatusEnum;
use App\Traits\UpdateOnlyColumn;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes, UpdateOnlyColumn;

    protected $fillable = [
        'title',
        'isbn',
        'description',
        'cover_image',
        'number_of_pages',
        'publisher_id',
        'publication_date',
        'quantity_in_stock',
        'price',
        'slug'
    ];

    protected $casts = [
        'isbn' => 'string',
        'publication_date' => 'date:Y-m-d',
        'price' => 'double',
        'cover_image' => ImageCast::class
    ];

    /**
     * Get the publisher for the book.
     *
     * @return BelongsTo
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    /**
     * Get the authors associated with the book.
     *
     * @return BelongsToMany
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'author_book', 'book_id', 'author_id')->withTimestamps();
    }

    /**
     * Get the genres associated with the book.
     *
     * @return BelongsToMany
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'book_genre', 'book_id', 'genre_id')->withTimestamps();
    }

    public function coupons(): MorphMany
    {
        return $this->morphMany(Coupon::class, 'couponable');
    }

    public function newEloquentBuilder($query): BookBuilder
    {
        return new BookBuilder($query);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(DailyDeal::class);
    }

    public function isOutOfStock()
    {
        return $this->quantity_in_stock === 0;
    }

    public function costs(): HasMany
    {
        return $this->hasMany(Cost::class);
    }

    public function currentDeal(): HasOne
    {
        return $this->hasOne(DailyDeal::class)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot(['quantity', 'price', 'discount', 'options'])
            ->withTimestamps();
    }

    public function latestCost(): HasOne
    {
        return $this->hasOne(Cost::class)
            ->where('effective_date', '<=', now())
            ->orderByDesc('effective_date');
    }

    protected function stockStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->quantity_in_stock === 0 ? StockStatusEnum::OUT_OF_STOCK : ($this->quantity_in_stock < 10 ? StockStatusEnum::LOW_STOCK : StockStatusEnum::IN_STOCK);
            }
        );
    }

    protected function profit(): Attribute
    {
        $cost = $this->latestCost->cost ?? 0;

        return Attribute::make(
            get: function (mixed $value, array $attributes) use ($cost) {
                return round($attributes['price'] - $cost, 2);
            },
        );
    }

    protected function discountPrice(): Attribute
    {
        $deal = $this->currentDeal;

        return Attribute::make(
            get: function (mixed $value, array $attributes) use ($deal) {
                if (!$deal) {
                    return null;
                }

                return round($attributes['price'] - $deal->discount($attributes['price']), 2);
            },
        );
    }

    protected function ratingGroupCount(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->ratings()
                    ->selectRaw('rating, count(*) as count')
                    ->groupBy('rating')
                    ->orderBy('rating')
                    ->get();
            }
        );
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
