<?php

namespace App\Enums;

use App\Traits\EnumToArrayTrait;

enum PaymentMethodEnum: string
{
    use EnumToArrayTrait;

    case CASH = 'cash';

    case STRIPE = 'stripe';
}
