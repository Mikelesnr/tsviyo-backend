<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\RideStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ride extends Model
{
    use HasFactory;
    protected $fillable = [
        'driver_id',
        'rider_id',
        'pickup_lat',
        'pickup_lng',
        'dropoff_lat',
        'dropoff_lng',
        'status',
        'has_paid',
    ];

    protected $casts = [
        'has_paid' => 'boolean',
        'status' => RideStatus::class,
    ];

    /**
     * The driver handling the ride.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * The rider/passenger requesting the ride.
     */
    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    /**
     * Payment attached to the ride.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Ratings tied to this ride — submitted by either rider or driver.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
