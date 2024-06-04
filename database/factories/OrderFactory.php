<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => $this->faker->unique()->randomNumber(8),
            'user_id' => User::all()->random()->id,
            'billing_name' => $this->faker->name,
            'billing_email' => $this->faker->unique()->safeEmail,
            'billing_phone' => $this->faker->phoneNumber,
            'billing_address' => $this->faker->address,
            'billing_city' => $this->faker->city,
            'billing_state' => $this->faker->streetAddress,
            'billing_country' => $this->faker->country,
            'billing_zip' => $this->faker->postcode,
            'billing_subtotal' => $this->faker->randomFloat(2, 0, 1000),
            'billing_total' => $this->faker->randomFloat(2, 0, 1000),
            'total_profit' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
