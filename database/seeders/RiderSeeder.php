<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rider;

class RiderSeeder extends Seeder
{
    public function run(): void
    {
        Rider::factory()->count(10)->create();
    }
}
