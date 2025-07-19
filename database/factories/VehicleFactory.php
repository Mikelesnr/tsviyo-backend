<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Vehicle;
use App\Models\Driver;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'driver_id' => Driver::factory(),
            'make' => $this->faker->randomElement(['Toyota', 'Honda', 'Ford']),
            'model' => $this->faker->word(),
            'plate_number' => strtoupper($this->faker->unique()->bothify('??###')),
        ];
    }
}
