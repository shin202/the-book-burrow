<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [];
        $randomUsers = User::whereNotIn('id', $users)->inRandomOrder()->limit(150)->get();
        $users = array_merge($users, $randomUsers->pluck('id')->toArray());

        $books = [];
        $randomBooks = Book::whereNotIn('id', $books)->inRandomOrder()->get();
        $books = array_merge($books, $randomBooks->pluck('id')->toArray());

        $arr = [];

        foreach ($books as $book) {
            foreach ($users as $user) {
                $arr[] = [
                    'user_id' => $user,
                    'book_id' => $book,
                    'rating' => rand(2, 5),
                ];
            }
        }

        collect($arr)->chunk(1000)->each(fn($chunk) => Rating::insert($chunk->toArray()));
    }
}
