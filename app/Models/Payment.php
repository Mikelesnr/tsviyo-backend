<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Ride;
use App\Enums\PaymentMethod;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'amount',
        'method',
    ];

    protected $casts = [
        'method' => PaymentMethod::class,
    ];

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }
}
