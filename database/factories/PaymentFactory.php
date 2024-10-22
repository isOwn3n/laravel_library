<?php

namespace Database\Factories;

use App\Models\MembershipPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'completed', 'failed'];
        return [
            'user_id' => 2,
            'amount' => MembershipPlan::all()->random()->price,
            'transaction_id' => $this->faker->uuid(),
            'status' => $statuses[array_rand($statuses)],
        ];
    }
}
