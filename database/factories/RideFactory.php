<?php

namespace Database\Factories;

use App\Models\Ride;
use App\Models\Driver;
use App\Models\Rider;
use Illuminate\Database\Eloquent\Factories\Factory;

class RideFactory extends Factory
{
    protected $model = Ride::class;

    public function definition(): array
    {
        return [
            'driver_id' => Driver::inRandomOrder()->first()?->id,
            'rider_id' => Rider::inRandomOrder()->first()?->id,
            'pickup_lat' => $this->faker->latitude(),
            'pickup_lng' => $this->faker->longitude(),
            'dropoff_lat' => $this->faker->latitude(),
            'dropoff_lng' => $this->faker->longitude(),
            'status' => $this->faker->randomElement(['requested', 'accepted', 'in_progress', 'completed', 'canceled']),
            'has_paid' => $this->faker->boolean(),
        ];
    }
}
