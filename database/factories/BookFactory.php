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
            'title' => fake()->words(2, true),
            'author' => fake()->titleMale(),
            'isbn' => fake()->isbn13(),
            'category_id' => 1,
            'quantity' => 5,
            'available_quantity' => 5,
            'description' => fake()->sentences(5, true)
        ];
    }
}
