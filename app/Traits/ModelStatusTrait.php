<?php

namespace App\Traits;

use App\Enums\ModelStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait ModelStatusTrait
{
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return is_null($attributes['deleted_at']) ? ModelStatusEnum::AVAILABLE : ModelStatusEnum::DELETED;
            },
        );
    }
}
