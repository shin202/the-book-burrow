<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        $reviews = [];

        for ($i = 0; $i < 100000; $i++) {
            $reviews[] = [
                'user_id' => User::customers()->get()->random()->id,
                'book_id' => Book::all()->random()->id,
                'review_text' => $faker->text(150),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        collect($reviews)->chunk(1000)->each(fn($chunk) => Review::insert($chunk->toArray()));
    }
}
