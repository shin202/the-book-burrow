<?php

namespace App\Console\Commands;

use App\Models\GeneralSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Command\Command as CommandAlias;

class BootGeneralSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:boot-general-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Boot general settings from file.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $settings = Arr::dot(config('general_settings'));
        $settings = array_map(fn($value, $key) => ['key' => $key, 'value' => $value], $settings, array_keys($settings));

        GeneralSetting::query()->upsert($settings, ['key'], ['value']);
        GeneralSetting::refreshConfig();

        $this->info('General settings booted successfully.');

        return CommandAlias::SUCCESS;
    }
}
