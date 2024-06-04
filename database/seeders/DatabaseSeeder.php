<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            GeneralSettingSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            BookSeeder::class,
            BannerSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
