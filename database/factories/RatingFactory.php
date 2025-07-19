<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rating;
use App\Models\Ride;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
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
            'reviewer_id' => User::factory(),
            'reviewee_id' => User::factory(),
            'stars' => $this->faker->numberBetween(1, 5),
        ];
    }
}
