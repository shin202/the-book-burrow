<?php

namespace Database\Factories;

use App\Enums\BannerStatusEnum;
use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'image' => $this->faker->imageUrl,
            'link' => $this->faker->url,
            'status' => $this->faker->randomElement(BannerStatusEnum::values()),
        ];
    }
}
