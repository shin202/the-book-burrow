<?php

namespace App\Enums;

use App\Traits\EnumToArrayTrait;

enum UserGenderEnum: string
{
    use EnumToArrayTrait;

    case M = 'male';
    case F = 'female';
}
