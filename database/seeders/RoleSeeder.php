<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleKeys = UserRoleEnum::names();
        $roleValues = UserRoleEnum::values();

        $roles = array_map(function ($key, $value) {
            return [
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $roleKeys, $roleValues);

        DB::table('roles')->insert($roles);
    }
}
