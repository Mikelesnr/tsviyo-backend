<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Ride;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'amount',
        'method',
    ];

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }
}
