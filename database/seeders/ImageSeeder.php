<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Image;
use App\Models\Vehicle;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed profile images for all users
        User::all()->each(function ($user) {
            Image::create([
                'url' => fake()->imageUrl(640, 480, 'people', true),
                'type' => 'profile',
                'user_id' => $user->id,
                'vehicle_id' => null,
            ]);
        });

        // Seed vehicle images for all vehicles
        Vehicle::all()->each(function ($vehicle) {
            Image::create([
                'url' => fake()->imageUrl(640, 480, 'transport', true),
                'type' => 'vehicle',
                'user_id' => null,
                'vehicle_id' => $vehicle->id,
            ]);
        });
    }
}
