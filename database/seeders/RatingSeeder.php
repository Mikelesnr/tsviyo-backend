<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rating;
use App\Models\Ride;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        // Load related profiles for each ride
        $rides = Ride::with(['rider', 'driver'])->get();

        foreach ($rides as $ride) {
            $riderUserId = $ride->rider?->user_id;
            $driverUserId = $ride->driver?->user_id;

            // Ensure both profiles are present before rating
            if ($riderUserId && $driverUserId) {
                // Rider rates driver
                Rating::factory()->create([
                    'ride_id' => $ride->id,
                    'reviewer_id' => $riderUserId,
                    'reviewee_id' => $driverUserId,
                ]);

                // Driver rates rider
                Rating::factory()->create([
                    'ride_id' => $ride->id,
                    'reviewer_id' => $driverUserId,
                    'reviewee_id' => $riderUserId,
                ]);
            }
        }
    }
}
