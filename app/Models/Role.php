<?php

namespace App\Models;

use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'pivot'
    ];

    protected $casts = [
        'value' => UserRoleEnum::class,
    ];

    public function scopeBaseUser(Builder $query): Model|Builder
    {
        return $query->where('value', UserRoleEnum::BASE_USER)->firstOrFail();
    }

    public function scopeAdministrator(Builder $query): Model|Builder
    {
        return $query->where('value', UserRoleEnum::ADMINISTRATOR)->firstOrFail();
    }
}
