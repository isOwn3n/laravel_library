<?php

namespace Database\Factories;

use App\Models\MembershipPlan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = ['admin', 'librarian', 'member'];
        return [
            'firstname' => fake()->firstName(),
            'lastname' => fake()->firstName(),
            'username' => fake()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => $roles[array_rand($roles)],
            'membership_plan_id' => MembershipPlan::all()->random()->id,
            'plan_expires_at' => now()->addYear()
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
