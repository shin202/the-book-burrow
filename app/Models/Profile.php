<?php

namespace App\Models;

use App\Enums\UserGenderEnum;
use App\Traits\UpdateOnlyColumn;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes, UpdateOnlyColumn;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'country',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date:m/d/Y',
        'gender' => UserGenderEnum::class,
    ];

    protected function age(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $diff = now()->diffInYears($attributes['date_of_birth']);
                return max($diff, 0);
            },
        );
    }
}
