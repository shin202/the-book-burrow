<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->sentence,
            'isbn' => $this->faker->unique()->isbn13,
            'description' => $this->faker->paragraph,
            'cover_image' => $this->faker->imageUrl,
            'number_of_pages' => $this->faker->numberBetween(100, 1000),
            'publication_date' => $this->faker->date,
            'quantity_in_stock' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'slug' => $this->faker->unique()->slug,
        ];
    }
}
