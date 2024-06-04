<?php

namespace App\Models;

use App\Builder\AuthorBuilder;
use App\Casts\ImageCast;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'biography',
        'avatar',
        'slug'
    ];

    protected $casts = [
        'avatar' => ImageCast::class
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'author_book', 'author_id', 'book_id')->withTimestamps();
    }

    public function coupons(): MorphMany
    {
        return $this->morphMany(Coupon::class, 'couponable');
    }

    public function newEloquentBuilder($query): AuthorBuilder
    {
        return new AuthorBuilder($query);
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $attributes['first_name'] . ' ' . $attributes['last_name'];
            },
        );
    }
}
