<?php

namespace App\Enums;

use App\Traits\EnumToArrayTrait;

enum BannerStatusEnum: string
{
    use EnumToArrayTrait;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
