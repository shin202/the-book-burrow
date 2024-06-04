<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::factory()
            ->has(OrderStatus::factory(3), 'statuses')
            ->count(10000)
            ->create();

        $orders = Order::all();

        $orderItems = [];

        foreach ($orders as $order) {
            $book = Book::inRandomOrder()->first();
            $orderItems[] = [
                'order_id' => $order->id,
                'book_id' => $book->id,
                'quantity' => rand(1, 20),
                'price' => $book->price,
            ];
        }

        DB::table('order_items')->insert($orderItems);
    }
}
