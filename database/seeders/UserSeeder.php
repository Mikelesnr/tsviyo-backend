<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Specific named users
        User::factory()->state([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '0771234567',
            'role' => 'admin',
        ])->create();

        User::factory()->state([
            'name' => 'Driver User',
            'email' => 'driver@example.com',
            'phone' => '0779876543',
            'role' => 'driver',
        ])->create();

        User::factory()->state([
            'name' => 'Rider User',
            'email' => 'rider@example.com',
            'phone' => '0775678901',
            'role' => 'rider',
        ])->create();

        // Additional randomized users
        User::factory()->count(10)->create();
    }
}
