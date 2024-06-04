<?php

namespace App\Services;

use App\Console\Commands\BootGeneralSettings;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    public function __construct(protected GeneralSetting $generalSetting)
    {
    }

    public function updateGeneralSetting(string $key, mixed $data): void
    {
        if ($key === 'site.logo') {
            $data = Storage::putFileAs('uploads', $data, 'logo.png');
        }

        config(['general_settings.' . $key => $data]);

        Artisan::call(BootGeneralSettings::class);
    }
}
