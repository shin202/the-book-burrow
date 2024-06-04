<?php

namespace App\Enums;

use App\Traits\EnumToArrayTrait;

enum PaymentStatusEnum: string
{
    use EnumToArrayTrait;

    case PENDING = 'pending';

    case PAID = 'paid';

    case FAILED = 'failed';
}
