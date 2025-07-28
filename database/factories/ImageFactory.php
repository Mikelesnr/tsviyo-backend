<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Vehicle;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => $this->faker->imageUrl(640, 480, 'transport', true),
            'type' => $this->faker->randomElement(['profile', 'vehicle']),
            'user_id' => User::inRandomOrder()->first()->id ?? null,
            'vehicle_id' => Vehicle::inRandomOrder()->first()->id ?? null,
        ];
    }
}
