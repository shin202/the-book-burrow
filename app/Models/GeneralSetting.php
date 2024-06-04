<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Traits\EnumeratesValues;

class GeneralSetting extends Model
{
    public $timestamps = false;

    public static function refreshConfig(): EnumeratesValues|Collection
    {
        Cache::forget('general_settings');
        Cache::rememberForever('general_settings', fn() => static::query()->get()->toBase());

        return self::bootConfig();
    }

    public static function bootConfig(): EnumeratesValues|Collection|null
    {
        $settings = Cache::get('general_settings');

        if (isset($settings)) {
            collect($settings)->each(fn($setting) => config(["general_settings.{$setting->key}" => $setting->value]));
        }

        return $settings;
    }
}
