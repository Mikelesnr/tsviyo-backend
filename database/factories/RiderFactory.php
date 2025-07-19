<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Rider;
use App\Models\User;

class RiderFactory extends Factory
{
    protected $model = Rider::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'rider']),
            'home_address' => $this->faker->address(),
        ];
    }
}
