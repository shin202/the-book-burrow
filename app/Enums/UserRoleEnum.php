<?php

namespace App\Enums;

use App\Traits\EnumToArrayTrait;

enum UserRoleEnum: string
{
    use EnumToArrayTrait;

    case BASE_USER = 'base_user';
    case ADMINISTRATOR = 'administrator';
}
