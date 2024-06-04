<?php

namespace Database\Seeders;

use App\Enums\UserGenderEnum;
use App\Models\Profile;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        $customers = User::query()->customers()->pluck('id')->toArray();

        $profiles = [];

        foreach ($customers as $customer) {
            $profiles[] = [
                'user_id' => $customer,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'gender' => $faker->randomElement(UserGenderEnum::values()),
                'date_of_birth' => $faker->date('Y-m-d', '2007-12-31'),
                'country' => $faker->country,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        collect($profiles)->chunk(1000)->each(fn($chunk) => Profile::insert($chunk->toArray()));
    }
}
