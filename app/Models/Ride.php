<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    protected $fillable = [
        'driver_id',
        'pickup_lat',
        'pickup_lng',
        'dropoff_lat',
        'dropoff_lng',
        'status',
        'has_paid',
    ];

    protected $casts = [
        'has_paid' => 'boolean',
    ];

    // Optional driver until ride is accepted
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // Many passengers via pivot
    public function passengers()
    {
        return $this->belongsToMany(User::class, 'ride_passengers')
            ->withTimestamps()
            ->withPivot('checked_in');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
