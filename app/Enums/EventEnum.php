<?php

namespace App\Enums;

use App\Traits\EnumToArrayTrait;

enum EventEnum: string
{
    use EnumToArrayTrait;

    case EXPORT_COMPLETED = 'export.completed';
    case IMPORT_COMPLETED = 'import.completed';
}
