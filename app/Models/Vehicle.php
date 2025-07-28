<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'make',
        'model',
        'plate_number',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Vehicle image.
     */
    public function vehicleImage()
    {
        return $this->hasOne(Image::class);
    }
}
