<?php

namespace App\Models;

use App\Builder\BannerBuilder;
use App\Enums\BannerStatusEnum;
use App\Traits\UpdateOnlyColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory, UpdateOnlyColumn;

    protected $fillable = [
        'title',
        'description',
        'image',
        'link',
        'status'
    ];

    protected $casts = [
        'status' => BannerStatusEnum::class,
    ];

    public function newEloquentBuilder($query): BannerBuilder
    {
        return new BannerBuilder($query);
    }
}
