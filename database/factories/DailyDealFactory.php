<?php

namespace Database\Factories;

use App\Enums\DiscountTypeEnum;
use App\Models\DailyDeal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyDeal>
 */
class DailyDealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'discount_type' => $this->faker->randomElement(DiscountTypeEnum::values()),
            'discount_value' => $this->faker->numberBetween(1, 100),
            'start_date' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'end_date' => $this->faker->dateTimeBetween('+1 week', '+2 week'),
        ];
    }
}
