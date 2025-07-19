<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ride;
use App\Models\Rider;
use App\Models\Driver;

class RideSeeder extends Seeder
{
    public function run(): void
    {
        $riders = Rider::all();
        $drivers = Driver::all();

        // Create 10 rides, each linked to one rider and an optional driver
        Ride::factory()->count(10)->create()->each(function ($ride) use ($riders, $drivers) {
            $ride->update([
                'rider_id' => $riders->random()->id,
                'driver_id' => $drivers->random()?->id, // Allow null if needed
            ]);
        });
    }
}
