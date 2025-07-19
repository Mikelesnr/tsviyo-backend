<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\Driver;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Attach 1–2 vehicles to each existing driver
        Driver::all()->each(function ($driver) {
            Vehicle::factory()
                ->count(rand(1, 2))
                ->state([
                    'driver_id' => $driver->id,
                ])
                ->create();
        });
    }
}
