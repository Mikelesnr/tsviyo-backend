<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // UserSeeder::class,         // Create base users with roles
            // RiderSeeder::class,        // Generate rider profiles linked to rider-role users
            // DriverSeeder::class,       // Create driver profiles linked to driver-role users
            // VehicleSeeder::class,      // Assign vehicles to drivers
            // RideSeeder::class,         // Generate rides with driver_id and rider_id
            // PaymentSeeder::class,      // Create payments for rides
            // RatingSeeder::class,       // Add mutual ratings between riders and drivers
            ImageSeeder::class,
        ]);
    }
}
