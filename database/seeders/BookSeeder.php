<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Coupon;
use App\Models\DailyDeal;
use App\Models\Genre;
use App\Models\Publisher;
use App\Models\Rating;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::factory()->has(Author::factory()->count(3))
            ->has(Genre::factory()->count(1))
            ->for(Publisher::factory()->create())
            ->has(DailyDeal::factory()->count(5), 'deals')
            ->has(Coupon::factory()->count(5), 'coupons')
            ->has(Rating::factory()->count(10))
            ->count(10)
            ->create();
    }
}
