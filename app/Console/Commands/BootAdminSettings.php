<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class BootAdminSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:boot-admin-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Boot admin settings from the file.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $settings = config('admin_settings');

        User::administrators()->update(['settings' => json_encode($settings)]);

        $this->info('Admin settings booted successfully.');

        return CommandAlias::SUCCESS;
    }
}
