<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RidePassenger extends Model
{
    protected $fillable = ['ride_id', 'user_id', 'checked_in'];

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
