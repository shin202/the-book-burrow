<?php

namespace Database\Seeders;

use App\Enums\AccountStatusEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'username' => 'dellavik',
            'email' => 'dellavik@bookburrow.dev',
            'password' => Hash::make('Delvik@007'),
            'status' => AccountStatusEnum::ACTIVE,
            'email_verified_at' => now(),
        ]);

        $role = Role::administrator();
        $user->roles()->attach($role);

        User::factory()
            ->count(1000)
            ->create();
    }
}
