<?php

namespace App\Models;

use App\Builder\PublisherBuilder;
use App\Traits\ModelStatusTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publisher extends Model
{
    use HasFactory, SoftDeletes, ModelStatusTrait;

    protected $fillable = [
        'name',
        'description',
        'contact_information',
        'slug',
    ];

    protected $casts = [
        'contact_information' => 'json'
    ];

    public function newEloquentBuilder($query): PublisherBuilder
    {
        return new PublisherBuilder($query);
    }
}
