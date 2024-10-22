<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'fulfilled', 'cancelled'];
        return [
            'user_id' => User::all()->random()->id,
            'book_id' => Book::all()->random()->id,
            'reserved_at' => $this->faker->date(),
            'expires_at' => $this->faker->date(),
            'status' => $statuses[array_rand($statuses)],
        ];
    }
}
