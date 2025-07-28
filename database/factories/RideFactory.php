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
        $locations = [
            [
                'pickup_add' => 'Food Lovers Market Greendale, Harare',
                'pickup_lat' => -17.797830,
                'pickup_lng' => 31.125870,
                'dropoff_add' => 'Pick n Pay Kamfinsa, Harare',
                'dropoff_lat' => -17.814722,
                'dropoff_lng' => 31.144722,
            ],
            [
                'pickup_add' => 'Pick n Pay Kamfinsa, Harare',
                'pickup_lat' => -17.814722,
                'pickup_lng' => 31.144722,
                'dropoff_add' => '3 Alexander Road, Highlands, Harare',
                'dropoff_lat' => -17.785440,
                'dropoff_lng' => 31.110050,
            ],
            [
                'pickup_add' => '3 Alexander Road, Highlands, Harare',
                'pickup_lat' => -17.785440,
                'pickup_lng' => 31.110050,
                'dropoff_add' => 'Food Lovers Market Greendale, Harare',
                'dropoff_lat' => -17.797830,
                'dropoff_lng' => 31.125870,
            ]
        ];

        $location = $this->faker->randomElement($locations);

        return [
            'driver_id' => Driver::inRandomOrder()->first()?->id,
            'rider_id' => Rider::inRandomOrder()->first()?->id,
            'pickup_lat' => $location['pickup_lat'],
            'pickup_lng' => $location['pickup_lng'],
            'dropoff_lat' => $location['dropoff_lat'],
            'dropoff_lng' => $location['dropoff_lng'],
            'pickup_add' => $location['pickup_add'],
            'dropoff_add' => $location['dropoff_add'],
            'pickup_time' => $this->faker->dateTimeBetween('-1 week', '+1 day'),
            'fare' => $this->faker->randomFloat(2, 3, 20),
            'status' => $this->faker->randomElement(['requested', 'accepted', 'in_progress', 'completed', 'canceled']),
        ];
    }
}
