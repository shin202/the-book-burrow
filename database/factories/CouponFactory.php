<?php

namespace Database\Factories;

use App\Enums\DiscountTypeEnum;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->word,
            'type' => $this->faker->randomElement(DiscountTypeEnum::values()),
            'value' => $this->faker->randomFloat(2, 0, 100),
            'minimum_order_amount' => $this->faker->randomFloat(2, 0, 100),
            'usage_limit' => $this->faker->randomNumber(2),
            'usage_per_user' => $this->faker->randomNumber(2),
            'valid_from' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'valid_to' => $this->faker->dateTimeBetween('now', '+1 month'),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
