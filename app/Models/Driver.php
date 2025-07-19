<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'is_activated',
        'is_online',
        'is_suspended',
    ];

    protected $casts = [
        'is_activated' => 'boolean',
        'is_online' => 'boolean',
        'is_suspended' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
