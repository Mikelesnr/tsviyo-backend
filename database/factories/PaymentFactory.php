<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payment;
use App\Models\Ride;

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
    public function definition()
    {
        return [
            'ride_id' => Ride::factory(),
            'amount' => $this->faker->randomFloat(2, 5, 100),
            'method' => $this->faker->randomElement(['cash', 'card', 'mobile']),
            'transaction_reference' => $this->faker->uuid(),
            'is_confirmed' => true,
        ];
    }
}
